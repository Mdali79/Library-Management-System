@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 4rem);">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="logo" style="text-align: center; margin-bottom: 1.5rem; display: flex; justify-content: center; align-items: center;">
                        <img src="{{ asset('images/library.png') }}" alt="" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                    </div>
                    <div class="mb-3" style="text-align: left;">
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm" style="background: rgba(255, 255, 255, 0.9); color: #333; border: 1px solid rgba(0,0,0,0.1);">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                    <form class="yourform" action="{{ route('register.store') }}" method="post" enctype="multipart/form-data" id="registrationForm" onsubmit="return validateDepartment()">
                        @csrf
                        <h3 class="heading">
                            <i class="fas fa-user-plus"></i> User Registration
                        </h3>

                        <div class="form-group">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username') }}" required>
                            @error('username')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                title="Please enter a valid email address (e.g., user@example.com)">
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">A verification code will be sent to this email address</small>
                        </div>

                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                                value="{{ old('contact') }}" placeholder="e.g., +1234567890">
                            @error('contact')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror" required id="roleSelect" onchange="toggleRoleFields()">
                                <option value="">Select Role</option>
                                <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Select your role. Admin registrations require approval.
                            </small>
                            @error('role')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Student Specific Fields -->
                        <div id="studentFields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> <strong>Student Information</strong>
                            </div>

                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <select name="department" id="department" class="form-control @error('department') is-invalid @enderror" required disabled>
                                    <option value="">Select Department</option>
                                    @php
                                        $selectedDept = old('department');
                                    @endphp
                                    <option value="Computer Science" {{ $selectedDept == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                    <option value="CSE" {{ $selectedDept == 'CSE' ? 'selected' : '' }}>CSE (Computer Science & Engineering)</option>
                                    <option value="Electrical Engineering" {{ $selectedDept == 'Electrical Engineering' ? 'selected' : '' }}>Electrical Engineering</option>
                                    <option value="Mechanical Engineering" {{ $selectedDept == 'Mechanical Engineering' ? 'selected' : '' }}>Mechanical Engineering</option>
                                    <option value="Civil Engineering" {{ $selectedDept == 'Civil Engineering' ? 'selected' : '' }}>Civil Engineering</option>
                                    <option value="Business Administration" {{ $selectedDept == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                                    <option value="Mathematics" {{ $selectedDept == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                                    <option value="Physics" {{ $selectedDept == 'Physics' ? 'selected' : '' }}>Physics</option>
                                    <option value="Chemistry" {{ $selectedDept == 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
                                    <option value="English" {{ $selectedDept == 'English' ? 'selected' : '' }}>English</option>
                                    <option value="Economics" {{ $selectedDept == 'Economics' ? 'selected' : '' }}>Economics</option>
                                    <option value="Other" {{ $selectedDept == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('department')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Batch <span class="text-danger">*</span></label>
                                <select name="batch" id="batch" class="form-control @error('batch') is-invalid @enderror" required>
                                    <option value="">Select Batch</option>
                                    @php
                                        $batches = [];
                                        // Generate batches from 90th to 120th
                                        for ($i = 90; $i <= 120; $i++) {
                                            $batches[] = $i . 'th';
                                        }
                                        $selectedBatch = old('batch');
                                    @endphp
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch }}" {{ $selectedBatch == $batch ? 'selected' : '' }}>{{ $batch }}</option>
                                    @endforeach
                                </select>
                                @error('batch')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Roll Number <span class="text-danger">*</span></label>
                                <input type="text" name="roll" id="roll" class="form-control @error('roll') is-invalid @enderror"
                                    value="{{ old('roll') }}" placeholder="e.g., CS1001" disabled>
                                @error('roll')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Registration/ID Number <span class="text-danger">*</span></label>
                                <input type="text" name="reg_no" id="reg_no" class="form-control @error('reg_no') is-invalid @enderror"
                                    value="{{ old('reg_no') }}" placeholder="e.g., CS-10001" disabled>
                                @error('reg_no')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Admin Specific Fields -->
                        <div id="adminFields" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> Admin registrations require approval from an existing Administrator.
                            </div>

                            <div class="form-group">
                                <label>Department/Organization (Optional)</label>
                                <input type="text" name="admin_department" id="admin_department" class="form-control @error('admin_department') is-invalid @enderror"
                                    value="{{ old('admin_department') }}" placeholder="e.g., Library Management, IT Department" disabled>
                                <small class="form-text text-muted">Optional field for organizational reference</small>
                                @error('admin_department')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <div style="position: relative;">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                    required minlength="8" pattern="^(?=.*[A-Za-z]{2,})(?=.*[0-9]{2,}).{8,}$"
                                    title="Password must contain at least 2 letters, 2 numbers, and be 8 characters long"
                                    oninput="validatePassword(this.value)" style="padding-right: 45px;">
                                <button type="button" class="btn btn-link" onclick="togglePassword('password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); padding: 0.375rem 0.5rem; color: #6c757d; text-decoration: none; z-index: 10; background: transparent; border: none; outline: none;">
                                    <i class="fas fa-eye" id="password-eye"></i>
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
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <div style="position: relative;">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                    required oninput="validatePasswordMatch()" style="padding-right: 45px;">
                                <button type="button" class="btn btn-link" onclick="togglePassword('password_confirmation')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); padding: 0.375rem 0.5rem; color: #6c757d; text-decoration: none; z-index: 10; background: transparent; border: none; outline: none;">
                                    <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                </button>
                            </div>
                            <small id="password-match-message" class="form-text" style="display: none;"></small>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror"
                                    name="terms" id="terms" value="1" required
                                    style="margin-top: 0.3rem; cursor: pointer;">
                                <label class="form-check-label" for="terms" style="cursor: pointer; color: #000; font-size: 0.95rem;">
                                    <span class="text-danger">*</span> I agree to the Terms and Conditions and Privacy Policy
                                </label>
                                @error('terms')
                                    <div class="alert alert-danger mt-2" role="alert">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-primary btn-lg btn-block" id="registerBtn" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                        <div class="mt-3 text-center">
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg btn-block">
                                <i class="fas fa-arrow-left"></i> Back to Login
                            </a>
                        </div>
                        <div class="mt-4 text-center">
                            <p style="color: rgba(255, 255, 255, 0.95); margin-bottom: 0;">Already have an account?
                                <a href="{{ route('login') }}" style="color: #fff; font-weight: 600; text-decoration: underline;">
                                    Login Here
                                </a>
                            </p>
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
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            #wrapper-admin .yourform {
                padding: 3rem;
            }
        }

        @media (min-width: 992px) {
            #wrapper-admin .yourform {
                padding: 3.5rem;
            }
        }

        .form-group > div[style*="position: relative"] {
            position: relative;
        }
        .form-group > div[style*="position: relative"] button {
            background: transparent !important;
            border: none !important;
            outline: none !important;
        }
        .form-group > div[style*="position: relative"] button:hover {
            color: #2563eb !important;
        }
        .form-group > div[style*="position: relative"] button:focus {
            box-shadow: none !important;
        }
        #password-strength .progress {
            height: 5px;
            border-radius: 3px;
            background-color: #e9ecef;
        }
        #password-strength .progress-bar {
            transition: width 0.3s ease;
        }
    </style>

    <script>
        function toggleRoleFields() {
            const roleSelect = document.getElementById('roleSelect');
            const selectedRole = roleSelect.value;
            const studentFields = document.getElementById('studentFields');
            const adminFields = document.getElementById('adminFields');

            // Hide all fields first
            if (studentFields) studentFields.style.display = 'none';
            if (adminFields) adminFields.style.display = 'none';

            // Get field references
            const deptField = document.getElementById('department');
            const adminDeptField = document.getElementById('admin_department');
            const batchField = document.getElementById('batch');
            const rollField = document.getElementById('roll');
            const regNoField = document.getElementById('reg_no');

            // Remove required attributes from all fields
            if (deptField) deptField.removeAttribute('required');
            if (adminDeptField) adminDeptField.removeAttribute('required');
            if (batchField) batchField.removeAttribute('required');
            if (rollField) rollField.removeAttribute('required');
            if (regNoField) regNoField.removeAttribute('required');

            // Show relevant fields based on role
            if (selectedRole === 'Student') {
                if (studentFields) studentFields.style.display = 'block';
                // Make student fields required and enable them
                if (deptField) {
                    deptField.setAttribute('required', 'required');
                    deptField.removeAttribute('disabled');
                    // Only reset dropdown if it's empty or has no valid selection
                    if (deptField.tagName === 'SELECT' && (!deptField.value || deptField.value === '')) {
                        deptField.selectedIndex = 0;
                    }
                }
                if (batchField) {
                    batchField.setAttribute('required', 'required');
                    batchField.removeAttribute('disabled');
                }
                if (rollField) {
                    rollField.setAttribute('required', 'required');
                    rollField.removeAttribute('disabled');
                }
                if (regNoField) {
                    regNoField.setAttribute('required', 'required');
                    regNoField.removeAttribute('disabled');
                }
                // Disable and clear admin department field
                if (adminDeptField) {
                    adminDeptField.value = '';
                    adminDeptField.setAttribute('disabled', 'disabled');
                }
            } else if (selectedRole === 'Admin') {
                if (adminFields) adminFields.style.display = 'block';
                // Disable student department field
                if (deptField) {
                    deptField.removeAttribute('required');
                    deptField.setAttribute('disabled', 'disabled');
                    if (deptField.tagName === 'SELECT') {
                        deptField.selectedIndex = 0;
                    } else {
                        deptField.value = '';
                    }
                }
                // Enable admin department field
                if (adminDeptField) {
                    adminDeptField.removeAttribute('disabled');
                }
                // Disable student fields
                if (batchField) {
                    batchField.value = '';
                    batchField.removeAttribute('required');
                    batchField.setAttribute('disabled', 'disabled');
                }
                if (rollField) {
                    rollField.value = '';
                    rollField.removeAttribute('required');
                    rollField.setAttribute('disabled', 'disabled');
                }
                if (regNoField) {
                    regNoField.value = '';
                    regNoField.removeAttribute('required');
                    regNoField.setAttribute('disabled', 'disabled');
                }
            } else {
                // No role selected - disable all fields
                if (deptField) {
                    deptField.removeAttribute('required');
                    deptField.setAttribute('disabled', 'disabled');
                    if (deptField.tagName === 'SELECT') {
                        deptField.selectedIndex = 0;
                    } else {
                        deptField.value = '';
                    }
                }
                if (adminDeptField) {
                    adminDeptField.value = '';
                    adminDeptField.setAttribute('disabled', 'disabled');
                }
                if (batchField) {
                    batchField.value = '';
                    batchField.removeAttribute('required');
                    batchField.setAttribute('disabled', 'disabled');
                }
                if (rollField) {
                    rollField.value = '';
                    rollField.removeAttribute('required');
                    rollField.setAttribute('disabled', 'disabled');
                }
                if (regNoField) {
                    regNoField.value = '';
                    regNoField.removeAttribute('required');
                    regNoField.setAttribute('disabled', 'disabled');
                }
            }
        }

        // Run on page load if role is already selected (for validation errors)
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            if (roleSelect && roleSelect.value) {
                toggleRoleFields();
            }
        });

        // Validate department before form submission
        function validateDepartment() {
            const roleSelect = document.getElementById('roleSelect');
            const deptField = document.getElementById('department');
            const adminDeptField = document.getElementById('admin_department');
            const studentFields = document.getElementById('studentFields');

            if (roleSelect && roleSelect.value === 'Student') {
                if (studentFields && studentFields.style.display !== 'none') {
                    // Ensure student department field is enabled
                    if (deptField) {
                        deptField.removeAttribute('disabled');
                    }
                    // Ensure admin department field is disabled (so it's not submitted)
                    if (adminDeptField) {
                        adminDeptField.setAttribute('disabled', 'disabled');
                    }

                    if (!deptField || !deptField.value || deptField.value === '' || deptField.value === 'Select Department') {
                        alert('Please select a department from the dropdown.');
                        if (deptField) {
                            deptField.focus();
                            deptField.classList.add('is-invalid');
                        }
                        return false;
                    }
                }
            } else if (roleSelect && roleSelect.value === 'Admin') {
                // Ensure student department field is disabled
                if (deptField) {
                    deptField.setAttribute('disabled', 'disabled');
                }
                // Admin department is optional, so we don't need to validate it
            }
            return true;
        }

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
            let strengthLabel = '';

            if (has8Chars) strength += 33;
            if (has2Letters) strength += 33;
            if (has2Numbers) strength += 34;

            if (strength < 33) {
                strengthColor = 'bg-danger';
                strengthLabel = 'Weak';
            } else if (strength < 66) {
                strengthColor = 'bg-warning';
                strengthLabel = 'Medium';
            } else if (strength < 100) {
                strengthColor = 'bg-info';
                strengthLabel = 'Good';
            } else {
                strengthColor = 'bg-success';
                strengthLabel = 'Strong';
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
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
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

        // Enhanced form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const termsChecked = document.getElementById('terms').checked;

            if (!termsChecked) {
                e.preventDefault();
                alert('Please agree to the Terms and Conditions and Privacy Policy to continue.');
                document.getElementById('terms').focus();
                return false;
            }

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            // Validate password pattern
            const letters = (password.match(/[A-Za-z]/g) || []).length;
            const numbers = (password.match(/[0-9]/g) || []).length;
            const length = password.length;

            if (length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                document.getElementById('password').focus();
                return false;
            }

            if (letters < 2) {
                e.preventDefault();
                alert('Password must contain at least 2 letters.');
                document.getElementById('password').focus();
                return false;
            }

            if (numbers < 2) {
                e.preventDefault();
                alert('Password must contain at least 2 numbers.');
                document.getElementById('password').focus();
                return false;
            }

            // Validate password match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                document.getElementById('password_confirmation').focus();
                return false;
            }

            return true;
        });
    </script>
@endsection

