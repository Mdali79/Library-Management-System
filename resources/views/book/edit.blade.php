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
                                    <label>Authors <span class="text-danger">*</span></label>
                                    <div id="authors-container">
                                        @php
                                            $existingAuthors = $book->authors ?? collect();
                                            $oldAuthors = old('authors', []);
                                            $authorsToShow = !empty($oldAuthors) ? $oldAuthors : $existingAuthors->map(function($author) {
                                                return [
                                                    'id' => $author->id,
                                                    'is_main' => $author->pivot->is_main_author ?? false,
                                                    'is_corresponding' => $author->pivot->is_corresponding_author ?? false,
                                                ];
                                            })->toArray();
                                        @endphp
                                        @if(count($authorsToShow) > 0)
                                            @foreach($authorsToShow as $index => $authorData)
                                                <div class="author-row mb-3" data-row-index="{{ $index }}">
                                                    <div class="card" style="border: 1px solid #dee2e6; border-radius: 0.5rem;">
                                                        <div class="card-body p-3">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-12 mb-2">
                                                                    <label class="small text-muted mb-1">Author</label>
                                                                    <select class="form-control author-select" name="authors[{{ $index }}][id]" required style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 4.5rem !important;">
                                                                        <option value="">Select Author</option>
                                                                        @foreach ($authors as $author)
                                                                            <option value="{{ $author->id }}" 
                                                                                {{ (isset($authorData['id']) && $authorData['id'] == $author->id) ? 'selected' : '' }}>
                                                                                {{ $author->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input main-author-radio" type="radio" name="main_author" value="{{ $index }}" id="main_author_{{ $index }}" 
                                                                            {{ (!empty($authorData['is_main']) || ($index == 0 && empty($authorsToShow[0]['is_main']))) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="main_author_{{ $index }}">
                                                                            <i class="fas fa-star text-warning"></i> Main Author
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input corresponding-author-checkbox" type="checkbox" name="authors[{{ $index }}][is_corresponding]" value="1" id="corresponding_{{ $index }}"
                                                                            {{ !empty($authorData['is_corresponding']) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="corresponding_{{ $index }}">
                                                                            <i class="fas fa-envelope text-info"></i> Corresponding
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1 text-right">
                                                                    @if($index > 0)
                                                                        <button type="button" class="btn btn-sm btn-danger remove-author-btn" onclick="removeAuthorRow(this)" title="Remove Author">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="author-row mb-3" data-row-index="0">
                                                <div class="card" style="border: 1px solid #dee2e6; border-radius: 0.5rem;">
                                                    <div class="card-body p-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-12 mb-2">
                                                                <label class="small text-muted mb-1">Author</label>
                                                                <select class="form-control author-select" name="authors[0][id]" required style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 4.5rem !important;">
                                                                    <option value="">Select Author</option>
                                                                    @foreach ($authors as $author)
                                                                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input main-author-radio" type="radio" name="main_author" value="0" id="main_author_0" checked>
                                                                    <label class="form-check-label" for="main_author_0">
                                                                        <i class="fas fa-star text-warning"></i> Main Author
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input corresponding-author-checkbox" type="checkbox" name="authors[0][is_corresponding]" value="1" id="corresponding_0">
                                                                    <label class="form-check-label" for="corresponding_0">
                                                                        <i class="fas fa-envelope text-info"></i> Corresponding
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-author-btn">
                                        <i class="fas fa-plus"></i> Add Another Author
                                    </button>
                                    @error('authors')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    @error('authors.*')
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

    <script>
        let authorRowIndex = {{ count($authorsToShow ?? []) }};
        const allAuthors = @json($authors);

        document.getElementById('add-author-btn').addEventListener('click', function() {
            const container = document.getElementById('authors-container');
            const newRow = document.createElement('div');
            newRow.className = 'author-row mb-3';
            newRow.setAttribute('data-row-index', authorRowIndex);
            
            newRow.innerHTML = `
                <div class="card" style="border: 1px solid #dee2e6; border-radius: 0.5rem;">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-12 mb-2">
                                <label class="small text-muted mb-1">Author</label>
                                <select class="form-control author-select" name="authors[${authorRowIndex}][id]" required style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 4.5rem !important;">
                                    <option value="">Select Author</option>
                                    ${allAuthors.map(author => `<option value="${author.id}">${author.name}</option>`).join('')}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input main-author-radio" type="radio" name="main_author" value="${authorRowIndex}" id="main_author_${authorRowIndex}">
                                    <label class="form-check-label" for="main_author_${authorRowIndex}">
                                        <i class="fas fa-star text-warning"></i> Main Author
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-check">
                                    <input class="form-check-input corresponding-author-checkbox" type="checkbox" name="authors[${authorRowIndex}][is_corresponding]" value="1" id="corresponding_${authorRowIndex}">
                                    <label class="form-check-label" for="corresponding_${authorRowIndex}">
                                        <i class="fas fa-envelope text-info"></i> Corresponding
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-sm btn-danger remove-author-btn" onclick="removeAuthorRow(this)" title="Remove Author">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
            updateAuthorOptions();
            authorRowIndex++;
        });

        function removeAuthorRow(btn) {
            const row = btn.closest('.author-row');
            const rowIndex = row.getAttribute('data-row-index');
            
            // Check if this is the main author
            const isMainAuthor = row.querySelector('.main-author-radio').checked;
            
            // If removing main author, set first remaining author as main
            if (isMainAuthor) {
                const remainingRows = document.querySelectorAll('.author-row');
                if (remainingRows.length > 1) {
                    remainingRows[0].querySelector('.main-author-radio').checked = true;
                }
            }
            
            row.remove();
            updateAuthorOptions();
        }

        function updateAuthorOptions() {
            const rows = document.querySelectorAll('.author-row');
            const selectedAuthors = [];
            
            rows.forEach(row => {
                const select = row.querySelector('.author-select');
                if (select.value) {
                    selectedAuthors.push(select.value);
                }
            });
            
            rows.forEach(row => {
                const select = row.querySelector('.author-select');
                const currentValue = select.value;
                const options = select.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value === '' || option.value === currentValue) {
                        option.style.display = '';
                    } else if (selectedAuthors.includes(option.value)) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = '';
                    }
                });
            });
        }

        // Update author options when selection changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('author-select')) {
                updateAuthorOptions();
            }
        });

        // Ensure only one main author is selected
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('main-author-radio') && e.target.checked) {
                document.querySelectorAll('.main-author-radio').forEach(radio => {
                    if (radio !== e.target) {
                        radio.checked = false;
                    }
                });
            }
        });

        // Ensure only one corresponding author is selected
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('corresponding-author-checkbox') && e.target.checked) {
                document.querySelectorAll('.corresponding-author-checkbox').forEach(checkbox => {
                    if (checkbox !== e.target) {
                        checkbox.checked = false;
                    }
                });
            }
        });

        // Before form submission, set hidden fields for main author and ensure all data is correct
        document.querySelector('form').addEventListener('submit', function(e) {
            // Remove any existing hidden inputs to avoid duplicates
            document.querySelectorAll('input[name*="[is_main]"]').forEach(input => {
                if (input.type === 'hidden') {
                    input.remove();
                }
            });
            
            const mainAuthorRadio = document.querySelector('.main-author-radio:checked');
            if (mainAuthorRadio) {
                const mainIndex = mainAuthorRadio.value;
                const mainRow = document.querySelector(`.author-row[data-row-index="${mainIndex}"]`);
                if (mainRow) {
                    // Check if author is selected
                    const authorSelect = mainRow.querySelector('.author-select');
                    if (authorSelect && authorSelect.value) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `authors[${mainIndex}][is_main]`;
                        hiddenInput.value = '1';
                        mainRow.appendChild(hiddenInput);
                    } else {
                        e.preventDefault();
                        alert('Please select an author for the Main Author field.');
                        authorSelect.focus();
                        return false;
                    }
                } else {
                    e.preventDefault();
                    alert('Please ensure at least one author is marked as Main Author.');
                    return false;
                }
            } else {
                e.preventDefault();
                alert('Please select at least one Main Author.');
                return false;
            }
            
            // Ensure all authors have IDs
            const allAuthorRows = document.querySelectorAll('.author-row');
            let hasError = false;
            allAuthorRows.forEach(row => {
                const select = row.querySelector('.author-select');
                if (!select || !select.value) {
                    hasError = true;
                    select.classList.add('is-invalid');
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Please select an author for all author fields.');
                return false;
            }
        });

        // Initialize author options on page load
        updateAuthorOptions();
    </script>
    
    <style>
        .author-row {
            margin-bottom: 1rem;
        }
        .author-row .card {
            transition: all 0.3s ease;
        }
        .author-row .card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-color: #2563eb !important;
        }
        .author-row .form-check {
            margin-bottom: 0;
            padding: 0.5rem 0;
        }
        .author-row .form-check-label {
            font-size: 0.9rem;
            white-space: nowrap;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .author-row .form-check-input {
            margin-top: 0.25rem;
        }
        .author-select {
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: normal !important;
            padding-right: 4.5rem !important;
            min-height: 2.5rem !important;
        }
        #authors-container {
            margin-bottom: 1rem;
        }
        .remove-author-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endsection
