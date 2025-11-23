@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-book"></i> Request a Book
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('book_issued') }}">
                        <i class="fas fa-list"></i> My Requests
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-2 col-md-8">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; border: none;">
                            <h5 style="margin: 0; font-weight: 600;">
                                <i class="fas fa-info-circle"></i> Student Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $student->name }}</p>
                            <p><strong>Department:</strong> {{ $student->department ?? 'N/A' }}</p>
                            <p><strong>Roll No:</strong> {{ $student->roll ?? 'N/A' }}</p>
                            <p><strong>Registration No:</strong> {{ $student->reg_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <form class="yourform" action="{{ route('book_issue.store') }}" method="post" autocomplete="off" style="margin-top: 2rem;">
                        @csrf
                        <div class="form-group">
                            <label>Select Book <span class="text-danger">*</span></label>
                            <select class="form-control" name="book_id" required>
                                <option value="">Select Book</option>
                                @foreach ($books as $book)
                                    <option value='{{ $book->id }}' {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->name }} 
                                        @if($book->auther)
                                            - {{ $book->auther->name }}
                                        @endif
                                        @if($book->available_quantity > 0)
                                            (Available: {{ $book->available_quantity }})
                                        @else
                                            (Unavailable - Can Reserve)
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
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Note:</strong> Your request will be sent to the librarian for approval. 
                            You will be notified once it's approved or rejected.
                        </div>
                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

