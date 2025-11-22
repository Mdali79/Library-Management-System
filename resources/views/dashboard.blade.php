@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="admin-heading">Dashboard - {{ auth()->user()->role }}</h2>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Main Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
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
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.9;">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700;">{{ $issued_books }}</h1>
                            <h5 class="card-title" style="font-size: 0.9rem; opacity: 0.95;">Issued Books</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.9;">
                                <i class="fas fa-undo"></i>
                            </div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700;">{{ $returned_books }}</h1>
                            <h5 class="card-title" style="font-size: 0.9rem; opacity: 0.95;">Returned Books</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics for Admin/Librarian -->
            @if(in_array($role, ['Admin', 'Librarian']))
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card text-center" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border: none;">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.9;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h1 class="mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ $pending_fines_count }}</h1>
                            <h5 class="card-title" style="font-size: 0.85rem; opacity: 0.95;">Pending Fines</h5>
                            <p class="mb-0" style="font-size: 1.1rem; font-weight: 600;">${{ number_format($pending_fines_amount, 2) }}</p>
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
            @if(in_array($role, ['Student', 'Teacher', 'Librarian']) && isset($my_issued_books))
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
                                        <td>${{ number_format($fine->amount, 2) }}</td>
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

            <!-- Overdue Books for Admin/Librarian -->
            @if(in_array($role, ['Admin', 'Librarian']) && isset($overdue_books) && count($overdue_books) > 0)
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Activity Chart
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = @json($monthly_activity);
        
        new Chart(ctx, {
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
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
