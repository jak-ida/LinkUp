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
                    <div class="col-lg-11 mx-auto">
                        <div class="d-flex justify-content-between mb-3 me-3">
                            <a class="btn-prime" href="{{ route('dashboard') }}"> Back </a>
                            <div class="col-md-3 d-flex justify-content-end align-items-center">
                                @if ($meeting->host_id === Auth::id())
                                    <a class="btn-prime me-2 m-auto" href="{{ route('meeting.postpone', $meeting->id) }}">
                                        Postpone </a>
                                @endif
                                @if ($meeting->host_id === Auth::id())
                                <form action="{{ route('meeting.delete', $meeting->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-secondary py-1" style="font-size: .8rem"
                                        href="{{ route('meeting.delete', $meeting->id) }}"> Cancel
                                    </button>
                                </form>
                                    @else
                                        <a class="btn btn-secondary py-1" style="font-size: .8rem"> Decline </a>
                                    @endif

                            </div>
                        </div>
                        @if ($meeting)
                            <div class="shadow-sm p-5 rounded-2 me-3 border-prime my-3 row">
                                <div class="">
                                    <h1 class="text-prime display-6">{{ $meeting->title }}</h1>
                                    <div class="d-flex gap-4">
                                        <h5><i class="text-prime">Host:</i> {{ $meeting->host->name }}</h5>
                                        <h5><i class="text-prime">Co-Host:</i>
                                            {{ $meeting->co_host_id === $meeting->host_id ? 'Null' : $meeting->co_host->name }}
                                        </h5>
                                    </div>
                                    <div class="d-flex gap-3 display-7">
                                        <p class="p"><i class="text-prime">Date:</i>
                                            {{ $meeting->date }}</p>
                                        <p class="p"><i class="text-prime">Time:</i>
                                            {{ $meeting->start_time }} - {{ $meeting->end_time }}</p>
                                    </div>
                                    <div class="d-flex gap-5 display-7">
                                        <p class="p"><i class="text-prime">Type:</i>
                                            {{ $meeting->type }}</p>
                                        <p class="p"><i class="text-prime">Venue:</i>
                                            {{ $meeting->venue ?? ($meeting->link ?? 'Contact Host to Confirm  Venue') }}</small>
                                        </p>
                                    </div>
                                    <hr class="w-75 blue mx-auto my-4">
                                    @foreach ($meeting->agendas as $agenda)
                                        <p class="mt-2 fw-bold display-7"> <i class="text-prime"> Agenda Item: </i>
                                            <i>{{ $agenda->title }}</i></p>
                                            <p> <i class="text-prime">Speaker: </i>{{ $agenda->presenter->name ?? $agenda->meeting->host->name}}</p>

                                        {{-- @foreach ($users as $user)
                                            @if ($user->id === $agenda->presenter_id)
                                                <p>Speaker: <i class="text-prime">{{ $user->name }}</i></p>
                                            @endif
                                        @endforeach --}}
                                        <p> {!! $agenda->description !!}</p>
                                        <br>
                                    @endforeach
                                </div>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
