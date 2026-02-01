@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 4rem);">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="logo" style="text-align: center; margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/library.png') }}" alt="" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                    </div>
                    <div class="mb-3" style="text-align: left;">
                        <a href="{{ route('password.request') }}" class="btn btn-secondary btn-sm" style="background: rgba(255, 255, 255, 0.9); color: #333; border: 1px solid rgba(0,0,0,0.1);">
                            <i class="fas fa-arrow-left"></i> Back to Forgot Password
                        </a>
                    </div>
                    <form class="yourform" action="{{ route('password.verify.otp.post') }}" method="post" id="otpForm">
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
                            We've sent a 6-digit code to:<br>
                            <strong>{{ $maskedEmail }}</strong>
                        </div>
                        <div class="form-group">
                            <label>Enter Verification Code <span class="text-danger">*</span></label>
                            <input type="text" name="otp" id="otp-input"
                                class="form-control @error('otp') is-invalid @enderror"
                                placeholder="000000" maxlength="6" required autocomplete="off"
                                style="font-size: 2rem; text-align: center; letter-spacing: 0.5rem; font-weight: bold; font-family: 'Courier New', monospace;">
                            @error('otp')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-clock"></i> Code expires in: <span id="countdown-timer">15:00</span>
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" id="verifyBtn" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-check-circle"></i> Verify & Continue
                        </button>
                        <div class="mt-3 text-center">
                            <p class="mb-2" style="color: #6c757d;">Didn't receive the code?</p>
                            <form action="{{ route('password.resend.otp') }}" method="post" id="resendForm" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary" id="resendBtn">
                                    <i class="fas fa-redo"></i> Resend Code
                                </button>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const otpInput = document.getElementById('otp-input');
            const otpForm = document.getElementById('otpForm');
            const resendBtn = document.getElementById('resendBtn');
            const countdownTimer = document.getElementById('countdown-timer');
            const verifyBtn = document.getElementById('verifyBtn');
            const expiryTimestamp = {{ $otpExpiresAt }};
            let countdownInterval = null;

            if (otpInput) {
                otpInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 6) this.value = this.value.substring(0, 6);
                    if (this.value.length === 6) setTimeout(function() { otpForm.submit(); }, 100);
                });
                otpInput.focus();
                otpInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const digits = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').substring(0, 6);
                    this.value = digits;
                    if (digits.length === 6) setTimeout(function() { otpForm.submit(); }, 100);
                });
            }

            function updateCountdown() {
                const now = Math.floor(Date.now() / 1000);
                const remaining = expiryTimestamp - now;
                if (remaining <= 0) {
                    if (countdownInterval) clearInterval(countdownInterval);
                    countdownTimer.textContent = '00:00';
                    countdownTimer.parentElement.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Code expired. Request a new code.</span>';
                    otpInput.disabled = true;
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<i class="fas fa-times-circle"></i> Code Expired';
                    return;
                }
                const m = Math.floor(remaining / 60), s = remaining % 60;
                countdownTimer.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }
            if (countdownTimer && expiryTimestamp) {
                updateCountdown();
                countdownInterval = setInterval(updateCountdown, 1000);
            }

            if (otpForm) {
                otpForm.addEventListener('submit', function(e) {
                    if (Math.floor(Date.now() / 1000) > expiryTimestamp) {
                        e.preventDefault();
                        alert('The verification code has expired. Please request a new code.');
                        return false;
                    }
                });
            }
        })();
    </script>
    <style>
        #wrapper-admin .yourform { background: rgba(255, 255, 255, 0.98); border-radius: 15px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        #otp-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25); }
    </style>
@endsection
