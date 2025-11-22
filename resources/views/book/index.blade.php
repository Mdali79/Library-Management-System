@extends('layouts.app')
@section('content')

    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-book"></i> All Books
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('book.create') }}">
                        <i class="fas fa-plus-circle"></i> Add New Book
                    </a>
                </div>
            </div>

            <!-- Advanced Search Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div class="card-header" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; border: none;">
                            <h5 style="margin: 0; font-weight: 600;">
                                <i class="fas fa-search"></i> Advanced Search
                            </h5>
                        </div>
                        <div class="card-body" style="background: #ffffff;">
                            <form method="GET" action="{{ route('books') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search (Name/ISBN/Author)</label>
                                            <input type="text" name="search" class="form-control" 
                                                value="{{ $filters['search'] ?? '' }}" placeholder="Search...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="form-control">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" 
                                                        {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Author</label>
                                            <select name="author" class="form-control">
                                                <option value="">All Authors</option>
                                                @foreach($authors as $auth)
                                                    <option value="{{ $auth->id }}" 
                                                        {{ ($filters['author'] ?? '') == $auth->id ? 'selected' : '' }}>
                                                        {{ $auth->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Publisher</label>
                                            <select name="publisher" class="form-control">
                                                <option value="">All Publishers</option>
                                                @foreach($publishers as $pub)
                                                    <option value="{{ $pub->id }}" 
                                                        {{ ($filters['publisher'] ?? '') == $pub->id ? 'selected' : '' }}>
                                                        {{ $pub->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="">All</option>
                                                <option value="available" {{ ($filters['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                                                <option value="unavailable" {{ ($filters['status'] ?? '') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.75rem;">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="message"></div>
                    <table class="content-table">
                        <thead>
                            <th>S.No</th>
                            <th>Cover</th>
                            <th>Book Name</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Publisher</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>
                        <tbody>
                            @forelse ($books as $book)
                                <tr>
                                    <td class="id">{{ $book->id }}</td>
                                    <td>
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                                alt="{{ $book->name }}" style="width: 50px; height: 70px; object-fit: cover;">
                                        @else
                                            <div style="width: 50px; height: 70px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                <small>No Image</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $book->name }}</td>
                                    <td>{{ $book->isbn ?? 'N/A' }}</td>
                                    <td>{{ $book->category->name }}</td>
                                    <td>{{ $book->auther->name }}</td>
                                    <td>{{ $book->publisher->name }}</td>
                                    <td>
                                        <small>Total: {{ $book->total_quantity ?? 1 }}</small><br>
                                        <small>Available: {{ $book->available_quantity ?? 0 }}</small><br>
                                        <small>Issued: {{ $book->issued_quantity ?? 0 }}</small>
                                    </td>
                                    <td>
                                        @if(($book->available_quantity ?? 0) > 0)
                                            <span class='badge badge-success'>Available</span>
                                        @else
                                            <span class='badge badge-danger'>Unavailable</span>
                                        @endif
                                    </td>
                                    <td class="edit">
                                        <a href="{{ route('book.edit', $book) }}" class="btn btn-success">Edit</a>
                                    </td>
                                    <td class="delete">
                                        <form action="{{ route('book.destroy', $book) }}" method="post" class="form-hidden">
                                            <button class="btn btn-danger delete-book">Delete</button>
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11">No Books Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $books->links('vendor/pagination/bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
