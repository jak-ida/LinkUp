<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Agenda;
use App\Notifications\SendEmailNotification;
use GuzzleHttp\Psr7\FnStream;
use Illuminate\Support\Facades\Redis;
use Mews\Purifier\Facades\Purifier;
use League\CommonMark\CommonMarkConverter;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use DateTime;
use DateTimeZone;

class MeetingController extends Controller
{
    private function getDashboardData()
    {
        $userId = Auth::id();
        return Meeting::forUser($userId)
            ->with(['agendas', 'attendees'])
            ->orderBy('date', 'asc')
            ->paginate(3);
    }
    public function index()
    {
        $meetings = $this->getDashboardData();
        return view('dashboard', compact('meetings'));
    }

    public function create()
    {
        $users = User::select('id', 'name')->get();
        $user = Auth::user();

        // Assuming the user has a 'contacts' relationship
        $contacts = $user->contacts;
        if (!request()->has('code')) {
            // Store return URL in session
            session(['zoom_return_url' => 'create']);
            return $this->get_oauth_step_1();
        }

        // Get token after redirect
        $token = $this->get_oauth_step_2(request()->code);
        session(['zoom_token' => $token['access_token']]);

        return view('meeting.create', compact('users', 'contacts'));
    }

    public function edit(Meeting $meeting)
    {
        return view('meeting.postpone', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validatedData = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time',
        ]);

        $meeting->update([
            'date' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Meeting postponed successfully!');
    }

    public function meetings()
    {
        $userId = Auth::id();
        $meetings = Meeting::where('host_id', $userId)
            ->with('agendas')
            ->paginate(10);
        return view('dashboard', compact('meetings'));
    }

    public function store(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::id();
        } else {
            return redirect()->route('login')->with('error', 'You must be logged in to create a meeting.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'co_host_id' => 'nullable|exists:users,id',
            'attendees' => 'required|array|exists:users,id', // Validate that each attendee is a valid user ID
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time',
            'type' => 'required|string|in:online,offline',
            'venue' => 'required_if:type,offline|nullable|string|max:255',
            'agenda_title.*' => 'required|string|max:255', // Validate agenda titles as an array
            'description.*' => 'required|string', // Validate agenda descriptions as an array
            'presenter.*' => 'nullable|exists:users,id', // Validate agenda presenters as an array
        ]);

        try {
            DB::beginTransaction();

            // Convert the meeting description and sanitize it
            $converter = new CommonMarkConverter();

            // Create the meeting
            $meeting = Meeting::create([
                'title' => $validated['title'],
                'host_id' => $user_id,
                'co_host_id' => $validated['co_host_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'type' => $validated['type'],
                'venue' => $validated['venue'],
            ]);
            // Attach attendees
            foreach ($validated['attendees'] as $attendeeId) {
                $meeting->attendees()->create([
                    'user_id' => $attendeeId,
                ]);
            }

            $agendaDetails = [];
            foreach ($validated['agenda_title'] as $index => $agenda_title) {
                $agenda_description = $validated['description'][$index] ?? 'No description provided';
                $agenda_presenter = $validated['presenter'][$index] ?? null;

                // Convert and sanitize the description for the current agenda
                $convertedDescription = Purifier::clean($converter->convertToHtml($agenda_description));

                Agenda::create([
                    'meeting_id' => $meeting->id,
                    'title' => $agenda_title,
                    'description' => $convertedDescription,
                    'presenter_id' => $agenda_presenter,
                ]);

                $agendaDetails[] = [
                    'title' => $agenda_title,
                    'description' => $convertedDescription,
                    'presenter' => $agenda_presenter ? User::find($agenda_presenter)->name : 'N/A',
                ];
            }

            $zoomDetails = [
                'topic' => $meeting->title,
                'type' => 2, // Scheduled meeting// Format 'date' as 'Y-m-d' using the same start_time date
                'date' => (new DateTime($meeting->start_time, new DateTimeZone('Africa/Johannesburg')))
                    ->format('Y-m-d'), // Just the date portion in 'Y-m-d'

                'start_time' => (new DateTime($meeting->start_time, new DateTimeZone('Africa/Johannesburg')))
                    ->setTimezone(new DateTimeZone('UTC'))
                    ->format('Y-m-d\TH:i:s\Z'), // Convert to UTC
                'duration' => $meeting->duration ?? 45, // Default to 60 minutes if not set
                'timezone' => 'Africa/Johannesburg', // Optional if start_time is UTC
                'agenda' => "Meeting agenda: {$meeting->title}",
                'settings' => [
                    'host_video' => true, // Host video enabled
                    'participant_video' => true, // Participant video enabled
                    'join_before_host' => false, // Do not allow participants to join before host
                    'mute_upon_entry' => true, // Participants will be muted upon entry
                    'waiting_room' => true, // Enable waiting room
                    'approval_type' => 0, // No registration required
                ],
                'jwtToken' => session('zoom_token'), // Ensure this is valid
            ];


            $zoomResponse = $this->create_a_zoom_meeting($zoomDetails);
        if ($zoomResponse['success']) {
            $meeting->update(['meeting_link' => $zoomResponse['response']['join_url']]);
        }

            // Send notifications to attendees
            $this->sendMeetingNotifications($meeting, $agendaDetails);

            DB::commit();
            //$this->saveMeeting($request, $meeting, $agendaDetails);
            return redirect()->route('dashboard')->with('success', 'Meeting created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create meeting. Please try again.')->withInput();
        }
    }
    private function sendMeetingNotifications($meeting, $agendaDetails)
    {
        $attendees = User::whereIn('id', [$meeting->host_id, $meeting->co_host_id])->get();

        $details = [
            'greeting' => 'Hello, you have been invited to the following meeting:',
            'title' => $meeting->title,
            'datetime' => $meeting->start_time . ' - ' . $meeting->end_time,
            'venue' => $meeting->venue,
            'agenda' => collect($agendaDetails)
                ->map(fn($item) => " - {$item['title']} by {$item['presenter']} ({$item['description']})")
                ->join("\n"),
            'actiontext' => 'Join Meeting',
            'actionurl' => $this->generateZoomLink($meeting),
            'lastline' => 'Remember to RSVP!',
        ];

        Notification::send($attendees, new SendEmailNotification($details));
    }

    private function generateZoomLink($meeting)
    {
        // Placeholder for Zoom API integration
        //return "https://zoom.us/meeting-link/{$meeting->id}";
    }

    public function show($id, Request $request)
    {
        // get the meeting by its id.
        $meeting = Meeting::where('id', $id)->firstOrFail();
        //$users = User::all();

        return view('meeting.show', compact('meeting'));
    }

    public function deleteMeeting($id)
    {
        $meeting = Meeting::findOrFail($id);

        // Soft delete the meeting
        $meeting->delete();

        // Optionally change status if needed (not required for soft delete to work)
        $meeting->state = 'cancelled';
        $meeting->save();

        return redirect()->route('dashboard')->with('success', 'Meeting marked as deleted.');
    }

    // private function storage(Meeting $meeting){
    //     $meeting_data = [
    //             'id' => $meeting->id,
    //             'title' => $meeting->title,
    //             'type' => $meeting->type,
    //             'start_time' => $meeting->start_time,
    //             'timezone' => $meeting->timezone ?? 'America/New_York'
    //     ];
    // return $meeting_data;
    // }
    // public function saveMeeting(Request $request, Meeting $meeting)
    // {
    //     // Check if we have the authorization code

    //         // Retrieve meeting data from session
    //         $stored = session('stored_meeting');
    //         //dd($stored);
    //         // if (!$stored) {
    //         //     return redirect()->back()->withErrors('Meeting data is missing.');
    //         // }

    //         // Now proceed with OAuth step 2
    //         $getToken = $this->get_oauth_step_2($request->code);
    //         if (!$getToken || empty($getToken['access_token'])) {
    //             return redirect()->back()->withErrors('Failed to retrieve OAuth token.');
    //         }

    //         // Prepare Zoom meeting details
    //         $zoomDetails = [
    //             'topic' => $meeting['title'],
    //             'type' => $meeting['type'],
    //             'date' => $meeting['date'],
    //             'start_time' => $meeting['start_time'],
    //             'timezone' => $meeting['timezone'],
    //             'agenda' => "Meeting agenda: {$meeting['title']}",
    //             'jwtToken' => $getToken['access_token'],
    //         ];

    //         // Create Zoom meeting
    //         $zoomResponse = $this->create_a_zoom_meeting($zoomDetails);
    //         if (!$zoomResponse['success']) {
    //             return redirect()->back()->withErrors('Failed to create Zoom meeting.');
    //         }

    //         // Update meeting with Zoom link
    //         $meeting->update([
    //             'meeting_link' => $zoomResponse['response']['join_url'],
    //         ]);

    //         // Clear session data
    //         session()->forget('stored_meeting');

    //         // Retrieve and display updated dashboard data
    //         $meetings = $this->getDashboardData();
    //         return view('dashboard', compact('meetings'));
    //     }
    // }

    private function get_oauth_step_1()
    {
        $redirectURL = 'http://localhost/dashboard/linkUp/public/create';
        return redirect()->away("https://zoom.us/oauth/authorize?client_id=YRm9cLlZQYWFWYMNOvYVaA&redirect_uri={$redirectURL}&response_type=code&scope=&state=xyz");

        //++++++++++++++++++++++++++++++++++++++++++++++++
        //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $redirectURL  = 'http://localhost/dashboard/linkUp/public/create';
    //     $authorizeURL = 'https://zoom.us/oauth/authorize';
    //     //+++++++++++++++++++++++++++++++++++++++++++++++++++
    //     $clientID     = "YRm9cLlZQYWFWYMNOvYVaA";
    //     //++++++++++++++++++++++++++++++++++++++++++++++++++
    //     $authURL = $authorizeURL . '?client_id=' . $clientID . '&redirect_uri=' . $redirectURL . '&response_type=code&scope=&state=xyz';

    //     header('Location: ' . $authURL);
    //     exit;
    }

    private function get_oauth_step_2($code)
    {
        // Set the required variables
        $tokenURL    = 'https://zoom.us/oauth/token';
        $redirectURL = 'http://localhost/dashboard/linkUp/public/create';
        $clientID    = "YRm9cLlZQYWFWYMNOvYVaA";
        $clientSecret = "6uCVM6VSIlGeerMrHYqj4X509wiPdLOx";

        // Encode client_id and client_secret in Base64 for the Authorization header
        $authHeader = base64_encode("$clientID:$clientSecret");

        // Create the POST request body
        $postFields = http_build_query([
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectURL,
        ]);

        // Initialize cURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $tokenURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $authHeader",
                "Content-Type: application/x-www-form-urlencoded",
            ],
        ]);

        // Execute the cURL request and handle the response
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // Handle errors
        if ($err) {
            throw new \Exception("cURL Error: $err");
        }

        // Decode and return the response
        $response = json_decode($response, true);

        // Optionally handle Zoom API-specific errors here
        if (isset($response['error'])) {
            throw new \Exception("Zoom API Error: " . implode(',', $response));
        }

        return $response;
    }


    public function create_a_zoom_meeting($meetingConfig = [])
    {
        // $startTimeUTC = (new \DateTime($meetingConfig['start_time'], new \DateTimeZone($meetingConfig['timezone'])))
        //     ->setTimezone(new \DateTimeZone('UTC'))
        //     ->format('Y-m-d\TH:i:s\Z');

        $requestBody = [
            'topic' => $meetingConfig['topic'],
            'type' => 2,  // Scheduled meeting
            'start_time' => $meetingConfig['start_time'],
            'duration' => $meetingConfig['duration'] ?? 60, // Default to 60 minutes if not set
            'timezone' => 'Africa/Johannesburg',
            'agenda' => $meetingConfig['agenda'],
            'settings' => [
                'host_video' => false,
                'participant_video' => true,
                'join_before_host' => true,
                'mute_upon_entry' => true,
                'waiting_room' => false,
                'approval_type' => 0,
                'registration_type' => 0,
                'audio' => 'voip',
                'auto_recording' => 'none',
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.zoom.us/v2/users/me/meetings",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $meetingConfig['jwtToken'],
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return [
                'success' => false,
                'msg' => 'cURL Error: ' . $err,
                'response' => null,
            ];
        }

        $decodedResponse = json_decode($response, true);

        if (isset($decodedResponse['code'])) {
            return [
                'success' => false,
                'msg' => "Zoom API Error: " . $decodedResponse['message'],
                'response' => $decodedResponse,
            ];
        }

        return [
            'success' => true,
            'msg' => 'Meeting created successfully.',
            'response' => $decodedResponse,
        ];
    }
}
