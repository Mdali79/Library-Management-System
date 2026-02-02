@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="logo" style="text-align: center; margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/library.png') }}" alt="" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                    </div>
                    <form class="yourform" action="{{ route('login') }}" method="post">
                        @csrf
                        @if(request('redirect'))
                            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                        @endif
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
                            <div class="text-right mt-1">
                                <a href="{{ route('password.request') }}" style="font-size: 0.9rem; color: #7c3aed;">Forgot Password?</a>
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
