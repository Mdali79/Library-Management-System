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
                    @if(auth()->user()->role == 'Admin')
                        <a class="add-new" href="{{ route('book.create') }}">
                            <i class="fas fa-plus-circle"></i> Add New Book
                        </a>
                    @endif
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
                            <form method="GET" action="{{ route('books') }}" id="book-search-form">
                                <!-- Active Filters Display -->
                                @if(!empty(array_filter($filters ?? [])))
                                <div class="mb-3" id="active-filters">
                                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                        <small class="text-muted" style="font-weight: 600;">Active Filters:</small>
                                        @if(!empty($filters['search']))
                                        <span class="badge badge-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Search: {{ $filters['search'] }}
                                            <button type="button" class="btn-close-filter" data-filter="search" style="background: none; border: none; color: white; margin-left: 6px; font-size: 1rem; cursor: pointer; opacity: 0.8;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">×</button>
                                        </span>
                                        @endif
                                        @if(!empty($filters['category']))
                                        <span class="badge badge-info" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Category: {{ $categories->where('id', $filters['category'])->first()->name ?? 'N/A' }}
                                            <button type="button" class="btn-close-filter" data-filter="category" style="background: none; border: none; color: white; margin-left: 6px; font-size: 1rem; cursor: pointer; opacity: 0.8;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">×</button>
                                        </span>
                                        @endif
                                        @if(!empty($filters['author']))
                                        <span class="badge badge-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Author: {{ $authors->where('id', $filters['author'])->first()->name ?? 'N/A' }}
                                            <button type="button" class="btn-close-filter" data-filter="author" style="background: none; border: none; color: white; margin-left: 6px; font-size: 1rem; cursor: pointer; opacity: 0.8;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">×</button>
                                        </span>
                                        @endif
                                        @if(!empty($filters['publisher']))
                                        <span class="badge badge-warning" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Publisher: {{ $publishers->where('id', $filters['publisher'])->first()->name ?? 'N/A' }}
                                            <button type="button" class="btn-close-filter" data-filter="publisher" style="background: none; border: none; color: white; margin-left: 6px; font-size: 1rem; cursor: pointer; opacity: 0.8;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">×</button>
                                        </span>
                                        @endif
                                        @if(!empty($filters['status']))
                                        <span class="badge badge-{{ $filters['status'] == 'available' ? 'success' : 'danger' }}" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Status: {{ ucfirst($filters['status']) }}
                                            <button type="button" class="btn-close-filter" data-filter="status" style="background: none; border: none; color: white; margin-left: 6px; font-size: 1rem; cursor: pointer; opacity: 0.8;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">×</button>
                                        </span>
                                        @endif
                                        <a href="{{ route('books') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.85rem;">
                                            <i class="fas fa-times"></i> Clear All
                                        </a>
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group" style="position: relative;">
                                            <label>Search</label>
                                            <input type="text" name="search" id="book-search-input" class="form-control"
                                                value="{{ $filters['search'] ?? '' }}" placeholder="Search books, authors..." autocomplete="off">
                                            <div id="book-suggestions" class="suggestions-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="form-control search-select">
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
                                            <select name="author" class="form-control search-select">
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
                                            <select name="publisher" class="form-control search-select">
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
                                            <select name="status" class="form-control search-select">
                                                <option value="">All Status</option>
                                                <option value="available" {{ ($filters['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                                                <option value="unavailable" {{ ($filters['status'] ?? '') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="d-flex" style="gap: 5px;">
                                                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 0; flex: 1;">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                                @if(!empty(array_filter($filters ?? [])))
                                                <a href="{{ route('books') }}" class="btn btn-outline-secondary" style="margin-top: 0; padding: 0.5rem 0.75rem;" title="Clear all filters">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                @endif
                                            </div>
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
                            @if(auth()->user()->role == 'Admin')
                                <th>Edit</th>
                                <th>Delete</th>
                            @endif
                        </thead>
                        <tbody>
                            @forelse ($books as $book)
                                <tr>
                                    <td class="id">{{ $book->id }}</td>
                                    <td>
                                        @if($book->cover_image)
                                            <img src="{{ $book->cover_image_url }}"
                                                alt="{{ $book->name }}" style="width: 50px; height: 70px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'70\'%3E%3Crect width=\'50\' height=\'70\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'10\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                                        @else
                                            <div style="width: 50px; height: 70px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                <small>No Image</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $book->name }}</td>
                                    <td>{{ $book->isbn ?? 'N/A' }}</td>
                                    <td>{{ $book->category->name }}</td>
                                    <td style="white-space: nowrap;">
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
                                                    <span style="margin: 0 3px;">,</span>
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
                                    @if(auth()->user()->role == 'Admin')
                                        <td class="edit">
                                            <a href="{{ route('book.edit', $book) }}" class="btn btn-success">Edit</a>
                                        </td>
                                        <td class="delete">
                                            <form action="{{ route('book.destroy', $book) }}" method="post" class="form-hidden">
                                                <button class="btn btn-danger delete-book">Delete</button>
                                                @csrf
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role == 'Admin' ? '11' : '9' }}">No Books Found</td>
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
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1050;
            width: 100%;
            min-width: 350px;
            max-width: 600px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 4px;
            top: 100%;
            left: 0;
        }
        .suggestion-item {
            padding: 12px 18px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            min-height: 48px;
        }
        .suggestion-item:hover,
        .suggestion-item.highlighted {
            background: linear-gradient(90deg, #e7f3ff 0%, #f0f8ff 100%);
            border-left: 4px solid #2563eb;
            padding-left: 14px;
            transform: translateX(2px);
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }
        .suggestion-icon {
            margin-right: 12px;
            font-size: 1.2em;
            width: 24px;
            text-align: center;
        }
        .suggestion-text {
            flex: 1;
            font-size: 0.95rem;
            color: #333;
            font-weight: 500;
        }
        .suggestion-type {
            font-size: 0.75em;
            color: #6c757d;
            margin-left: auto;
            text-transform: capitalize;
            padding: 4px 8px;
            background: #f8f9fa;
            border-radius: 12px;
            font-weight: 500;
        }
        .suggestions-header {
            padding: 10px 18px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 700;
            font-size: 0.85em;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .suggestions-dropdown::-webkit-scrollbar {
            width: 8px;
        }
        .suggestions-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .suggestions-dropdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .suggestions-dropdown::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Styles to ensure dropdown selected text stays on one line and align with search input */
        .search-select,
        select.search-select,
        select.form-control.search-select,
        .form-group select.search-select {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            padding-right: 2rem !important;
            padding-left: 0.5rem !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            min-height: 2.5rem !important;
            height: 2.5rem !important;
            line-height: 1.5 !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            background-position: right 0.5rem center !important;
            background-size: 1rem 1rem !important;
            font-size: 0.875rem !important;
            text-indent: 0 !important;
        }

        /* Ensure search input and button align with dropdowns */
        #book-search-input {
            height: 2.5rem !important;
            line-height: 1.5 !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }

        /* Align form groups vertically and ensure consistent spacing */
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            height: 1.5rem;
            line-height: 1.5rem;
        }

        /* Ensure all inputs and selects align at the same baseline */
        .form-group input,
        .form-group select,
        .form-group button {
            margin-top: 0;
        }

        /* Make sure button height matches input/select height */
        .form-group button.btn-block {
            height: 2.5rem !important;
            line-height: 1.5 !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }

        /* Dropdown options can wrap if needed */
        .search-select option {
            white-space: normal !important;
            word-wrap: break-word !important;
            padding: 0.5rem !important;
        }

        .search-select:focus,
        .search-select:active,
        .search-select:hover {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
    </style>

    <script>
        const bookSearchInput = document.getElementById('book-search-input');
        const bookSuggestionsDiv = document.getElementById('book-suggestions');
        let suggestionTimeout = null;
        const suggestionsUrl = '{{ route("book.suggestions") }}';

        // Show suggestions on focus
        bookSearchInput.addEventListener('focus', function() {
            const searchTerm = this.value.trim();
            if (searchTerm.length > 0) {
                fetchSuggestions(searchTerm);
            } else {
                fetchSuggestions('');
            }
        });

        // Fetch suggestions as user types - immediate response
        bookSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();

            // Clear previous timeout
            if (suggestionTimeout) {
                clearTimeout(suggestionTimeout);
            }

            // If user just started typing (first character), show suggestions immediately
            if (searchTerm.length === 1) {
                fetchSuggestions(searchTerm);
            } else if (searchTerm.length > 1) {
                // For subsequent characters, use shorter debounce (150ms) for faster response
                suggestionTimeout = setTimeout(function() {
                    fetchSuggestions(searchTerm);
                }, 150);
            } else {
                // If input is cleared, show popular suggestions
                fetchSuggestions('');
            }
        });

        // Also trigger on keydown for immediate feedback
        bookSearchInput.addEventListener('keydown', function(e) {
            // Don't interfere with arrow keys, enter, escape
            if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
                return;
            }

            const searchTerm = this.value.trim();
            // If user is typing a character, show suggestions immediately
            if (searchTerm.length >= 1 && e.key.length === 1) {
                clearTimeout(suggestionTimeout);
                suggestionTimeout = setTimeout(function() {
                    fetchSuggestions(searchTerm + e.key);
                }, 100);
            }
        });

        // Fetch suggestions from server
        function fetchSuggestions(searchTerm) {
            const url = suggestionsUrl + (searchTerm ? '?q=' + encodeURIComponent(searchTerm) : '');

            fetch(url)
                .then(response => response.json())
                .then(suggestions => {
                    if (suggestions && suggestions.length > 0) {
                        displayBookSuggestions(suggestions);
                    } else {
                        bookSuggestionsDiv.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    bookSuggestionsDiv.style.display = 'none';
                });
        }

        function displayBookSuggestions(suggestions) {
            bookSuggestionsDiv.innerHTML = '';

            if (!suggestions || suggestions.length === 0) {
                bookSuggestionsDiv.style.display = 'none';
                return;
            }

            // Group suggestions by type
            const grouped = {};
            suggestions.forEach(item => {
                const type = item.type || 'other';
                if (!grouped[type]) {
                    grouped[type] = [];
                }
                grouped[type].push(item);
            });

            // Type labels mapping
            const typeLabels = {
                'book': '📚 Books',
                'author': '✍️ Authors',
                'category': '📂 Categories',
                'publisher': '🏢 Publishers',
                'isbn': '🔢 ISBN'
            };

            // Display grouped suggestions with headers
            Object.keys(grouped).forEach(type => {
                const items = grouped[type];

                // Add header for each type
                const header = document.createElement('div');
                header.className = 'suggestions-header';
                header.textContent = typeLabels[type] || type.charAt(0).toUpperCase() + type.slice(1);
                bookSuggestionsDiv.appendChild(header);

                items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';

                    const icon = document.createElement('span');
                    icon.className = 'suggestion-icon';
                    icon.textContent = item.icon || '🔍';

                    const text = document.createElement('span');
                    text.className = 'suggestion-text';
                    text.textContent = item.text;

                    div.appendChild(icon);
                    div.appendChild(text);

                    div.addEventListener('click', function() {
                        bookSearchInput.value = item.text;
                        bookSearchInput.focus();
                        bookSuggestionsDiv.style.display = 'none';
                        // Don't auto-submit - let user review and click search button
                    });

                    bookSuggestionsDiv.appendChild(div);
                });
            });

            bookSuggestionsDiv.style.display = 'block';
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!bookSearchInput.contains(e.target) && !bookSuggestionsDiv.contains(e.target)) {
                bookSuggestionsDiv.style.display = 'none';
            }
        });

        // Handle keyboard navigation
        bookSearchInput.addEventListener('keydown', function(e) {
            const visibleSuggestions = bookSuggestionsDiv.querySelectorAll('.suggestion-item');
            const highlighted = bookSuggestionsDiv.querySelector('.suggestion-item.highlighted');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (highlighted) {
                    highlighted.classList.remove('highlighted');
                    const next = highlighted.nextElementSibling;
                    if (next) {
                        next.classList.add('highlighted');
                    } else if (visibleSuggestions.length > 0) {
                        visibleSuggestions[0].classList.add('highlighted');
                    }
                } else if (visibleSuggestions.length > 0) {
                    visibleSuggestions[0].classList.add('highlighted');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (highlighted) {
                    highlighted.classList.remove('highlighted');
                    const prev = highlighted.previousElementSibling;
                    if (prev) {
                        prev.classList.add('highlighted');
                    }
                }
            } else if (e.key === 'Enter') {
                if (highlighted) {
                    e.preventDefault();
                    highlighted.click();
                } else {
                    // Submit form on Enter if no suggestion is highlighted
                    document.getElementById('book-search-form').submit();
                }
            } else if (e.key === 'Escape') {
                bookSuggestionsDiv.style.display = 'none';
            }
        });

        // Handle filter removal
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-close-filter').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const filterName = this.getAttribute('data-filter');
                    const form = document.getElementById('book-search-form');

                    if (filterName === 'search') {
                        document.getElementById('book-search-input').value = '';
                    } else {
                        const select = form.querySelector('[name="' + filterName + '"]');
                        if (select) {
                            select.value = '';
                        }
                    }

                    form.submit();
                });
            });
        });
    </script>

    <style>
        .suggestion-item.highlighted {
            background-color: #e9ecef;
        }
    </style>
@endsection
