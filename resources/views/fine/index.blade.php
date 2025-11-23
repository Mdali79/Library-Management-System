@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="admin-heading">Fine Management</h2>
                </div>
                <div class="offset-md-6 col-md-3">
                    @if(in_array($role, ['Admin', 'Librarian']))
                        <form action="{{ route('fines.calculate_overdue') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Calculate fines for all overdue books?')">
                                <i class="fas fa-calculator"></i> Calculate Overdue Fines
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center bg-warning text-white">
                        <div class="card-body">
                            <h3>${{ number_format($pendingFines, 2) }}</h3>
                            <h5>Pending Fines</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center bg-success text-white">
                        <div class="card-body">
                            <h3>${{ number_format($paidFines, 2) }}</h3>
                            <h5>Paid Fines</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center bg-info text-white">
                        <div class="card-body">
                            <h3>${{ number_format($totalFines, 2) }}</h3>
                            <h5>Total Fines</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('fines.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="waived" {{ request('status') == 'waived' ? 'selected' : '' }}>Waived</option>
                                </select>
                            </div>
                            @if(in_array($role, ['Admin', 'Librarian']))
                                <div class="col-md-3">
                                    <input type="text" name="student_id" class="form-control" 
                                        placeholder="Student ID" value="{{ request('student_id') }}">
                                </div>
                            @endif
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fines Table -->
            <div class="row">
                <div class="col-md-12">
                    <table class="content-table">
                        <thead>
                            <th>S.No</th>
                            <th>Member</th>
                            <th>Book</th>
                            <th>Days Overdue</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Paid Date</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse ($fines as $fine)
                                <tr>
                                    <td>{{ $fine->id }}</td>
                                    <td>{{ $fine->student->name ?? 'N/A' }}</td>
                                    <td>{{ $fine->bookIssue->book->name ?? 'N/A' }}</td>
                                    <td>{{ $fine->days_overdue }} days</td>
                                    <td>${{ number_format($fine->amount, 2) }}</td>
                                    <td>
                                        @if($fine->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($fine->status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-secondary">Waived</span>
                                        @endif
                                    </td>
                                    <td>{{ $fine->payment_method ? ucfirst($fine->payment_method) : 'N/A' }}</td>
                                    <td>{{ $fine->paid_at ? \Carbon\Carbon::parse($fine->paid_at)->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        @if($fine->status == 'pending')
                                            @if(in_array($role, ['Student', 'Teacher']))
                                                @php
                                                    $student = \App\Models\student::where('user_id', auth()->id())->first();
                                                    $canPay = $student && $fine->student_id == $student->id;
                                                @endphp
                                                @if($canPay)
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                        data-toggle="modal" data-target="#payModal{{ $fine->id }}">Pay</button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            @else
                                                {{-- Admin/Librarian can pay any fine --}}
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                    data-toggle="modal" data-target="#payModal{{ $fine->id }}">Pay</button>
                                                <form action="{{ route('fines.waive', $fine->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-secondary" 
                                                        onclick="return confirm('Are you sure you want to waive this fine?')">
                                                        <i class="fas fa-hand-holding-heart"></i> Waive
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Pay Modal -->
                                <div class="modal fade" id="payModal{{ $fine->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Pay Fine</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form action="{{ route('fines.pay', $fine->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p><strong>Amount:</strong> ${{ number_format($fine->amount, 2) }}</p>
                                                    <div class="form-group">
                                                        <label>Payment Method <span class="text-danger">*</span></label>
                                                        <select name="payment_method" class="form-control" required>
                                                            <option value="cash">Cash</option>
                                                            <option value="online">Online Payment</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Notes</label>
                                                        <textarea name="notes" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Confirm Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="9">No fines found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $fines->links('vendor/pagination/bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

