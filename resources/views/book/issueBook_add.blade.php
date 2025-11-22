@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-hand-holding"></i> Issue Book
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('book_issued') }}">
                        <i class="fas fa-list"></i> All Issue List
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-3 col-md-6">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="yourform" action="{{ route('book_issue.store') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label>Member Name <span class="text-danger">*</span></label>
                            <select class="form-control" name="student_id" required>
                                <option value="">Select Member</option>
                                @foreach ($students as $student)
                                    <option value='{{ $student->id }}' {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} 
                                        @if($student->user)
                                            ({{ $student->user->role }} - {{ $student->reg_no ?? 'N/A' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Book Name <span class="text-danger">*</span></label>
                            <select class="form-control" name="book_id" required>
                                <option value="">Select Book</option>
                                @foreach ($books as $book)
                                    <option value='{{ $book->id }}' {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->name }} 
                                        @if($book->available_quantity > 0)
                                            (Available: {{ $book->available_quantity }})
                                        @else
                                            (Unavailable)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('book_id')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" class="form-control" name="issue_date" 
                                value="{{ old('issue_date', date('Y-m-d')) }}">
                            <small class="form-text text-muted">Leave empty to use today's date</small>
                        </div>
                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-check"></i> Issue Book
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
