@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-plus-circle"></i> Add New Book
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('books') }}">
                        <i class="fas fa-arrow-left"></i> Back to Books
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-2 col-md-8">
                    <form class="yourform" action="{{ route('book.store') }}" method="post" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Book Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Book Name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>ISBN</label>
                            <input type="text" class="form-control @error('isbn') is-invalid @enderror"
                                placeholder="ISBN" name="isbn" value="{{ old('isbn') }}">
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
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        @foreach ($authors as $author)
                                            <option value='{{ $author->id }}' {{ old('auther_id') == $author->id ? 'selected' : '' }}>
                                                {{ $author->name }}
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
                                            <option value='{{ $publisher->id }}' {{ old('publisher_id') == $publisher->id ? 'selected' : '' }}>
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
                                        placeholder="e.g., 1st, 2nd, 3rd" name="edition" value="{{ old('edition') }}">
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
                                        value="{{ old('publication_year') }}" min="1000" max="{{ date('Y') + 1 }}">
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
                                        placeholder="Total copies" name="total_quantity" value="{{ old('total_quantity', 1) }}" 
                                        min="1" required>
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
                                name="description" rows="4" placeholder="Book description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Book Cover Image</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                name="cover_image" accept="image/*">
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                            @error('cover_image')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-save"></i> Save Book
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
