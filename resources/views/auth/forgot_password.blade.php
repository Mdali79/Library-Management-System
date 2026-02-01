@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="logo" style="text-align: center; margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/library.png') }}" alt="" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                    </div>
                    <div class="mb-3" style="text-align: left;">
                        <a href="{{ url('/') }}" class="btn btn-secondary btn-sm" style="background: rgba(255, 255, 255, 0.9); color: #333; border: 1px solid rgba(0,0,0,0.1);">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                    <form class="yourform" action="{{ route('password.email') }}" method="post">
                        @csrf
                        <h3 class="heading">
                            <i class="fas fa-key"></i> Forgot Password
                        </h3>
                        <p class="text-muted mb-3" style="font-size: 0.9rem;">Enter your email address and we'll send you a verification code to reset your password.</p>
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="Enter your registered email" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-paper-plane"></i> Send Verification Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        #wrapper-admin .yourform {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
@endsection
