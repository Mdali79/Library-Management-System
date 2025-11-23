@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2 class="admin-heading">
                        <i class="fas fa-clock"></i> Pending Book Requests
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <table class="content-table">
                        <thead>
                            <th>S.No</th>
                            <th>Student Name</th>
                            <th>Book Name</th>
                            <th>Department</th>
                            <th>Roll No</th>
                            <th>Request Date</th>
                            <th>Expected Return Date</th>
                            <th>Book Availability</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse ($pendingRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>
                                        <strong>{{ $request->student->name }}</strong><br>
                                        <small>{{ $request->student->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $request->book->name }}</strong><br>
                                        @if($request->book->auther)
                                            <small>By: {{ $request->book->auther->name }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $request->student->department ?? 'N/A' }}</td>
                                    <td>{{ $request->student->roll ?? 'N/A' }}</td>
                                    <td>{{ $request->issue_date->format('d M, Y') }}</td>
                                    <td>{{ $request->return_date->format('d M, Y') }}</td>
                                    <td>
                                        @if($request->book->available_quantity > 0)
                                            <span class='badge badge-success'>Available ({{ $request->book->available_quantity }})</span>
                                        @else
                                            <span class='badge badge-danger'>Unavailable</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('book_issue.approve', $request->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Approve and issue this book to {{ $request->student->name }}?')"
                                                    @if($request->book->available_quantity <= 0) disabled title="Book not available" @endif>
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $request->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                                                <h5 class="modal-title">Reject Book Request</h5>
                                                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('book_issue.reject', $request->id) }}" method="post">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" required 
                                                            placeholder="Please provide a reason for rejection..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Reject Request</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div style="padding: 2rem;">
                                            <i class="fas fa-check-circle" style="font-size: 3rem; color: #10b981;"></i>
                                            <p style="margin-top: 1rem; font-size: 1.1rem;">No pending requests!</p>
                                            <p style="color: #64748b;">All book requests have been processed.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $pendingRequests->links('vendor/pagination/bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

