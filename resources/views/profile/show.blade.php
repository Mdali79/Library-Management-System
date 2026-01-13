@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-user"></i> My Profile
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('profile.edit') }}">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="offset-md-2 col-md-8">
                    <div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div class="card-body" style="padding: 2rem;">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <a href="{{ route('profile.edit') }}" style="text-decoration: none; display: inline-block;">
                                        @if($user->profile_picture)
                                            @php
                                                $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)
                                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture)
                                                    : asset('storage/' . $user->profile_picture);
                                                // Add cache buster to ensure fresh image after update
                                                $profileImageUrl .= '?v=' . time();
                                            @endphp
                                            <img src="{{ $profileImageUrl }}"
                                                alt="{{ $user->name }}"
                                                style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease;"
                                                onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';"
                                                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';"
                                                onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @else
                                            <div style="width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease;"
                                                onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';"
                                                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                                <span style="font-size: 4rem; color: white;">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                <div class="col-md-8">
                                    <h3 class="mb-4">{{ $user->name }}</h3>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Username:</th>
                                            <td>{{ $user->username }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $user->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Contact:</th>
                                            <td>{{ $user->contact ?? 'N/A' }}</td>
                                        </tr>
                                        @if($user->department)
                                        <tr>
                                            <th>Department:</th>
                                            <td>{{ $user->department }}</td>
                                        </tr>
                                        @endif
                                        @if($user->batch)
                                        <tr>
                                            <th>Batch:</th>
                                            <td>{{ $user->batch }}</td>
                                        </tr>
                                        @endif
                                        @if($user->roll)
                                        <tr>
                                            <th>Roll No:</th>
                                            <td>{{ $user->roll }}</td>
                                        </tr>
                                        @endif
                                        @if($user->reg_no)
                                        <tr>
                                            <th>Registration No:</th>
                                            <td>{{ $user->reg_no }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

