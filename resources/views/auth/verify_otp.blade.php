@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row">
                <div class="offset-md-3 col-md-6">
                    <div class="logo border border-danger">
                        <img src="{{ asset('images/library.png') }}" alt="">
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('register') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Registration
                        </a>
                    </div>
                    <form class="yourform" action="{{ route('verify.otp.post') }}" method="post" id="otpForm">
                        @csrf
                        <h3 class="heading">
                            <i class="fas fa-envelope"></i> Verify Your Email
                        </h3>

                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                            </div>
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

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Verification code sent!</strong><br>
                            We've sent a 6-digit verification code to:<br>
                            <strong>{{ $maskedEmail }}</strong>
                        </div>

                        <div class="form-group">
                            <label>Enter Verification Code <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="otp"
                                   id="otp-input"
                                   class="form-control @error('otp') is-invalid @enderror"
                                   placeholder="000000"
                                   maxlength="6"
                                   required
                                   autocomplete="off"
                                   style="font-size: 2rem; text-align: center; letter-spacing: 0.5rem; font-weight: bold; font-family: 'Courier New', monospace;">
                            @error('otp')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-clock"></i> Code expires in: <span id="countdown-timer">15:00</span>
                            </small>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg btn-block" id="verifyBtn">
                            <i class="fas fa-check-circle"></i> Verify Email
                        </button>

                        <div class="mt-3 text-center">
                            <p class="mb-2" style="color: #6c757d;">Didn't receive the code?</p>
                            <form action="{{ route('resend.otp') }}" method="post" id="resendForm" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary" id="resendBtn">
                                    <i class="fas fa-redo"></i> Resend Code
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-white" style="font-size: 0.9rem;">
                                <i class="fas fa-shield-alt"></i> Your information is secure. This code will expire in 15 minutes.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const otpInput = document.getElementById('otp-input');
            const otpForm = document.getElementById('otpForm');
            const resendForm = document.getElementById('resendForm');
            const resendBtn = document.getElementById('resendBtn');
            const countdownTimer = document.getElementById('countdown-timer');
            const verifyBtn = document.getElementById('verifyBtn');

            // OTP expiry timestamp from server (in seconds)
            const expiryTimestamp = {{ $otpExpiresAt }};
            let countdownInterval = null;

            // Format OTP input - only numbers, auto-format
            if (otpInput) {
                otpInput.addEventListener('input', function(e) {
                    // Remove any non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // Limit to 6 digits
                    if (this.value.length > 6) {
                        this.value = this.value.substring(0, 6);
                    }
                });

                // Auto-submit when 6 digits entered
                otpInput.addEventListener('input', function(e) {
                    if (this.value.length === 6) {
                        // Small delay to ensure value is set
                        setTimeout(function() {
                            otpForm.submit();
                        }, 100);
                    }
                });

                // Focus on input when page loads
                otpInput.focus();
            }

            // Countdown timer
            function updateCountdown() {
                const now = Math.floor(Date.now() / 1000);
                const remaining = expiryTimestamp - now;

                if (remaining <= 0) {
                    // OTP expired
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                    }
                    countdownTimer.textContent = '00:00';
                    countdownTimer.parentElement.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Code has expired. Please request a new code.</span>';
                    otpInput.disabled = true;
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<i class="fas fa-times-circle"></i> Code Expired';
                    return;
                }

                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                countdownTimer.textContent =
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }

            // Start countdown
            if (countdownTimer && expiryTimestamp) {
                updateCountdown();
                countdownInterval = setInterval(updateCountdown, 1000);
            }

            // Handle resend OTP - prevent double submission
            if (resendForm && resendBtn) {
                let isSubmitting = false;
                resendForm.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }

                    isSubmitting = true;
                    resendBtn.disabled = true;
                    resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                    // Form will submit normally and page will reload
                });
            }

            // Prevent form submission if OTP is expired
            if (otpForm) {
                otpForm.addEventListener('submit', function(e) {
                    const now = Math.floor(Date.now() / 1000);
                    if (now > expiryTimestamp) {
                        e.preventDefault();
                        alert('The verification code has expired. Please request a new code.');
                        return false;
                    }
                });
            }

            // Paste handler - extract 6 digits from pasted content
            if (otpInput) {
                otpInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = pastedData.replace(/[^0-9]/g, '').substring(0, 6);
                    this.value = digits;

                    // Auto-submit if 6 digits
                    if (digits.length === 6) {
                        setTimeout(function() {
                            otpForm.submit();
                        }, 100);
                    }
                });
            }

            // Keyboard navigation
            if (otpInput) {
                otpInput.addEventListener('keydown', function(e) {
                    // Allow: backspace, delete, tab, escape, enter
                    if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                        (e.keyCode === 65 && e.ctrlKey === true) ||
                        (e.keyCode === 67 && e.ctrlKey === true) ||
                        (e.keyCode === 86 && e.ctrlKey === true) ||
                        (e.keyCode === 88 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right
                        (e.keyCode >= 35 && e.keyCode <= 39)) {
                        return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });
            }
        })();
    </script>

@endsection
