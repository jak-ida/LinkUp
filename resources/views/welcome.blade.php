@extends('layouts.app')

@section('content')
    <div class="text-center row w-auto px-5 py-5 mt-4" style="">
        <div class="col-md-6">
            <img src="images/Background_image3.png" alt="Meetings illustration" class="img-fluid height-auto    ">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <h1 class="display-4">Welcome to <i class="text-prime">Link<b class="fw-bold text-prime">UP</b></i></h1>
            <p class=" text-muted">Manage your meetings and streamline your calendar - all on One Platform</p>
            <div><a href="{{route('login')}}" class=" btn-prime btn-md">Login</a>
                <a href="{{route('register')}}" class=" btn-prime2">Sign Up</a>
            </div>
        </div>

    </div>
@endsection
