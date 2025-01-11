@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Card Wrapper -->
                <div class="shadow-lg border-prime rounded-3 login-cont">
                    <div class="card-body p-5">
                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h3 class="text-center text-prime2 mb-1">{{ __('Login') }}</h3>

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-1">
                                <label for="email" class="form-label"></label>
                                <input id="email" class="form-control @error('email') is-invalid @enderror"
                                    type="email" name="email" value="{{ old('email') }}" required autofocus
                                    autocomplete="username" placeholder="Email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label"></label>
                                <input id="password" class="form-control @error('password') is-invalid @enderror"
                                    type="password" name="password" required autocomplete="current-password"
                                    placeholder="Password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                            class="text-muted small justify-content-start">
                                            {{ __('Forgot your password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>


                            <!-- Remember Me -->
                            <div class="form-check mb-3">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label for="remember_me" class="form-check-label small">{{ __('Remember me') }}</label>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class=" btn-prime w-25 m-auto">{{ __('Log in') }}</button>
                            </div>

                        </form>

                        <!-- Sign Up Link -->
                        <div class="text-center mt-2 small">
                            <p class="text-muted mb-0">{{ __('Don\'t have an account?') }} <a
                                    href="{{ route('register') }}" class="text-prime">{{ __('Sign Up') }}</a></p>
                        </div>

                    </div>
                </div>
                <!-- End Card Wrapper -->
            </div>
        </div>
    </div>
@endsection
