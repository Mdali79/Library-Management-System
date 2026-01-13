@extends('layouts.app')
@section('content')
@php
    use Illuminate\Support\Str;
@endphp
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-list"></i>
                        @if(isset($role) && in_array($role, ['Student', 'Teacher']))
                            My Book Requests
                        @else
                            All Book Issues
                        @endif
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end align-items-center mb-3" style="display: flex !important; justify-content: flex-end !important; align-items: center !important; margin-bottom: 1rem !important; width: 100%;">
                        <div style="display: flex !important; flex-wrap: nowrap;">
                            @if(!isset($role) || !in_array($role, ['Student', 'Teacher']))
                                <a class="add-new" href="{{ route('book_issue.create') }}" style="text-decoration: none; margin-right: 0;">
                                    <i class="fas fa-plus"></i> Issue Book
                                </a>
                            @endif
                            @if(auth()->user()->role == 'Librarian' || auth()->user()->role == 'Admin')
                                <a class="add-new" href="{{ route('book_issue.pending') }}" style="text-decoration: none; background: linear-gradient(135deg, #f59e0b, #d97706); margin-left: 10px;">
                                    <i class="fas fa-clock"></i> Pending Requests
                                </a>
                            @endif
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <table class="content-table">
                        <thead>
                            <th>S.No</th>
                            @if(!isset($role) || !in_array($role, ['Student', 'Teacher']))
                                <th>Student Name</th>
                            @endif
                            <th>Book Name</th>
                            @if(!isset($role) || !in_array($role, ['Student', 'Teacher']))
                                <th>Phone</th>
                                <th>Email</th>
                            @endif
                            <th>Issue Date</th>
                            <th>Return Date</th>
                            <th>Request Status</th>
                            <th>Issue Status</th>
                            @if(isset($role) && in_array($role, ['Student', 'Teacher']))
                                <th>Action</th>
                            @else
                                <th>Edit</th>
                                <th>Delete</th>
                            @endif
                        </thead>
                        <tbody>
                            @forelse ($books as $book)
                                <tr style='@if (date('Y-m-d') > $book->return_date->format('Y-m-d') && $book->issue_status == 'N' && $book->request_status == 'issued') background:rgba(255,0,0,0.2) @endif'>
                                    <td>{{ $book->id }}</td>
                                    @if(!isset($role) || !in_array($role, ['Student', 'Teacher']))
                                        <td>{{ $book->student->name }}</td>
                                    @endif
                                    <td>{{ $book->book->name }}</td>
                                    @if(!isset($role) || !in_array($role, ['Student', 'Teacher']))
                                        <td>{{ $book->student->phone }}</td>
                                        <td>{{ $book->student->email }}</td>
                                    @endif
                                    <td>{{ $book->issue_date->format('d M, Y') }}</td>
                                    <td>{{ $book->return_date->format('d M, Y') }}</td>
                                    <td>
                                        @if($book->request_status == 'pending')
                                            <span class='badge badge-warning'>Pending</span>
                                        @elseif($book->request_status == 'approved')
                                            <span class='badge badge-info'>Approved</span>
                                        @elseif($book->request_status == 'rejected')
                                            <span class='badge badge-danger'>Rejected</span>
                                            @if($book->rejection_reason)
                                                <br><small style="color: #dc2626;">{{ Str::limit($book->rejection_reason, 30) }}</small>
                                            @endif
                                        @elseif($book->request_status == 'issued')
                                            <span class='badge badge-success'>Issued</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($book->issue_status == 'Y')
                                            <span class='badge badge-success'>Returned</span>
                                        @else
                                            <span class='badge badge-danger'>Not Returned</span>
                                        @endif
                                    </td>
                                    @if(isset($role) && in_array($role, ['Student', 'Teacher']))
                                        <td>
                                            @if($book->request_status == 'pending')
                                                <form action="{{ route('book_issue.destroy', $book->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this request?')">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </form>
                                            @elseif($book->request_status == 'issued' && $book->issue_status == 'N')
                                                <a href="{{ route('book_issue.edit', $book->id) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-undo"></i> Return
                                                </a>
                                            @endif
                                        </td>
                                    @else
                                        <td class="edit">
                                            @if($book->request_status == 'issued')
                                                <a href="{{ route('book_issue.edit', $book->id) }}" class="btn btn-success">Edit</a>
                                            @endif
                                        </td>
                                        <td class="delete">
                                            @if($book->issue_status != 'Y')
                                                <form action="{{ route('book_issue.destroy', $book) }}" method="post" class="form-hidden">
                                                    <button class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book issue?')">Delete</button>
                                                    @csrf
                                                </form>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ !isset($role) || !in_array($role, ['Student', 'Teacher']) ? '10' : '7' }}">No Book Issues Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($books instanceof \Illuminate\Pagination\LengthAwarePaginator && $books->hasPages())
                        {{ $books->links('vendor/pagination/bootstrap-4') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
