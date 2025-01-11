@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Card Wrapper -->
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body pt-5 pb-3 px-5">
                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h3 class="text-center mb-1">{{ __('Register') }}</h3>

                        <!-- Register Form -->
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-1">
                                <label for="name" class="form-label"></label>
                                <input id="name" class="form-control @error('name') is-invalid @enderror"
                                    type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="mb-1">
                                <label for="email" class="form-label"></label>
                                <input id="email" class="form-control @error('email') is-invalid @enderror"
                                    type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-1">
                                <label for="password" class="form-label"></label>
                                <input id="password" class="form-control @error('password') is-invalid @enderror"
                                    type="password" name="password" required autocomplete="new-password" placeholder="Password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-1">
                                <label for="password_confirmation" class="form-label"></label>
                                <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                    type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Register Button -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary w-50 m-auto">{{ __('Register') }}</button>
                            </div>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-4 small">
                            <p class="text-muted mb-0">{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="text-primary">{{ __('Log In') }}</a></p>
                        </div>

                    </div>
                </div>
                <!-- End Card Wrapper -->
            </div>
        </div>
    </div>
@endsection
