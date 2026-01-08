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
                                        <div class="form-group" style="position: relative;">
                                            <label>Search (Name/ISBN/Author)</label>
                                            <input type="text" name="search" id="book-search-input" class="form-control"
                                                value="{{ $filters['search'] ?? '' }}" placeholder="Search..." autocomplete="off">
                                            <div id="book-suggestions" class="suggestions-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="form-control search-select" style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 5rem !important; min-height: 2.75rem !important;">
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
                                            <select name="author" class="form-control search-select" style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 5rem !important; min-height: 2.75rem !important;">
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
                                            <select name="publisher" class="form-control search-select" style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 5rem !important; min-height: 2.75rem !important;">
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
                                            <select name="status" class="form-control search-select" style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 5rem !important; min-height: 2.75rem !important;">
                                                <option value="">All Status</option>
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
                                    <td>
                                        @php
                                            $bookAuthors = $book->authors ?? collect();
                                            if ($bookAuthors->isEmpty() && $book->auther) {
                                                // Fallback to old single author for backward compatibility
                                                $bookAuthors = collect([$book->auther]);
                                            }
                                        @endphp
                                        @if($bookAuthors->count() > 0)
                                            @foreach($bookAuthors as $index => $author)
                                                <span>{{ $author->name }}</span>
                                                @if(!$loop->last)
                                                    <br>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
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

    <style>
        .suggestions-dropdown {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }

        /* Additional inline styles to ensure dropdown text is fully visible */
        .search-select,
        select.search-select,
        select.form-control.search-select,
        .form-group select.search-select {
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            padding-right: 5rem !important;
            padding-left: 1rem !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            min-height: 2.75rem !important;
            height: auto !important;
            line-height: 1.5 !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            background-position: right 0.75rem center !important;
            font-size: 1rem !important;
            text-indent: 0 !important;
        }

        .search-select:focus,
        .search-select:active,
        .search-select:hover,
        select.search-select:focus,
        select.search-select:active,
        select.search-select:hover {
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: normal !important;
            padding-right: 5rem !important;
        }
    </style>

    <script>
        const bookSearchInput = document.getElementById('book-search-input');
        const bookSuggestionsDiv = document.getElementById('book-suggestions');
        const bookSuggestions = @json($suggestions ?? []);

        bookSearchInput.addEventListener('focus', function() {
            if (bookSuggestions.length > 0) {
                showBookSuggestions();
            }
        });

        bookSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            if (searchTerm.length > 0) {
                const filtered = bookSuggestions.filter(s => s.toLowerCase().includes(searchTerm));
                if (filtered.length > 0) {
                    displayBookSuggestions(filtered);
                } else {
                    bookSuggestionsDiv.style.display = 'none';
                }
            } else {
                showBookSuggestions();
            }
        });

        function showBookSuggestions() {
            displayBookSuggestions(bookSuggestions);
        }

        function displayBookSuggestions(items) {
            bookSuggestionsDiv.innerHTML = '';
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = item;
                div.addEventListener('click', function() {
                    bookSearchInput.value = item;
                    bookSuggestionsDiv.style.display = 'none';
                    document.querySelector('form[method="GET"]').submit();
                });
                bookSuggestionsDiv.appendChild(div);
            });
            bookSuggestionsDiv.style.display = 'block';
        }

        document.addEventListener('click', function(e) {
            if (!bookSearchInput.contains(e.target) && !bookSuggestionsDiv.contains(e.target)) {
                bookSuggestionsDiv.style.display = 'none';
            }
        });
    </script>
@endsection
