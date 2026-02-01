@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2 class="admin-heading">All Students</h2>
                </div>
                <div class="offset-md-6 col-md-2">
                    <a class="add-new" href="{{ route('student.create') }}">Add Student</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="message"></div>
                    <table class="content-table">
                        <thead>
                            <th style="text-align: center;">S.No</th>
                            <th style="text-align: center;">Student Name</th>
                            <th style="text-align: center;">Gender</th>
                            <th style="text-align: center;">Phone</th>
                            <th style="text-align: center;">Email</th>
                            <th style="text-align: center;">View</th>
                            <th style="text-align: center;">Edit</th>
                            <th style="text-align: center;">Delete</th>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr>
                                    <td class="id" style="text-align: center;">{{ $student->id }}</td>
                                    <td style="text-align: center;">{{ $student->name }}</td>
                                    <td class="text-capitalize" style="text-align: center;">{{ $student->gender }}</td>
                                    <td style="text-align: center;">{{ $student->phone }}</td>
                                    <td style="text-align: center;">{{ $student->email }}</td>
                                    <td class="view" style="text-align: center;">
                                        <button data-sid='{{ $student->id }}>'
                                            class="btn btn-primary view-btn">View</button>
                                    </td>
                                    <td class="edit" style="text-align: center;">
                                        <a href="{{ route('student.edit', $student) }}>" class="btn btn-success">Edit</a>
                                    </td>
                                    <td class="delete" style="text-align: center;">
                                        <form action="{{ route('student.destroy', $student->id) }}" method="post" class="form-hidden">
                                            @csrf
                                            <button type="button" class="btn btn-danger confirm-delete" data-confirm-message="Are you sure you want to delete the student &quot;{{ $student->name }}&quot;? This action cannot be undone.">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No Students Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $students->links('vendor/pagination/bootstrap-4') }}
                    <div id="modal">
                        <div id="modal-form">
                            <table cellpadding="10px" width="100%">

                            </table>
                            <div id="close-btn">X</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script type="text/javascript">
        //Show shudent detail
        $(".view-btn").on("click", function() {
            var student_id = $(this).data("sid");
            $.ajax({
                url: "http://127.0.0.1:8000/student/show/"+student_id,
                type: "get",
                success: function(student) {
                    console.log(student);
                    form ="<tr><td>Student Name :</td><td><b>"+student['name']+"</b></td></tr><tr><td>Address :</td><td><b>"+student['address']+"</b></td></tr><tr><td>Gender :</td><td><b>"+ student['gender']+ "</b></td></tr><tr><td>Class :</td><td><b>"+ student['class']+ "</b></td></tr><tr><td>Age :</td><td><b>"+ student['age']+ "</b></td></tr><tr><td>Phone :</td><td><b>"+ student['phone']+ "</b></td></tr><tr><td>Email :</td><td><b>"+ student['email']+ "</b></td></tr>";
          console.log(form);

                    $("#modal-form table").html(form);
                    $("#modal").show();
                }
            });
        });

        //Hide modal box
        $('#close-btn').on("click", function() {
            $("#modal").hide();
        });
    </script>
@endsection
