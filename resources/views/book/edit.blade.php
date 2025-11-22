@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="admin-heading">Update Book</h2>
                </div>
                <div class="offset-md-7 col-md-2">
                    <a class="add-new" href="{{ route('books') }}">All Books</a>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-2 col-md-8">
                    <form class="yourform" action="{{ route('book.update', $book->id) }}" method="post"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Book Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Book Name" name="name" value="{{ old('name', $book->name) }}" required>
                            @error('name')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>ISBN</label>
                            <input type="text" class="form-control @error('isbn') is-invalid @enderror"
                                placeholder="ISBN" name="isbn" value="{{ old('isbn', $book->isbn) }}">
                            @error('isbn')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Author <span class="text-danger">*</span></label>
                                    <select class="form-control @error('auther_id') is-invalid @enderror" name="auther_id" required>
                                        <option value="">Select Author</option>
                                        @foreach ($authors as $auther)
                                            <option value="{{ $auther->id }}" 
                                                {{ old('auther_id', $book->auther_id) == $auther->id ? 'selected' : '' }}>
                                                {{ $auther->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('auther_id')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Publisher <span class="text-danger">*</span></label>
                                    <select class="form-control @error('publisher_id') is-invalid @enderror" name="publisher_id" required>
                                        <option value="">Select Publisher</option>
                                        @foreach ($publishers as $publisher)
                                            <option value="{{ $publisher->id }}" 
                                                {{ old('publisher_id', $book->publisher_id) == $publisher->id ? 'selected' : '' }}>
                                                {{ $publisher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('publisher_id')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Edition</label>
                                    <input type="text" class="form-control @error('edition') is-invalid @enderror"
                                        placeholder="e.g., 1st, 2nd" name="edition" value="{{ old('edition', $book->edition) }}">
                                    @error('edition')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Publication Year</label>
                                    <input type="number" class="form-control @error('publication_year') is-invalid @enderror"
                                        placeholder="e.g., 2024" name="publication_year" 
                                        value="{{ old('publication_year', $book->publication_year) }}" min="1000" max="{{ date('Y') + 1 }}">
                                    @error('publication_year')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_quantity') is-invalid @enderror"
                                        placeholder="Total copies" name="total_quantity" 
                                        value="{{ old('total_quantity', $book->total_quantity ?? 1) }}" min="1" required>
                                    <small class="form-text text-muted">
                                        Available: {{ $book->available_quantity ?? 0 }}, 
                                        Issued: {{ $book->issued_quantity ?? 0 }}
                                    </small>
                                    @error('total_quantity')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                name="description" rows="4" placeholder="Book description...">{{ old('description', $book->description) }}</textarea>
                            @error('description')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Book Cover Image</label>
                            @if($book->cover_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                        alt="{{ $book->name }}" style="max-width: 200px; max-height: 300px;">
                                    <p class="text-muted">Current cover image</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                name="cover_image" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep current image. Max size: 2MB</small>
                            @error('cover_image')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-save"></i> Update Book
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
