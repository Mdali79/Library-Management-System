@extends('layouts.guest')
@section('content')

    <div id="wrapper-admin">
        <div class="container">
            <div class="row">
                <div class="offset-md-4 col-md-4">
                    <div class="logo border border-danger">
                        <img src="{{ asset('images/library.png') }}" alt="">
                    </div>
                    <form class="yourform" action="{{ route('login') }}" method="post">
                        @csrf
                        <h3 class="heading">
                            <i class="fas fa-book-reader"></i> Library Login
                        </h3>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}"
                                placeholder="Enter your username" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" class="form-control" 
                                placeholder="Enter your password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        <div class="mt-4 text-center">
                            <p class="text-white">Don't have an account? 
                                <a href="{{ route('register') }}" style="color: #fff; font-weight: 600; text-decoration: underline;">
                                    Register Here
                                </a>
                            </p>
                        </div>
                    </form>
                    @error('username')
                        <div class='alert alert-danger'>{{ $message }}</div>
                    @enderror
                    @if(session('success'))
                        <div class='alert alert-success'>{{ session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
