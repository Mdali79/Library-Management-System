@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="admin-heading">Book Reservations</h2>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('reservations.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="book_id" class="form-control" 
                                    placeholder="Book ID" value="{{ request('book_id') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="row">
                <div class="col-md-12">
                    <table class="content-table">
                        <thead>
                            <th>S.No</th>
                            <th>Book</th>
                            <th>Member</th>
                            <th>Reserved Date</th>
                            <th>Status</th>
                            <th>Notified</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse ($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->id }}</td>
                                    <td>{{ $reservation->book->name }}</td>
                                    <td>{{ $reservation->student->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->reserved_at)->format('d M Y') }}</td>
                                    <td>
                                        @if($reservation->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($reservation->status == 'available')
                                            <span class="badge badge-success">Available</span>
                                        @elseif($reservation->status == 'issued')
                                            <span class="badge badge-info">Issued</span>
                                        @else
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reservation->notified_at)
                                            {{ \Carbon\Carbon::parse($reservation->notified_at)->format('d M Y') }}
                                        @else
                                            <span class="text-muted">Not notified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reservation->expires_at)
                                            {{ \Carbon\Carbon::parse($reservation->expires_at)->format('d M Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reservation->status == 'pending' && $reservation->book->available_quantity > 0)
                                            <form action="{{ route('reservations.notify', $reservation->book_id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Notify Available</button>
                                            </form>
                                        @endif
                                        @if($reservation->status == 'available')
                                            <form action="{{ route('reservations.mark_issued', $reservation->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Mark as Issued</button>
                                            </form>
                                        @endif
                                        @if(in_array($reservation->status, ['pending', 'available']))
                                            <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to cancel this reservation?')">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No reservations found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $reservations->links('vendor/pagination/bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

