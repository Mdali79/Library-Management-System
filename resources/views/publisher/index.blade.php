@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="admin-heading">All Publisher</h2>
                </div>
                <div class="offset-md-7 col-md-2">
                    <a class="add-new" href="{{ route('publisher.create') }}">Add Publisher</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="message"></div>
                    <table class="content-table">
                        <thead>
                            <th style="text-align: center;">S.No</th>
                            <th style="text-align: center;">Publisher Name</th>
                            <th style="text-align: center;">Edit</th>
                            <th style="text-align: center;">Delete</th>
                        </thead>
                        <tbody>
                            @forelse ($publishers as $publisher)
                                <tr>
                                    <td style="text-align: center;">{{ $publisher->id }}</td>
                                    <td style="text-align: center;">{{ $publisher->name }}</td>
                                    <td class="edit" style="text-align: center;">
                                        <a href="{{ route('publisher.edit', $publisher) }}" class="btn btn-success">Edit</a>
                                    </td>
                                    <td class="delete" style="text-align: center;">
                                        <form action="{{ route('publisher.destroy', $publisher) }}" method="post" class="form-hidden">
                                            @csrf
                                            <button type="button" class="btn btn-danger confirm-delete" data-confirm-message="Are you sure you want to delete the publisher &quot;{{ $publisher->name }}&quot;? This action cannot be undone.">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No Publisher Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $publishers->links('vendor/pagination/bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

