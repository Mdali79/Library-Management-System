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
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">
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
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
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
                                        $currentYear = date('Y');
                                        $batches = [];
                                        // Generate batches from 2020 to current year + 2
                                        for ($year = 2020; $year <= $currentYear + 2; $year++) {
                                            $batches[] = $year;
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
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                required minlength="8">
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" name="register" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                        <div class="mt-4 text-center">
                            <p class="text-white">Already have an account? 
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
    </script>
@endsection

