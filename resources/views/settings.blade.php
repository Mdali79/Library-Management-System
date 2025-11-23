@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2 class="admin-heading">
                        <i class="fas fa-cog"></i> System Settings
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-2 col-md-8">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form class="yourform" action="{{ route('settings.update') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="card mb-4" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                            <div class="card-header" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; border: none;">
                                <h5 style="margin: 0; font-weight: 600;">
                                    <i class="fas fa-clock"></i> Return & Fine Settings
                                </h5>
                            </div>
                            <div class="card-body" style="background: #ffffff;">
                                <div class="form-group">
                                    <label>Return Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="return_days" 
                                        value="{{ old('return_days', $data->return_days ?? 14) }}" 
                                        min="1" max="365" required>
                                    <small class="form-text text-muted">Number of days a book can be borrowed</small>
                                    @error('return_days')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Fine Per Day (in $) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="fine_per_day" 
                                        value="{{ old('fine_per_day', $data->fine_per_day ?? 0) }}" 
                                        min="0" required>
                                    <small class="form-text text-muted">Fine amount charged per day after grace period</small>
                                    @error('fine_per_day')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Fine Grace Period (Days) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="fine_grace_period_days" 
                                        value="{{ old('fine_grace_period_days', $data->fine_grace_period_days ?? 14) }}" 
                                        min="0" max="30" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> <strong>Fine calculation starts AFTER this many days past the return date.</strong><br>
                                        Example: If grace period is 14 days and return date is Jan 1, fine calculation starts on Jan 15 (14 days later).
                                    </small>
                                    @error('fine_grace_period_days')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                            <div class="card-header" style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%); color: white; border: none;">
                                <h5 style="margin: 0; font-weight: 600;">
                                    <i class="fas fa-book-reader"></i> Borrowing Limits
                                </h5>
                            </div>
                            <div class="card-body" style="background: #ffffff;">
                                <div class="form-group">
                                    <label>Max Books - Students <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="max_borrowing_limit_student" 
                                        value="{{ old('max_borrowing_limit_student', $data->max_borrowing_limit_student ?? 5) }}" 
                                        min="1" max="50" required>
                                    @error('max_borrowing_limit_student')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Max Books - Teachers <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="max_borrowing_limit_teacher" 
                                        value="{{ old('max_borrowing_limit_teacher', $data->max_borrowing_limit_teacher ?? 10) }}" 
                                        min="1" max="50" required>
                                    @error('max_borrowing_limit_teacher')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Max Books - Librarians <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="max_borrowing_limit_librarian" 
                                        value="{{ old('max_borrowing_limit_librarian', $data->max_borrowing_limit_librarian ?? 15) }}" 
                                        min="1" max="50" required>
                                    @error('max_borrowing_limit_librarian')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-save"></i> Update Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
