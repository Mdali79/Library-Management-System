@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row align-items-center" style="min-height: 80vh;">
                <!-- Library Image Section -->
                <div class="col-md-6 d-none d-md-block">
                    <div class="text-center" style="padding: 2rem;">
                        <div style="position: relative; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); min-height: 500px; background: linear-gradient(135deg, #2563eb, #7c3aed);">
                            @php
                                $libraryImage = file_exists(public_path('images/library-img.png')) ? asset('images/library-img.png') : asset('images/library.png');
                            @endphp
                            <img src="{{ $libraryImage }}" alt="Library"
                                style="width: 100%; height: 100%; object-fit: cover; display: block; filter: blur(3px) brightness(0.7); position: absolute; top: 0; left: 0;">
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(37, 99, 235, 0.75), rgba(124, 58, 237, 0.75)); display: flex; align-items: center; justify-content: center; z-index: 1;">
                                <div class="text-center" style="color: white; padding: 2rem; z-index: 2;">
                                    <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.9;">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                        Welcome to Library
                                    </h2>
                                    <p style="font-size: 1.3rem; opacity: 0.95; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); line-height: 1.6;">
                                        Discover, Learn, and Explore<br>
                                        <span style="font-size: 1rem; opacity: 0.85;">Your Gateway to Knowledge</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Login Form Section -->
                <div class="col-md-6">
                    <div class="logo border border-danger" style="margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/library.png') }}" alt="">
                    </div>
                    <form class="yourform" action="{{ route('login') }}" method="post">
                        @csrf
                        <h3 class="heading">
                            <i class="fas fa-book-reader"></i> User Login
                        </h3>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}"
                                placeholder="Enter your username" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <div style="position: relative;">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Enter your password" required style="padding-right: 45px;">
                                <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6c757d; cursor: pointer; padding: 5px; z-index: 10;">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-lg btn-block" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        <div class="mt-3 text-center">
                            <p class="mb-3" style="font-size: 0.9rem; color: #6c757d;">Don't have an account?</p>
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg btn-block" style="background: linear-gradient(135deg, #10b981, #059669); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <i class="fas fa-user-plus"></i> Create New Account
                            </a>
                        </div>
                        <div class="mt-3 text-center">
                            <small style="opacity: 0.8; color: #6c757d;">
                                Register as Student or Admin
                            </small>
                        </div>
                    </form>
                    @error('username')
                        <div class='alert alert-danger'>{{ $message }}</div>
                    @enderror
                    @if(session('success'))
                        <div class='alert alert-success'>{{ session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            #wrapper-admin .row {
                min-height: auto !important;
            }
        }
        #wrapper-admin .yourform {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        #togglePassword {
            transition: color 0.2s ease;
        }
        #togglePassword:hover {
            color: #2563eb !important;
        }
        #togglePassword:focus {
            outline: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle eye icon
                    if (type === 'password') {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    } else {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    }
                });
            }
        });
    </script>
@endsection
