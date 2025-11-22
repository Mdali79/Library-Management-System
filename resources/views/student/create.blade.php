@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-user-plus"></i> Add Member
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('students') }}">
                        <i class="fas fa-list"></i> All Members
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-3 col-md-6">
                    <form class="yourform" action="{{ route('student.store') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label>Student Name</label>
                            <input type="text" class="form-control" placeholder="Student Name" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" class="form-control" placeholder="Address" name="address"
                                value="{{ old('address') }}" required>
                            @error('address')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="male" selected>Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <input type="text" class="form-control" placeholder="Class" name="class"
                                value="{{ old('class') }}" required>
                            @error('class')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Age</label>
                            <input type="number" class="form-control" placeholder="Age" name="age"
                                value="{{ old('age') }}" required>
                            @error('age')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="phone" class="form-control" placeholder="Phone" name="phone"
                                value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" placeholder="Email" name="email"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Teacher" {{ old('role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="Librarian" {{ old('role') == 'Librarian' ? 'selected' : '' }}>Librarian</option>
                            </select>
                            @error('role')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control" placeholder="Department" name="department"
                                value="{{ old('department') }}">
                            @error('department')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Batch</label>
                            <input type="text" class="form-control" placeholder="Batch" name="batch"
                                value="{{ old('batch') }}">
                            @error('batch')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Roll Number</label>
                            <input type="text" class="form-control" placeholder="Roll Number" name="roll"
                                value="{{ old('roll') }}">
                            @error('roll')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Registration/ID Number</label>
                            <input type="text" class="form-control" placeholder="Reg. No / ID" name="reg_no"
                                value="{{ old('reg_no') }}">
                            @error('reg_no')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-save"></i> Save Member
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
