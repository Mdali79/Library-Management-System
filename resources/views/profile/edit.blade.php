@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="admin-heading">
                        <i class="fas fa-edit"></i> Edit Profile
                    </h2>
                </div>
                <div class="col-md-6 text-right">
                    <a class="add-new" href="{{ route('profile.show') }}">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="offset-md-2 col-md-8">
                    <form class="yourform" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group text-center mb-4">
                            <label>Profile Picture</label>
                            <div class="mb-3">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                        alt="{{ $user->name }}" 
                                        id="profile-preview"
                                        style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd;">
                                @else
                                    <div id="profile-preview" style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid #ddd;">
                                        <span style="font-size: 3rem; color: white;">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                                name="profile_picture" id="profile_picture" accept="image/*" onchange="previewImage(this)">
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                            @error('profile_picture')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Full Name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email Address" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" class="form-control @error('contact') is-invalid @enderror"
                                placeholder="Phone Number" name="contact" value="{{ old('contact', $user->contact) }}">
                            @error('contact')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profile-preview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        // Replace div with img
                        const img = document.createElement('img');
                        img.id = 'profile-preview';
                        img.src = e.target.result;
                        img.style.cssText = 'width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd;';
                        preview.parentNode.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

