@extends('layouts.app')

@section('content')
    <style>
        .CodeMirror-scroll {
            min-height: 150px !important;
        }

        #calendar {
            transform: scale(0.67);
            transform-origin: 0 0;

        }
    </style>
    <!-- Display All Errors -->
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden row">
                <div class="col-md-2 col-lg-2 ">
                    <x-sidebar></x-sidebar>
                </div>
                <div class="col-lg-10 row my-4">
                    <h2 class="mt-4 text-prime" style="margin-left:2rem">Create a New Meeting</h2>
                    <div class=" my-4 col-md-8 col-lg-8 px-5">
                        <form action="{{ route('store') }}" method="POST" class="">
                            @csrf
                            <!-- Meeting Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label"></label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Meeting Title" required>
                            </div>
                            <!-- Co-host -->
                            <div class="mb-3">

                                <select class="form-select" id="co_host_id" name="co_host_id" aria-lable="co_host_id"
                                    required>
                                    <option class="muted-text" value="">Select Co-host</option>
                                    @foreach ($contacts as $contact)
                                        <option value="{{ $contact->id }}"
                                            {{ old('co_host_id') == $contact->id ? 'selected' : '' }}>
                                            {{ $contact->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                            <!-- Attendees -->
                            <div class="mb-3">
                                <label for="attendees" class="form-label">Attendees</label>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="attendeesDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Attendees
                                    </button>
                                    <div class="dropdown-menu p-3 scrollable-dropdown" aria-labelledby="attendeesDropdown">
                                        @foreach ($contacts as $contact)
                                            <div class="form-check">
                                                <input type="checkbox" id="attendee_{{ $contact->id }}" name="attendees[]"
                                                    value="{{ $contact->id }}" class="form-check-input"
                                                    {{ in_array($contact->id, old('attendees', [])) ? 'checked' : '' }}>
                                                <label for="attendee_{{ $contact->id }}" class="form-check-label">
                                                    {{ $contact->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                                <div class="mb-3 d-flex gap-2 align-items-center w-50">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="date" class="form-control" id="date" name="date"
                                    min="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                                </div>
                            <!-- Start Time -->
                            <div class="d-flex gap-4">
                                <div class="mb-3 d-flex gap-2 align-items-center w-50">
                                    <label for="start_time" class="form-label">Start:</label>
                                    <input type="time" step="60" class="form-control" id="start_time" name="start_time"
                                        required>
                                </div>
                                <!-- End Time -->
                                <div class="mb-3 d-flex gap-2 align-items-center w-50">
                                    <label for="end_time" class="form-label">End:</label>
                                    <input type="time" step="60" class="form-control" id="end_time" name="end_time"
                                        required>
                                </div>
                            </div>
                            <div class="d-flex gap-4">
                                <!-- Type -->
                                <div class="mb-3 d-flex gap-2 align-items-center w-50">
                                    <label for="type" class="form-label">Type:</label>
                                    <select class="form-select" id="meeting_type" name="type" required>
                                        <option class="text-muted" value="">Select</option>
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                    </select>
                                </div>

                                <!-- Venue -->
                                <div class="mb-3 d-flex gap-2 align-items-center w-50" id="venue-container" style="">
                                    <label for="venue" class="form-label">Venue:</label>
                                    <input type="text" class="form-control" id="venue" name="venue"
                                        placeholder="Enter venue location">
                                </div>
                                <!-- Link -->
                                {{-- Generate Link Once Meeting is Created --}}
                            </div>


                            <hr class="w-75 mx-auto blue">
                            <!-- Agenda -->
                            <div id="agenda-section">
                                <div class="agenda-item mb-4">
                                    <h5>Meeting Agenda 1</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="agenda_title_1" name="agenda_title[]"
                                            placeholder="Main Topic" aria-label="agenda_title">
                                    </div>
                                    <!-- Speakers -->
                                    <div class="mb-3">
                                        <label for="speakers" class="form-label">Speakers</label>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                id="speakersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                Select Speakers
                                            </button>
                                            <div class="dropdown-menu p-3 scrollable-dropdown"
                                                aria-labelledby="speakersDropdown">
                                                @foreach ($contacts as $contact)
                                                    <div class="form-check">
                                                        <input type="checkbox" id="presenter_{{ $contact->id }}"
                                                            name="presenter[]" value="{{ $contact->id }}"
                                                            class="form-check-input"
                                                            {{ in_array($contact->id, old('presenter', [])) ? 'checked' : '' }}>
                                                        <label for="presenter_{{ $contact->id }}"
                                                            class="form-check-label">
                                                            {{ $contact->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3" style="">
                                        <label for="description_1" class="form-label">Details: </label>
                                        <textarea class="form-control agenda-editor" name="description[]" id="description_1"></textarea>
                                    </div>

                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button type="button" id="add-agenda" class="btn-prime2 py-1" style="font-size:1rem">Add
                                Agenda
                                Item</button>
                            <button type="submit" class="btn-prime" style="font-size:1rem">Create Meeting</button>
                            <button href="{{ route('dashboard') }}" class=" btn-prime2 py-1"
                                style="font-size:1rem;">Cancel</button>
                            {{-- FIX THIS STUPID BUTTON --}}
                        </form>
                    </div>
                    <div class="col-md-4 col-lg-4 d-flex justify-content-center align-items-start">
                        <img src="images/calendar.jpeg" class="fixed-height mx-auto p-4 shadow rounded-2 p-4"
                            alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize EasyMDE for the first agenda item's "Details" field
            new EasyMDE({
                element: document.querySelector('#description_1')
            });

            let agendaCount = 1;

            // Add new agenda items dynamically
            document.getElementById('add-agenda').addEventListener('click', function() {
                agendaCount++;

                // Reference the existing agenda-section div
                const agendaSection = document.getElementById('agenda-section');

                // Create a new agenda item div
                const newAgenda = document.createElement('div');
                newAgenda.classList.add('agenda-item', 'mb-4');
                newAgenda.innerHTML = `
                    <h5>Meeting Agenda ${agendaCount}</h5>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="agenda_title_${agendaCount}" name="agenda_title[]"
                            placeholder="Main Topic" aria-label="agenda_title_${agendaCount}">
                    </div>
                    <!-- Speakers -->
                    <div class="mb-3">
                        <label for="speakers" class="form-label">Speakers</label>
                        <div class="dropdown">
                            <button
                                class="btn btn-secondary dropdown-toggle"
                                type="button"
                                id="speakersDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Select Speakers
                            </button>
                            <div class="dropdown-menu p-3 scrollable-dropdown" aria-labelledby="speakersDropdown">
                                @foreach ($contacts as $contact)
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            id="presenter_{{ $contact->id }}"
                                            name="presenter[]"
                                            value="{{ $contact->id }}"
                                            class="form-check-input"
                                            {{ in_array($contact->id, old('presenter', [])) ? 'checked' : '' }}
                                        >
                                        <label for="presenter_{{ $contact->id }}" class="form-check-label">
                                            {{ $contact->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description_${agendaCount}" class="form-label">Details:</label>
                        <textarea class="form-control agenda-editor" name="description[]" id="description_${agendaCount}"></textarea>
                    </div>
                `;


                // Append the new agenda item to the agenda section
                agendaSection.appendChild(newAgenda);

                // Initialize EasyMDE for the new agenda item's "Details" field
                new EasyMDE({
                    element: document.querySelector(`#description_${agendaCount}`)
                });
            });
        });
    </script>




@endsection
