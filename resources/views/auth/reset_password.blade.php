@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="logo" style="text-align: center; margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/library.png') }}" alt="" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                    </div>
                    <form class="yourform" action="{{ route('password.update') }}" method="post">
                        @csrf
                        <h3 class="heading">
                            <i class="fas fa-lock"></i> Set New Password
                        </h3>
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="At least 8 characters, 2 letters & 2 numbers" required minlength="8"
                                style="padding-right: 45px;">
                            <small class="form-text text-muted">Must contain at least 2 letters and 2 numbers.</small>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                placeholder="Confirm your new password" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-check"></i> Reset Password
                        </button>
                        <div class="mt-3 text-center">
                            <a href="{{ url('/') }}" style="font-size: 0.9rem; color: #7c3aed;">Back to Login</a>
                        </div>
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
