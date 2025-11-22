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
                            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">Select Role</option>
                                <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Teacher" {{ old('role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="Librarian" {{ old('role') == 'Librarian' ? 'selected' : '' }}>Librarian</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" 
                                value="{{ old('department') }}" placeholder="e.g., Computer Science">
                            @error('department')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Batch</label>
                            <input type="text" name="batch" class="form-control @error('batch') is-invalid @enderror" 
                                value="{{ old('batch') }}" placeholder="e.g., 2024">
                            @error('batch')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Roll Number</label>
                            <input type="text" name="roll" class="form-control @error('roll') is-invalid @enderror" 
                                value="{{ old('roll') }}">
                            @error('roll')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Registration/ID Number</label>
                            <input type="text" name="reg_no" class="form-control @error('reg_no') is-invalid @enderror" 
                                value="{{ old('reg_no') }}">
                            @error('reg_no')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
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
@endsection

