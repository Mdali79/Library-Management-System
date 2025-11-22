<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Library Management System') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}"> <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Custom stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Font Awesome -->
</head>

<body>
    <div id="header">
        <!-- HEADER -->
        <div class="container">
            <div class="row">
                <div class="offset-md-4 col-md-4">
                    <div class="logo">
                        <a href="#"><img src="{{ asset('images/library.png') }}"></a>
                    </div>
                </div>
                <div class="offset-md-2 col-md-2">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('change_password') }}">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="document.getElementById('logoutForm').submit()">
                                <i class="fas fa-sign-out-alt"></i> Log Out
                            </a>
                        </div>
                        <form method="post" id="logoutForm" action="{{ route('logout') }}">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /HEADER -->
    <div id="menubar">
        <!-- Menu Bar -->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="menu">
                        <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                        @if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Librarian')
                            <li><a href="{{ route('authors') }}"><i class="fas fa-user-edit"></i> Authors</a></li>
                            <li><a href="{{ route('publishers') }}"><i class="fas fa-building"></i> Publishers</a></li>
                            <li><a href="{{ route('categories') }}"><i class="fas fa-tags"></i> Categories</a></li>
                            <li><a href="{{ route('students') }}"><i class="fas fa-users"></i> Members</a></li>
                        @endif
                        <li><a href="{{ route('books') }}"><i class="fas fa-book"></i> Books</a></li>
                        @if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Librarian')
                            <li><a href="{{ route('book_issued') }}"><i class="fas fa-hand-holding"></i> Book Issue</a></li>
                            <li><a href="{{ route('reservations.index') }}"><i class="fas fa-calendar-check"></i> Reservations</a></li>
                            <li><a href="{{ route('fines.index') }}"><i class="fas fa-dollar-sign"></i> Fines</a></li>
                        @endif
                        <li><a href="{{ route('reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
                        @if(auth()->user()->role == 'Admin')
                            <li><a href="{{ route('settings') }}"><i class="fas fa-cog"></i> Settings</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div> <!-- /Menu Bar -->

    @yield('content')

    <!-- FOOTER -->
    <div id="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <span>© Copyright {{ now()->format("Y") }} <a href="https://www.yahoobaba.net">YahooBaba 😎</a></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /FOOTER -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
