@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row">
                <div class="offset-md-3 col-md-6">
                    <div class="logo border border-danger">
                        <img src="{{ asset('images/library.png') }}" alt="">
                    </div>
                    <form class="yourform" action="{{ route('register.store') }}" method="post" enctype="multipart/form-data">
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
                                <option value="Teacher" {{ old('role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="Librarian" {{ old('role') == 'Librarian' ? 'selected' : '' }}>Librarian</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Select your role. Admin/Librarian registrations require approval.
                            </small>
                            @error('role')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Student/Teacher Specific Fields -->
                        <div id="studentFields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> <strong>Student/Teacher Information</strong>
                            </div>
                            
                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" 
                                    value="{{ old('department') }}" placeholder="e.g., Computer Science">
                                @error('department')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Batch <span class="text-danger">*</span></label>
                                <input type="text" name="batch" id="batch" class="form-control @error('batch') is-invalid @enderror" 
                                    value="{{ old('batch') }}" placeholder="e.g., 2024">
                                @error('batch')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Roll Number <span class="text-danger">*</span></label>
                                <input type="text" name="roll" id="roll" class="form-control @error('roll') is-invalid @enderror" 
                                    value="{{ old('roll') }}" placeholder="e.g., CS1001">
                                @error('roll')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Registration/ID Number <span class="text-danger">*</span></label>
                                <input type="text" name="reg_no" id="reg_no" class="form-control @error('reg_no') is-invalid @enderror" 
                                    value="{{ old('reg_no') }}" placeholder="e.g., CS-10001">
                                @error('reg_no')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Admin/Librarian Specific Fields -->
                        <div id="adminFields" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> Admin/Librarian registrations require approval from an existing Administrator or Librarian.
                            </div>
                            
                            <div class="form-group">
                                <label>Department/Organization (Optional)</label>
                                <input type="text" name="department" id="admin_department" class="form-control @error('department') is-invalid @enderror" 
                                    value="{{ old('department') }}" placeholder="e.g., Library Management, IT Department">
                                <small class="form-text text-muted">Optional field for organizational reference</small>
                                @error('department')
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
            if (selectedRole === 'Student' || selectedRole === 'Teacher') {
                if (studentFields) studentFields.style.display = 'block';
                // Make student fields required
                if (deptField) deptField.setAttribute('required', 'required');
                if (batchField) batchField.setAttribute('required', 'required');
                if (rollField) rollField.setAttribute('required', 'required');
                if (regNoField) regNoField.setAttribute('required', 'required');
                // Hide admin department field
                if (adminDeptField) adminDeptField.value = '';
            } else if (selectedRole === 'Admin' || selectedRole === 'Librarian') {
                if (adminFields) adminFields.style.display = 'block';
                // Clear student fields
                if (deptField) deptField.value = '';
                if (batchField) batchField.value = '';
                if (rollField) rollField.value = '';
                if (regNoField) regNoField.value = '';
            } else {
                // No role selected - clear all fields
                if (deptField) deptField.value = '';
                if (adminDeptField) adminDeptField.value = '';
                if (batchField) batchField.value = '';
                if (rollField) rollField.value = '';
                if (regNoField) regNoField.value = '';
            }
        }
        
        // Run on page load if role is already selected (for validation errors)
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            if (roleSelect && roleSelect.value) {
                toggleRoleFields();
            }
        });
    </script>
@endsection

