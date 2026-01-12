<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>eLibrary - Library Management System</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/library.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/library.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/library.png') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <!-- Custom stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    @yield('content')

    <!-- jQuery and Bootstrap JS for modals -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <style>
        /* Global styles for all search suggestion dropdowns across the project */
        .suggestions-dropdown {
            position: absolute !important;
            background: white !important;
            z-index: 999999 !important;
            top: 100% !important;
            left: 0 !important;
            margin-top: 2px !important;
        }
        /* Prevent parent containers from clipping dropdowns */
        .card, .card-body, .card-header, .row, .container, #admin-content,
        .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6,
        .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12,
        form {
            overflow: visible !important;
        }
        /* Ensure form groups with search inputs have proper positioning */
        .form-group[style*="position: relative"],
        .form-group {
            position: relative !important;
            z-index: 1 !important;
        }
        /* Ensure tables don't overlap dropdowns - lower z-index and remove overflow */
        .content-table,
        #admin-content .content-table,
        table.content-table,
        table.content-table thead,
        #admin-content .content-table thead {
            position: relative !important;
            z-index: 1 !important;
            overflow: visible !important;
        }
        /* Override the overflow: hidden from style.css */
        #admin-content .content-table {
            overflow: visible !important;
        }
        /* Ensure all rows with tables have lower z-index */
        .row .content-table,
        .row table.content-table {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
</body>

</html>
