@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="admin-heading">All Categories</h2>
                </div>
                <div class="offset-md-7 col-md-2">
                    <a class="add-new" href="{{ route('category.create') }}">Add Category</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <script>
                            // Show popup alert
                            setTimeout(function() {
                                alert('{{ session('success') }}');
                            }, 100);
                        </script>
                    @endif
                    <div class="message"></div>
                    <table class="content-table">
                        <thead>
                            <th style="text-align: center;">S.No</th>
                            <th style="text-align: center;">Category Name</th>
                            <th style="text-align: center;">Edit</th>
                            <th style="text-align: center;">Delete</th>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td style="text-align: center;">{{ $category->id }}</td>
                                    <td style="text-align: center;">{{ $category->name }}</td>
                                    <td class="edit" style="text-align: center;">
                                        <a href="{{ route('category.edit', $category) }}" class="btn btn-success">Edit</a>
                                    </td>
                                    <td class="delete" style="text-align: center;">
                                        <form action="{{ route('category.destroy', $category) }}" method="post"
                                            class="form-hidden">
                                            <button class="btn btn-danger delete-author">Delete</button>
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No Category Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $categories->links('vendor/pagination/bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

