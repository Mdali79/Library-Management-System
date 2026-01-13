@extends('layouts.app')
@section('content')

    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2 class="admin-heading">
                        <i class="fas fa-book-reader"></i> Read Books Online - CSE Department
                    </h2>
                    <p class="text-muted">Browse and read the first few chapters of CSE department books online</p>
                </div>
            </div>

            <!-- Search Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('reading.index') }}">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <input type="text" name="search" class="form-control"
                                                value="{{ request('search') }}"
                                                placeholder="Search by book name, author, or ISBN...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-block" style="background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); white-space: nowrap; overflow: visible;">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Books Grid -->
            @if($books->count() > 0)
                <div class="row">
                    @foreach($books as $book)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s;">
                                <div class="card-body">
                                    <!-- Book Cover -->
                                    <div class="text-center mb-3">
                                        @if($book->cover_image)
                                            <img src="{{ $book->cover_image_url }}?v={{ time() }}"
                                                alt="{{ $book->name }}"
                                                style="max-width: 150px; max-height: 200px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <div style="width: 150px; height: 200px; background: #f0f0f0; display: inline-flex; align-items: center; justify-content: center; border-radius: 4px;">
                                                <i class="fas fa-book" style="font-size: 48px; color: #999;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Book Title -->
                                    <h5 class="card-title" style="font-size: 16px; font-weight: 600; margin-bottom: 10px; min-height: 48px;">
                                        {{ \Str::limit($book->name, 50) }}
                                    </h5>

                                    <!-- Author -->
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-user"></i>
                                        @php
                                            $allAuthors = $book->authors ?? collect();
                                            if ($allAuthors->isEmpty() && $book->auther) {
                                                $allAuthors = collect([$book->auther]);
                                            }
                                            $authorsList = $allAuthors->map(function ($author) {
                                                return $author->name;
                                            })->join(', ');
                                        @endphp
                                        {{ $authorsList ?: 'N/A' }}
                                    </p>

                                    <!-- Category -->
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-tag"></i> {{ $book->category ? $book->category->name : 'N/A' }}
                                    </p>

                                    <!-- Preview Pages Info -->
                                    <p class="text-info small mb-3">
                                        <i class="fas fa-eye"></i> Preview: {{ $book->preview_pages ?? 50 }} pages
                                    </p>

                                    <!-- Read Online Button -->
                                    <a href="{{ route('reading.show', $book->id) }}" class="btn btn-block" style="background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                        <i class="fas fa-book-reader"></i> Read Online
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        {{ $books->links() }}
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    @if(request('search'))
                        No books found matching your search. <a href="{{ route('reading.index') }}">View all books</a>
                    @else
                        No CSE books with PDF files available for online reading at the moment.
                    @endif
                </div>
            @endif
        </div>
    </div>

@endsection

