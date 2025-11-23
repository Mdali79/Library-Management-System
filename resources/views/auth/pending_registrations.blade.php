@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-user-clock"></i> Pending User Registrations
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('dashboard') }}">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
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
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse ($pendingRegistrations as $registration)
                                <tr>
                                    <td>{{ $registration->id }}</td>
                                    <td><strong>{{ $registration->name }}</strong></td>
                                    <td>{{ $registration->username }}</td>
                                    <td>{{ $registration->email ?? 'N/A' }}</td>
                                    <td>{{ $registration->contact ?? 'N/A' }}</td>
                                    <td>
                                        @if($registration->role == 'Admin')
                                            <span class='badge badge-danger'>Admin</span>
                                        @elseif($registration->role == 'Librarian')
                                            <span class='badge badge-warning'>Librarian</span>
                                        @else
                                            <span class='badge badge-info'>{{ $registration->role }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $registration->department ?? 'N/A' }}</td>
                                    <td>{{ $registration->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('registrations.approve', $registration->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Approve registration for {{ $registration->name }} as {{ $registration->role }}?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $registration->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $registration->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                                                <h5 class="modal-title">Reject Registration</h5>
                                                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('registrations.reject', $registration->id) }}" method="post">
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
                                                    <button type="submit" class="btn btn-danger">Reject Registration</button>
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
                                            <p style="margin-top: 1rem; font-size: 1.1rem;">No pending registrations!</p>
                                            <p style="color: #64748b;">All user registrations have been processed.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if(method_exists($pendingRegistrations, 'links') && $pendingRegistrations->hasPages())
                        {{ $pendingRegistrations->links('vendor/pagination/bootstrap-4') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

