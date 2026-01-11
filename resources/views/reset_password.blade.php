@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="admin-heading">Reset Password</h2>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-3 col-md-6">
                    <form class="yourform" action="{{ route('change_password') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label>Current Password</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control" name="c_password" id="current_password" value=""
                                    required style="padding-right: 45px;">
                                <button type="button" class="btn btn-link" onclick="togglePassword('current_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); padding: 0.375rem 0.5rem; color: #6c757d; text-decoration: none; z-index: 10; background: transparent; border: none;">
                                    <i class="fas fa-eye" id="current_password-eye"></i>
                                </button>
                            </div>
                            @error('c_password')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control" name="password" id="new_password" value=""
                                    required pattern="^(?=.*[A-Za-z]{2,})(?=.*[0-9]{2,}).{8,}$"
                                    title="Password must contain at least 2 letters, 2 numbers, and be 8 characters long"
                                    oninput="validatePassword(this.value)" style="padding-right: 45px;">
                                <button type="button" class="btn btn-link" onclick="togglePassword('new_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); padding: 0.375rem 0.5rem; color: #6c757d; text-decoration: none; z-index: 10; background: transparent; border: none;">
                                    <i class="fas fa-eye" id="new_password-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Password must contain: <strong>at least 2 letters, 2 numbers, and be 8+ characters long</strong>
                            </small>
                            <div id="password-strength" class="mt-2" style="display: none;">
                                <div class="progress" style="height: 5px;">
                                    <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <small id="password-strength-text" class="mt-1 d-block"></small>
                            </div>
                            @error('password')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control" name="password_confirmation" id="confirm_password" value=""
                                    required oninput="validatePasswordMatch()" style="padding-right: 45px;">
                                <button type="button" class="btn btn-link" onclick="togglePassword('confirm_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); padding: 0.375rem 0.5rem; color: #6c757d; text-decoration: none; z-index: 10; background: transparent; border: none;">
                                    <i class="fas fa-eye" id="confirm_password-eye"></i>
                                </button>
                            </div>
                            <small id="password-match-message" class="form-text" style="display: none;"></small>
                        </div>
                        <input type="submit" class="btn btn-danger" value="Update" required>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Validate password pattern
        function validatePassword(password) {
            const strengthDiv = document.getElementById('password-strength');
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            if (!password) {
                strengthDiv.style.display = 'none';
                return;
            }

            strengthDiv.style.display = 'block';

            // Count letters and numbers
            const letters = (password.match(/[A-Za-z]/g) || []).length;
            const numbers = (password.match(/[0-9]/g) || []).length;
            const length = password.length;

            // Check requirements
            const has2Letters = letters >= 2;
            const has2Numbers = numbers >= 2;
            const has8Chars = length >= 8;

            // Calculate strength
            let strength = 0;
            let strengthColor = '';

            if (has8Chars) strength += 33;
            if (has2Letters) strength += 33;
            if (has2Numbers) strength += 34;

            if (strength < 33) {
                strengthColor = 'bg-danger';
            } else if (strength < 66) {
                strengthColor = 'bg-warning';
            } else if (strength < 100) {
                strengthColor = 'bg-info';
            } else {
                strengthColor = 'bg-success';
            }

            strengthBar.className = 'progress-bar ' + strengthColor;
            strengthBar.style.width = strength + '%';

            // Show requirements status
            const requirements = [];
            if (!has8Chars) requirements.push('8+ characters');
            if (!has2Letters) requirements.push('2+ letters');
            if (!has2Numbers) requirements.push('2+ numbers');

            if (requirements.length > 0) {
                strengthText.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Missing: ' + requirements.join(', ') + '</span>';
            } else {
                strengthText.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Password meets all requirements</span>';
            }
        }

        // Validate password match
        function validatePasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchMessage = document.getElementById('password-match-message');

            if (!confirmPassword) {
                matchMessage.style.display = 'none';
                return;
            }

            matchMessage.style.display = 'block';

            if (password === confirmPassword) {
                matchMessage.className = 'form-text text-success';
                matchMessage.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
            } else {
                matchMessage.className = 'form-text text-danger';
                matchMessage.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Validate password pattern
            const letters = (password.match(/[A-Za-z]/g) || []).length;
            const numbers = (password.match(/[0-9]/g) || []).length;
            const length = password.length;

            if (length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                document.getElementById('new_password').focus();
                return false;
            }

            if (letters < 2) {
                e.preventDefault();
                alert('Password must contain at least 2 letters.');
                document.getElementById('new_password').focus();
                return false;
            }

            if (numbers < 2) {
                e.preventDefault();
                alert('Password must contain at least 2 numbers.');
                document.getElementById('new_password').focus();
                return false;
            }

            // Validate password match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                document.getElementById('confirm_password').focus();
                return false;
            }

            return true;
        });
    </script>
@endsection
