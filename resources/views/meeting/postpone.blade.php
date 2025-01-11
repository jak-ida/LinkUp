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
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden row">
                <div class="col-md-2 col-lg-2 ">
                    <x-sidebar></x-sidebar>
                </div>
                <div class="col-lg-10 row my-4">
                    <h2 class="mt-4 text-prime" style="margin-left:2rem">Postpone Meeting: <i>"{{$meeting->title}}"</i></h2>
                    <div class=" my-4 col-md-8 col-lg-8 px-5">
                        <form action="{{ route('meeting.updatePostpone', $meeting->id) }}" method="POST" class="border-prime p-4 rounded">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="date" class="form-label">New Date:</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $meeting->date) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="start_time" class="form-label">New Start Time:</label>
                                <input type="time-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $meeting->start_time) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="end_time" class="form-label">New End Time:</label>
                                <input type="time-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $meeting->end_time) }}" required>
                            </div>

                            <button type="submit" class="btn-prime">Save Changes</button>
                            <a href="{{ route('dashboard') }}" class="btn-prime2">Cancel</a>
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
                    <div class="mb-3">
                        <select class="form-select" name="presenter[]" id="presenter_${agendaCount}" aria-label="Select speaker" required>
                            <option value="">Select Speaker</option>
                            <option value="1">User 1</option>
                            <option value="2">User 2</option>
                            <option value="3">User 3</option>
                        </select>
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

