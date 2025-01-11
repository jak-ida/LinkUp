@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden row">
                <div class="col-md-2 col-lg-2 ">
                    <x-sidebar></x-sidebar>
                </div>
                <div class="col-lg-10 row my-4">
                    <div class="col-md-9">
                        <div class="p-6 text-gray-900 d-flex justify-content-end">


                        </div>
                        {{-- @if (isset($meetings))
                            {{ $meetings->count() }} meetings found.
                        @else
                            No meetings available.
                        @endif --}}
                        @forelse ($meetings as $meeting)
                            @if ($meeting->date === \Carbon\Carbon::today()->toDateString())
                                <p class="text-success">This meeting is scheduled for today!</p>
                            @endif
                            <div class="shadow-sm py-3 px-4 rounded-2 me-3 border-prime my-3 row">

                                <div class="col-md-9">
                                    <h3><i class="text-prime">{{ $meeting->title }}</i> </h4>
                                        <div class="d-flex gap-4">
                                            <h6><i class="text-prime">Host:</i> {{ $meeting->host->name }}</h6>
                                            <h6><i class="text-prime">Co-Host:</i>
                                                {{ $meeting->co_host_id === $meeting->host_id ? 'Null' : $meeting->co_host->name }}
                                            </h6>
                                        </div>

                                        <div class="d-flex gap-3">
                                            <p class="p"><small> <i class="text-prime">Date:</i>
                                                    {{ $meeting->date }}</small></p>
                                            <p class="p"><small> <i class="text-prime">Time:</i>
                                                    {{ $meeting->start_time }} - {{ $meeting->end_time }}</small></p>
                                        </div>
                                        <div class="d-flex gap-5">
                                            <p class="p"><small> <i class="text-prime">Type:</i>
                                                    {{ $meeting->type }}</small></p>
                                            <p class="p"><small> <i class="text-prime">Venue:</i>
                                                    {{ $meeting->venue ?? ($meeting->link ?? 'Contact Host to Confirm  Venue') }}</small>
                                            </p>
                                        </div>
                                </div>
                                <div class="col-md-3 d-grid justify-content-end align-items-start my-4">
                                    <a href="{{ route('meeting.show', $meeting->id) }}" class="btn-prime"> View Details</a>
                                    <hr class="w-75 mx-auto blue2">
                                    @if ($meeting->host_id === Auth::id())
                                        <a class="btn btn-secondary py-1"
                                            href="{{ route('meeting.delete', $meeting->id) }}" style="font-size: .8rem">
                                            Cancel </a>
                                    @else
                                        <a class="btn btn-secondary py-1" style="font-size: .8rem"> Decline </a>
                                    @endif

                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p>You do not have any meetings lined up. Create one.</p>
                            </div>
                        @endforelse
                        <div class="pagination-container">
                            {{ $meetings->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="my-2">
                            <img src="images/calendar.jpeg" class="m-auto" style="width:90%" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
