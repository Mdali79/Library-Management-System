@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">Dashboard - {{ auth()->user()->role }}</h2>
                </div>
            </div>

            <!-- Dashboard Search Bar -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div class="card-header" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; border: none;">
                            <h5 style="margin: 0; font-weight: 600;">
                                <i class="fas fa-search"></i> Quick Search
                            </h5>
                        </div>
                        <div class="card-body" style="background: #ffffff;">
                            <form method="GET" action="{{ route('books') }}" id="dashboard-search-form">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group" style="position: relative;">
                                            <input type="text" name="search" id="dashboard-search-input" class="form-control form-control-lg"
                                                placeholder="Search books, authors, categories..." autocomplete="off">
                                            <div id="dashboard-suggestions" class="suggestions-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Main Statistics Cards -->
            <div class="row mb-4">
                <div class="{{ $role === 'Student' ? 'col-md-4' : 'col-md-3' }} mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.9;">
                                <i class="fas fa-book"></i>
                            </div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700;">{{ $books }}</h1>
                            <h5 class="card-title" style="font-size: 0.9rem; opacity: 0.95;">Total Books</h5>
                        </div>
                    </div>
                </div>
                @if($role !== 'Student')
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.9;">
                                <i class="fas fa-users"></i>
                            </div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700;">{{ $students }}</h1>
                            <h5 class="card-title" style="font-size: 0.9rem; opacity: 0.95;">Total Members</h5>
                        </div>
                    </div>
                </div>
                @endif
                <div class="{{ $role === 'Student' ? 'col-md-4' : 'col-md-3' }} mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.9;">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700;">{{ $issued_books }}</h1>
                            <h5 class="card-title" style="font-size: 0.9rem; opacity: 0.95;">{{ $role === 'Student' ? 'My Issued Books' : 'Issued Books' }}</h5>
                        </div>
                    </div>
                </div>
                <div class="{{ $role === 'Student' ? 'col-md-4' : 'col-md-3' }} mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.9;">
                                <i class="fas fa-undo"></i>
                            </div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700;">{{ $returned_books }}</h1>
                            <h5 class="card-title" style="font-size: 0.9rem; opacity: 0.95;">{{ $role === 'Student' ? 'My Returned Books' : 'Returned Books' }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics for Admin -->
            @if($role === 'Admin')
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.9;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h1 class="mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ $pending_fines_count }}</h1>
                            <h5 class="card-title" style="font-size: 0.85rem; opacity: 0.95;">Pending Fines</h5>
                            <p class="mb-0" style="font-size: 1.1rem; font-weight: 600;">{{ number_format($pending_fines_amount, 2) }} tk</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.9;">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <h1 class="mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ $authors }}</h1>
                            <h5 class="card-title" style="font-size: 0.85rem; opacity: 0.95;">Authors</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.8;">
                                <i class="fas fa-building" style="color: #7c3aed;"></i>
                            </div>
                            <h1 class="mb-1" style="font-size: 2.5rem; font-weight: 700; color: #1e293b;">{{ $publishers }}</h1>
                            <h5 class="card-title" style="font-size: 0.85rem; color: #64748b;">Publishers</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.8;">
                                <i class="fas fa-tags" style="color: #f59e0b;"></i>
                            </div>
                            <h1 class="mb-1" style="font-size: 2.5rem; font-weight: 700; color: #1e293b;">{{ $categories }}</h1>
                            <h5 class="card-title" style="font-size: 0.85rem; color: #64748b;">Categories</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Registrations Card -->
            @if(isset($pending_registrations) && $pending_registrations > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning" style="border-left: 4px solid #f59e0b;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-clock"></i>
                                <strong>{{ $pending_registrations }} Pending User Registration(s)</strong>
                                waiting for approval
                            </div>
                            <a href="{{ route('registrations.pending') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-eye"></i> Review Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif

            <!-- Monthly Activity Chart -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div class="card-header" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; border: none;">
                            <h5 style="margin: 0; font-weight: 600;">
                                <i class="fas fa-chart-line"></i> Monthly Activity Chart (Last 12 Months)
                            </h5>
                        </div>
                        <div class="card-body" style="background: #ffffff; padding: 2rem;">
                            <canvas id="monthlyChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Based Content -->
            @if($role === 'Student' && isset($my_issued_books))
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>My Issued Books ({{ $my_current_borrowed }}/{{ $my_borrowing_limit }})</h5>
                        </div>
                        <div class="card-body">
                            @if(count($my_issued_books) > 0)
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Book Name</th>
                                            <th>Issue Date</th>
                                            <th>Return Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($my_issued_books as $issue)
                                        <tr>
                                            <td>{{ $issue->book->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($issue->return_date)->format('d M Y') }}</td>
                                            <td>
                                                @if(\Carbon\Carbon::parse($issue->return_date)->isPast())
                                                    <span class="badge badge-danger">Overdue</span>
                                                @else
                                                    <span class="badge badge-warning">Active</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No books currently issued.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($my_pending_fines) && count($my_pending_fines) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5>My Pending Fines</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Amount</th>
                                        <th>Days Overdue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($my_pending_fines as $fine)
                                    <tr>
                                        <td>{{ $fine->bookIssue->book->name }}</td>
                                        <td>{{ number_format($fine->amount, 2) }} tk</td>
                                        <td>{{ $fine->days_overdue }} days</td>
                                        <td><a href="{{ route('fines.index') }}" class="btn btn-sm btn-primary">Pay Now</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif

            <!-- Overdue Books for Admin -->
            @if($role === 'Admin' && isset($overdue_books) && count($overdue_books) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5>Overdue Books ({{ count($overdue_books) }})</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Member</th>
                                        <th>Issue Date</th>
                                        <th>Return Date</th>
                                        <th>Days Overdue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdue_books as $issue)
                                    <tr>
                                        <td>{{ $issue->book->name }}</td>
                                        <td>{{ $issue->student->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($issue->return_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($issue->return_date)->diffInDays(now()) }} days</td>
                                        <td><a href="{{ route('book_issue.edit', $issue->id) }}" class="btn btn-sm btn-primary">Process Return</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js" onload="window.chartJsLoaded = true;"></script>
    <script>
        (function() {
            let chartInitialized = false;
            const monthlyData = @json($monthly_activity);

            function initializeChart() {
                // Prevent multiple initializations
                if (chartInitialized) {
                    return;
                }

                // Check if Chart.js is loaded
                if (typeof Chart === 'undefined') {
                    return;
                }

                // Check if DOM is ready
                const canvas = document.getElementById('monthlyChart');
                if (!canvas) {
                    return;
                }

                // Check if chart already exists
                if (canvas.chart) {
                    chartInitialized = true;
                    return;
                }

                if (!monthlyData || monthlyData.length === 0) {
                    console.warn('No monthly activity data available');
                    return;
                }

                try {
                    const ctx = canvas.getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: monthlyData.map(item => item.month),
                            datasets: [{
                                label: 'Books Issued',
                                data: monthlyData.map(item => item.issued),
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1
                            }, {
                                label: 'Books Returned',
                                data: monthlyData.map(item => item.returned),
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: {
                                duration: 0
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    canvas.chart = chart;
                    chartInitialized = true;
                } catch (error) {
                    console.error('Error initializing chart:', error);
                }
            }

            // Function to try initialization
            function tryInitialize() {
                if (typeof Chart !== 'undefined' && document.getElementById('monthlyChart')) {
                    initializeChart();
                }
            }

            // Wait for DOM and Chart.js
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    // Check if Chart.js is already loaded
                    if (window.chartJsLoaded || typeof Chart !== 'undefined') {
                        setTimeout(tryInitialize, 100);
                    } else {
                        // Poll for Chart.js
                        let attempts = 0;
                        const checkChart = setInterval(function() {
                            attempts++;
                            if (typeof Chart !== 'undefined') {
                                clearInterval(checkChart);
                                setTimeout(tryInitialize, 100);
                            } else if (attempts > 50) {
                                clearInterval(checkChart);
                                console.error('Chart.js failed to load');
                            }
                        }, 50);
                    }
                });
            } else {
                // DOM is already ready
                if (window.chartJsLoaded || typeof Chart !== 'undefined') {
                    setTimeout(tryInitialize, 100);
                } else {
                    // Poll for Chart.js
                    let attempts = 0;
                    const checkChart = setInterval(function() {
                        attempts++;
                        if (typeof Chart !== 'undefined') {
                            clearInterval(checkChart);
                            setTimeout(tryInitialize, 100);
                        } else if (attempts > 50) {
                            clearInterval(checkChart);
                            console.error('Chart.js failed to load');
                        }
                    }, 50);
                }
            }

            // Fallback: try on window load
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (!chartInitialized) {
                        tryInitialize();
                    }
                }, 300);
            });
        })();

        // Dashboard Search Suggestions - AJAX based
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardSearchInput = document.getElementById('dashboard-search-input');
            const dashboardSuggestionsDiv = document.getElementById('dashboard-suggestions');

            if (!dashboardSearchInput || !dashboardSuggestionsDiv) {
                console.error('Dashboard search elements not found');
                return;
            }

            let dashboardSuggestionTimeout = null;
            const dashboardSuggestionsUrl = '{{ route("book.suggestions") }}';

            // Show suggestions on focus
            dashboardSearchInput.addEventListener('focus', function() {
                const searchTerm = this.value.trim();
                if (searchTerm.length > 0) {
                    fetchDashboardSuggestions(searchTerm);
                } else {
                    fetchDashboardSuggestions('');
                }
            });

            // Fetch suggestions as user types - immediate response
            dashboardSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();

                // Clear previous timeout
                if (dashboardSuggestionTimeout) {
                    clearTimeout(dashboardSuggestionTimeout);
                }

                // If user just started typing (first character), show suggestions immediately
                if (searchTerm.length === 1) {
                    fetchDashboardSuggestions(searchTerm);
                } else if (searchTerm.length > 1) {
                    // For subsequent characters, use shorter debounce (150ms) for faster response
                    dashboardSuggestionTimeout = setTimeout(function() {
                        fetchDashboardSuggestions(searchTerm);
                    }, 150);
                } else {
                    // If input is cleared, show popular suggestions
                    fetchDashboardSuggestions('');
                }
            });

            // Also trigger on keydown for immediate feedback
            dashboardSearchInput.addEventListener('keydown', function(e) {
                // Don't interfere with arrow keys, enter, escape
                if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
                    return;
                }

                const searchTerm = this.value.trim();
                // If user is typing a character, show suggestions immediately
                if (searchTerm.length >= 1 && e.key.length === 1) {
                    clearTimeout(dashboardSuggestionTimeout);
                    dashboardSuggestionTimeout = setTimeout(function() {
                        fetchDashboardSuggestions(searchTerm + e.key);
                    }, 100);
                }
            });

            // Fetch suggestions from server
            function fetchDashboardSuggestions(searchTerm) {
                const url = dashboardSuggestionsUrl + (searchTerm ? '?q=' + encodeURIComponent(searchTerm) : '');

                fetch(url)
                    .then(response => response.json())
                    .then(suggestions => {
                        if (suggestions && suggestions.length > 0) {
                            displayDashboardSuggestions(suggestions);
                        } else {
                            dashboardSuggestionsDiv.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching suggestions:', error);
                        dashboardSuggestionsDiv.style.display = 'none';
                    });
            }

            function displayDashboardSuggestions(suggestions) {
                dashboardSuggestionsDiv.innerHTML = '';

                if (!suggestions || suggestions.length === 0) {
                    dashboardSuggestionsDiv.style.display = 'none';
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
                    dashboardSuggestionsDiv.appendChild(header);

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
                            dashboardSearchInput.value = item.text;
                            dashboardSearchInput.focus();
                            dashboardSuggestionsDiv.style.display = 'none';
                            // Don't auto-submit - let user review and click search button
                        });

                        dashboardSuggestionsDiv.appendChild(div);
                    });
                });

                dashboardSuggestionsDiv.style.display = 'block';
            }

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!dashboardSearchInput.contains(e.target) && !dashboardSuggestionsDiv.contains(e.target)) {
                    dashboardSuggestionsDiv.style.display = 'none';
                }
            });

            // Handle keyboard navigation
            dashboardSearchInput.addEventListener('keydown', function(e) {
                const visibleSuggestions = dashboardSuggestionsDiv.querySelectorAll('.suggestion-item');
                const highlighted = dashboardSuggestionsDiv.querySelector('.suggestion-item.highlighted');

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
                    const form = document.getElementById('dashboard-search-form');
                    if (form) {
                        form.submit();
                    }
                }
            } else if (e.key === 'Escape') {
                dashboardSuggestionsDiv.style.display = 'none';
            }
        });
        });
    </script>

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
            min-width: 400px;
            max-width: 700px;
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
    </style>
@endsection
