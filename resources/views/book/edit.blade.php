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
                                                ];
                                            })->toArray();
                                        @endphp
                                        @if(count($authorsToShow) > 0)
                                            @foreach($authorsToShow as $index => $authorData)
                                                <div class="author-row mb-3" data-row-index="{{ $index }}">
                                                    <div class="card" style="border: 1px solid #dee2e6; border-radius: 0.5rem;">
                                                        <div class="card-body p-3">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-10">
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
                                                                <div class="col-md-2 text-right">
                                                                    <button type="button" class="btn btn-sm btn-danger remove-author-btn mt-4" onclick="removeAuthorRow(this)" title="Remove Author" style="{{ $index == 0 && count($authorsToShow) == 1 ? 'display: none;' : '' }}">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
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
                                                            <div class="col-md-10">
                                                                <label class="small text-muted mb-1">Author</label>
                                                                <select class="form-control author-select" name="authors[0][id]" required style="overflow: visible !important; text-overflow: clip !important; white-space: normal !important; padding-right: 4.5rem !important;">
                                                                    <option value="">Select Author</option>
                                                                    @foreach ($authors as $author)
                                                                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 text-right">
                                                                <button type="button" class="btn btn-sm btn-danger remove-author-btn mt-4" onclick="removeAuthorRow(this)" title="Remove Author" style="display: none;">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
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
                                    <img src="{{ $book->cover_image_url }}?v={{ time() }}"
                                        alt="{{ $book->name }}" style="max-width: 200px; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;"
                                        onerror="this.onerror=null; this.src='{{ asset('images/default-book-cover.png') }}';">
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
                        <div class="form-group">
                            <label>PDF File (for Online Reading)</label>
                            @if($book->pdf_file)
                                <div class="mb-2">
                                    <p class="text-muted">
                                        <i class="fas fa-file-pdf text-danger"></i> Current PDF:
                                        <a href="{{ route('reading.show', $book->id) }}" target="_blank" class="text-primary">
                                            View PDF
                                        </a>
                                    </p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('pdf_file') is-invalid @enderror"
                                name="pdf_file" accept=".pdf,application/pdf">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Leave empty to keep current PDF. Max size: 50MB.
                                Only PDF files allowed. Students will be able to read the first few chapters online.
                            </small>
                            @error('pdf_file')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Preview Pages</label>
                            <input type="number" class="form-control @error('preview_pages') is-invalid @enderror"
                                name="preview_pages" value="{{ old('preview_pages', $book->preview_pages ?? 50) }}" min="1" max="500">
                            <small class="form-text text-muted">
                                Number of pages students can read in preview mode (default: 50 pages)
                            </small>
                            @error('preview_pages')
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
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" class="btn btn-sm btn-danger remove-author-btn mt-4" onclick="removeAuthorRow(this)" title="Remove Author">
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
            const container = document.getElementById('authors-container');
            const rows = container.querySelectorAll('.author-row');

            // Don't allow removing if only one author row remains
            if (rows.length <= 1) {
                alert('At least one author is required.');
                return;
            }

            row.remove();
            updateAuthorOptions();

            // Show/hide remove buttons based on number of rows
            const remainingRows = container.querySelectorAll('.author-row');
            remainingRows.forEach((r, index) => {
                const removeBtn = r.querySelector('.remove-author-btn');
                if (removeBtn) {
                    removeBtn.style.display = remainingRows.length > 1 ? 'block' : 'none';
                }
            });
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

        // Before form submission, ensure all authors have IDs
        document.querySelector('form').addEventListener('submit', function(e) {
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

        // Show/hide remove buttons on page load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('authors-container');
            const rows = container.querySelectorAll('.author-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-author-btn');
                if (removeBtn) {
                    removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
                }
            });
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
