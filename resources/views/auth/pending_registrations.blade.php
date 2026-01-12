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
                            <th style="text-align: center;">S.No</th>
                            <th style="text-align: center;">Name</th>
                            <th style="text-align: center;">Username</th>
                            <th style="text-align: center;">Email</th>
                            <th style="text-align: center;">Contact</th>
                            <th style="text-align: center;">Role</th>
                            <th style="text-align: center;">Department</th>
                            <th style="text-align: center;">Registration Date</th>
                            <th style="text-align: center;">Actions</th>
                        </thead>
                        <tbody>
                            @forelse ($pendingRegistrations as $registration)
                                <tr>
                                    <td style="text-align: center;">{{ $registration->id }}</td>
                                    <td style="text-align: center;"><strong>{{ $registration->name }}</strong></td>
                                    <td style="text-align: center;">{{ $registration->username }}</td>
                                    <td style="text-align: center;">{{ $registration->email ?? 'N/A' }}</td>
                                    <td style="text-align: center;">{{ $registration->contact ?? 'N/A' }}</td>
                                    <td style="text-align: center;">
                                        @if($registration->role == 'Admin')
                                            <span class='badge badge-danger'>Admin</span>
                                        @else
                                            <span class='badge badge-info'>{{ $registration->role }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">{{ $registration->department ?? 'N/A' }}</td>
                                    <td style="text-align: center;">{{ $registration->created_at->format('d M, Y') }}</td>
                                    <td style="text-align: center;">
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
                                <div class="modal fade" id="rejectModal{{ $registration->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $registration->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('registrations.reject', $registration->id) }}" method="post" id="rejectForm{{ $registration->id }}">
                                                @csrf
                                                <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $registration->id }}">Reject Registration</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($errors->has('rejection_reason'))
                                                        <div class="alert alert-danger">
                                                            {{ $errors->first('rejection_reason') }}
                                                        </div>
                                                    @endif
                                                    <div class="form-group">
                                                        <label for="rejection_reason{{ $registration->id }}">Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" id="rejection_reason{{ $registration->id }}"
                                                            class="form-control @error('rejection_reason') is-invalid @enderror"
                                                            rows="3" required
                                                            placeholder="Please provide a reason for rejection...">{{ old('rejection_reason') }}</textarea>
                                                        @error('rejection_reason')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <small class="form-text text-muted">This reason will be shown to the user if they try to login.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-times"></i> Reject Registration
                                                    </button>
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

