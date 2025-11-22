@extends('layouts.app')
@section('content')

    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-undo"></i> Return Book
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('book_issued') }}">
                        <i class="fas fa-list"></i> All Issue List
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-2 col-md-8">
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div class="card-header" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; border: none;">
                            <h5 style="margin: 0; font-weight: 600;">
                                <i class="fas fa-info-circle"></i> Book Issue Details
                            </h5>
                        </div>
                        <div class="card-body" style="background: #ffffff;">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Member Name:</th>
                                    <td><b>{{ $bookIssue->student->name }}</b></td>
                                </tr>
                                <tr>
                                    <th>Book Name:</th>
                                    <td><b>{{ $bookIssue->book->name }}</b></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><b>{{ $bookIssue->student->phone }}</b></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><b>{{ $bookIssue->student->email }}</b></td>
                                </tr>
                                <tr>
                                    <th>Issue Date:</th>
                                    <td><b>{{ \Carbon\Carbon::parse($bookIssue->issue_date)->format('d M, Y') }}</b></td>
                                </tr>
                                <tr>
                                    <th>Return Date:</th>
                                    <td><b>{{ \Carbon\Carbon::parse($bookIssue->return_date)->format('d M, Y') }}</b></td>
                                </tr>
                                @if($bookIssue->issue_receipt_number)
                                <tr>
                                    <th>Issue Receipt:</th>
                                    <td><b>{{ $bookIssue->issue_receipt_number }}</b></td>
                                </tr>
                                @endif
                                @if ($bookIssue->issue_status == 'Y')
                                    <tr>
                                        <th>Status:</th>
                                        <td><span class="badge badge-success">Returned</span></td>
                                    </tr>
                                    <tr>
                                        <th>Returned On:</th>
                                        <td><b>{{ \Carbon\Carbon::parse($bookIssue->return_day)->format('d M, Y') }}</b></td>
                                    </tr>
                                    @if($bookIssue->return_receipt_number)
                                    <tr>
                                        <th>Return Receipt:</th>
                                        <td><b>{{ $bookIssue->return_receipt_number }}</b></td>
                                    </tr>
                                    @endif
                                    @if($bookIssue->fine_amount > 0)
                                    <tr>
                                        <th>Fine Paid:</th>
                                        <td><b>${{ number_format($bookIssue->fine_amount, 2) }}</b></td>
                                    </tr>
                                    @endif
                                @else
                                    @if($daysOverdue > 0)
                                    <tr class="table-warning">
                                        <th>Days Overdue:</th>
                                        <td><b>{{ $daysOverdue }} days</b></td>
                                    </tr>
                                    @if($fine > 0)
                                    <tr class="table-danger">
                                        <th>Fine Amount:</th>
                                        <td><b>${{ number_format($fine, 2) }}</b></td>
                                    </tr>
                                    @else
                                    <tr class="table-success">
                                        <th>Fine Amount:</th>
                                        <td><b>No fine (within grace period)</b></td>
                                    </tr>
                                    @endif
                                    @else
                                    <tr class="table-success">
                                        <th>Status:</th>
                                        <td><b>On Time</b></td>
                                    </tr>
                                    @endif
                                @endif
                            </table>
                        </div>
                    </div>

                    @if ($bookIssue->issue_status == 'N')
                        <div class="card mt-4" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                            <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                                <h5 style="margin: 0; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Process Return
                                </h5>
                            </div>
                            <div class="card-body" style="background: #ffffff;">
                                <form action="{{ route('book_issue.update', $bookIssue->id) }}" method="post" autocomplete="off">
                                    @csrf
                                    <div class="form-group">
                                        <label>Book Condition <span class="text-danger">*</span></label>
                                        <select name="book_condition" class="form-control" required>
                                            <option value="good" {{ old('book_condition') == 'good' ? 'selected' : '' }}>Good</option>
                                            <option value="damaged" {{ old('book_condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                            <option value="lost" {{ old('book_condition') == 'lost' ? 'selected' : '' }}>Lost</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Damage/Loss Notes</label>
                                        <textarea name="damage_notes" class="form-control" rows="3" 
                                            placeholder="Describe any damage or loss details...">{{ old('damage_notes') }}</textarea>
                                    </div>
                                    @if($fine > 0)
                                    <div class="alert alert-warning">
                                        <strong>Fine to be charged: ${{ number_format($fine, 2) }}</strong><br>
                                        <small>Fine will be automatically added to the member's account.</small>
                                    </div>
                                    @endif
                                    <button type='submit' class='btn btn-danger btn-lg btn-block' name='save'>
                                        <i class="fas fa-check"></i> Process Return
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
