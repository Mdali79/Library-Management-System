@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">All Authors</h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('authors.create') }}">Add Author</a>
                </div>
            </div>
            
            <!-- Search Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div class="card-body" style="background: #ffffff;">
                            <form method="GET" action="{{ route('authors') }}" id="author-search-form">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>Search Authors</label>
                                            <input type="text" name="search" id="author-search-input" class="form-control" 
                                                value="{{ $filters['search'] ?? '' }}" placeholder="Search by author name..." autocomplete="off">
                                            <div id="author-suggestions" class="suggestions-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> Search
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
                            <th>Author Name</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>
                        <tbody>
                            @forelse ($authors as $auther)
                                <tr>
                                    <td>{{ $auther->id }}</td>
                                    <td>{{ $auther->name }}</td>
                                    <td class="edit">
                                        <a href="{{ route('authors.edit', $auther) }}" class="btn btn-success">Edit</a>
                                    </td>
                                    <td class="delete">
                                        <form action="{{ route('authors.destroy', $auther->id) }}" method="post"
                                            class="form-hidden">
                                            <button class="btn btn-danger delete-author">Delete</button>
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No Authors Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $authors->links('vendor/pagination/bootstrap-4') }}
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
    </style>

    <script>
        const authorSearchInput = document.getElementById('author-search-input');
        const suggestionsDiv = document.getElementById('author-suggestions');
        const suggestions = @json($suggestions ?? []);
        
        // Show suggestions on focus or input
        authorSearchInput.addEventListener('focus', function() {
            if (suggestions.length > 0) {
                showSuggestions();
            }
        });
        
        authorSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            if (searchTerm.length > 0) {
                // Filter suggestions based on input
                const filtered = suggestions.filter(s => s.toLowerCase().includes(searchTerm));
                if (filtered.length > 0) {
                    displaySuggestions(filtered);
                } else {
                    suggestionsDiv.style.display = 'none';
                }
            } else {
                showSuggestions();
            }
        });
        
        function showSuggestions() {
            displaySuggestions(suggestions);
        }
        
        function displaySuggestions(items) {
            suggestionsDiv.innerHTML = '';
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = item;
                div.addEventListener('click', function() {
                    authorSearchInput.value = item;
                    suggestionsDiv.style.display = 'none';
                    document.getElementById('author-search-form').submit();
                });
                suggestionsDiv.appendChild(div);
            });
            suggestionsDiv.style.display = 'block';
        }
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!authorSearchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.style.display = 'none';
            }
        });
    </script>
@endsection
