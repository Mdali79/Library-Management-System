@extends('layouts.app')
@section('content')

    <div id="admin-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('reading.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Books
                    </a>
                    <h3 class="d-inline-block ml-3">
                        <i class="fas fa-book-reader"></i> {{ $book->name }}
                    </h3>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <!-- PDF Viewer -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-pdf"></i> Book Preview
                                    </h5>
                                </div>
                                <div class="col-md-6 text-right">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-light" id="prev-page" title="Previous Page">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span class="btn btn-sm btn-light" id="page-info">
                                            Page <span id="page-num">1</span> of <span id="page-count">--</span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-light" id="next-page" title="Next Page">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light" id="zoom-out" title="Zoom Out">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <span class="btn btn-sm btn-light" id="zoom-level">100%</span>
                                        <button type="button" class="btn btn-sm btn-light" id="zoom-in" title="Zoom In">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="background: #525252; min-height: 600px;">
                            <div id="pdf-container" style="overflow: auto; height: 600px; text-align: center; padding: 20px;">
                                <div id="pdf-loader" class="text-white">
                                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                                    <p class="mt-3">Loading PDF...</p>
                                </div>
                                <canvas id="pdf-canvas" style="display: none; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="alert alert-warning mb-0" id="preview-limit-alert" style="display: none;">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Preview Limit Reached:</strong> You've reached the preview limit ({{ $previewPages }} pages). 
                                To continue reading, please <a href="{{ route('book_issue.create') }}" class="alert-link">request the full book</a> from the library.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Information Sidebar -->
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Book Information</h5>
                        </div>
                        <div class="card-body">
                            @if($book->cover_image)
                                <div class="text-center mb-3">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url('public/' . $book->cover_image) }}" 
                                        alt="{{ $book->name }}" 
                                        style="max-width: 100%; max-height: 300px; border-radius: 4px;">
                                </div>
                            @endif

                            <p><strong>Title:</strong><br>{{ $book->name }}</p>

                            <p><strong>Author(s):</strong><br>
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

                            @if($book->category)
                                <p><strong>Category:</strong><br>{{ $book->category->name }}</p>
                            @endif

                            @if($book->publisher)
                                <p><strong>Publisher:</strong><br>{{ $book->publisher->name }}</p>
                            @endif

                            @if($book->isbn)
                                <p><strong>ISBN:</strong><br>{{ $book->isbn }}</p>
                            @endif

                            @if($book->edition)
                                <p><strong>Edition:</strong><br>{{ $book->edition }}</p>
                            @endif

                            <p><strong>Preview Pages:</strong><br>{{ $previewPages }} pages</p>

                            <p><strong>Availability:</strong><br>
                                @if($book->available_quantity > 0)
                                    <span class="badge badge-success">Available ({{ $book->available_quantity }} copies)</span>
                                @else
                                    <span class="badge badge-danger">Not Available</span>
                                @endif
                            </p>

                            <hr>

                            <a href="{{ route('book_issue.create') }}" class="btn btn-danger btn-block">
                                <i class="fas fa-book"></i> Request Full Book
                            </a>
                        </div>
                    </div>

                    @if($book->description)
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-align-left"></i> Description</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $book->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        (function() {
            'use strict';
            
            // Set PDF.js worker
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }

            let pdfDoc = null;
            let pageNum = 1;
            let pageRendering = false;
            let pageNumPending = null;
            let scale = 1.0;
            const maxPreviewPages = {{ $previewPages }};
            const pdfUrl = '{{ route("reading.pdf", $book->id) }}';

            // Get canvas and context - wait for DOM to be ready
            let canvas = null;
            let ctx = null;
            
            // Initialize canvas when DOM is ready
            function initializeCanvas() {
                if (!canvas) {
                    canvas = document.getElementById('pdf-canvas');
                }
                if (!ctx && canvas) {
                    ctx = canvas.getContext('2d');
                }
                return canvas && ctx;
            }

        /**
         * Render a page
         */
        function renderPage(num) {
            if (!canvas || !ctx || !pdfDoc) {
                console.error('Canvas, context, or PDF document not initialized');
                return;
            }
            
            pageRendering = true;

            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }

                    // Update page info
                    document.getElementById('page-num').textContent = num;
                    document.getElementById('page-count').textContent = pdfDoc.numPages;

                    // Show/hide preview limit alert
                    if (num > maxPreviewPages) {
                        document.getElementById('preview-limit-alert').style.display = 'block';
                        // Don't render pages beyond preview limit
                        return;
                    } else {
                        document.getElementById('preview-limit-alert').style.display = 'none';
                    }
                });
            });

            // Update button states
            document.getElementById('prev-page').disabled = (num <= 1);
            document.getElementById('next-page').disabled = (num >= Math.min(pdfDoc.numPages, maxPreviewPages));
        }

        /**
         * Queue rendering of a page
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        /**
         * Show previous page
         */
        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        /**
         * Show next page
         */
        function onNextPage() {
            const maxPage = Math.min(pdfDoc.numPages, maxPreviewPages);
            if (pageNum >= maxPage) {
                // Show alert if trying to go beyond preview
                if (pageNum >= maxPreviewPages) {
                    document.getElementById('preview-limit-alert').style.display = 'block';
                }
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }

        /**
         * Zoom in
         */
        function zoomIn() {
            scale += 0.2;
            updateZoom();
            queueRenderPage(pageNum);
        }

        /**
         * Zoom out
         */
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.2;
            updateZoom();
            queueRenderPage(pageNum);
        }

        /**
         * Update zoom level display
         */
        function updateZoom() {
            document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
        }

        // Load PDF function
        function loadPDF() {
            // Ensure canvas is initialized
            if (!initializeCanvas()) {
                console.error('Failed to initialize canvas');
                const loader = document.getElementById('pdf-loader');
                if (loader) {
                    loader.innerHTML = 
                        '<div class="text-white"><i class="fas fa-exclamation-triangle fa-3x"></i><p class="mt-3">Failed to initialize PDF viewer.</p></div>';
                }
                return;
            }

            if (typeof pdfjsLib === 'undefined') {
                console.error('PDF.js library not loaded');
                const loader = document.getElementById('pdf-loader');
                if (loader) {
                    loader.innerHTML = 
                        '<div class="text-white"><i class="fas fa-exclamation-triangle fa-3x"></i><p class="mt-3">PDF.js library failed to load. Please refresh the page.</p></div>';
                }
                return;
            }

            // Load PDF
            pdfjsLib.getDocument({
                url: pdfUrl,
                withCredentials: false,
                httpHeaders: {}
            }).promise.then(function(pdf) {
                pdfDoc = pdf;
                const loader = document.getElementById('pdf-loader');
                const canvasEl = document.getElementById('pdf-canvas');
                
                if (loader) loader.style.display = 'none';
                if (canvasEl) canvasEl.style.display = 'block';
                
                const pageCountEl = document.getElementById('page-count');
                if (pageCountEl) pageCountEl.textContent = pdf.numPages;
                
                // Limit initial page to preview pages
                if (pdf.numPages > maxPreviewPages) {
                    pageNum = Math.min(pageNum, maxPreviewPages);
                }
                
                renderPage(pageNum);
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                const loader = document.getElementById('pdf-loader');
                if (loader) {
                    let errorMsg = 'Error loading PDF.';
                    if (error.message) {
                        errorMsg += ' ' + error.message;
                    }
                    loader.innerHTML = 
                        '<div class="text-white"><i class="fas fa-exclamation-triangle fa-3x"></i><p class="mt-3">' + errorMsg + '</p><p class="mt-2"><small>Please check if the PDF file exists and try again.</small></p></div>';
                }
            });
        }

        // Event listeners and PDF loading - wait for DOM
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners
            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            const zoomInBtn = document.getElementById('zoom-in');
            const zoomOutBtn = document.getElementById('zoom-out');
            
            if (prevBtn) prevBtn.addEventListener('click', onPrevPage);
            if (nextBtn) nextBtn.addEventListener('click', onNextPage);
            if (zoomInBtn) zoomInBtn.addEventListener('click', zoomIn);
            if (zoomOutBtn) zoomOutBtn.addEventListener('click', zoomOut);
            
            // Load PDF - wait a bit for PDF.js to load
            if (typeof pdfjsLib === 'undefined') {
                setTimeout(function() {
                    loadPDF();
                }, 500);
            } else {
                loadPDF();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (!pdfDoc) return; // Don't handle keys if PDF not loaded
            if (e.key === 'ArrowLeft') {
                onPrevPage();
            } else if (e.key === 'ArrowRight') {
                onNextPage();
            }
        });
        
        })(); // End IIFE
    </script>

@endsection

