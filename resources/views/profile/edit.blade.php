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
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture) ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture) : asset('storage/' . $user->profile_picture) }}"
                                        alt="{{ $user->name }}"
                                        id="profile-preview"
                                        class="profile-image-clickable"
                                        style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease;">
                                @else
                                    <div id="profile-preview" class="profile-image-clickable"
                                        style="width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease;">
                                        <span style="font-size: 4rem; color: white;">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                name="profile_picture" id="profile_picture" accept="image/*" style="display: none;">
                            <small class="form-text text-muted">Click on the image above to upload. Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
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

                        @if($user->role === 'Student')
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department" class="form-control @error('department') is-invalid @enderror">
                                <option value="">Select Department</option>
                                @php
                                    $selectedDept = old('department', $user->department);
                                @endphp
                                <option value="Computer Science" {{ $selectedDept == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                <option value="CSE" {{ $selectedDept == 'CSE' ? 'selected' : '' }}>CSE (Computer Science & Engineering)</option>
                                <option value="Electrical Engineering" {{ $selectedDept == 'Electrical Engineering' ? 'selected' : '' }}>Electrical Engineering</option>
                                <option value="Mechanical Engineering" {{ $selectedDept == 'Mechanical Engineering' ? 'selected' : '' }}>Mechanical Engineering</option>
                                <option value="Civil Engineering" {{ $selectedDept == 'Civil Engineering' ? 'selected' : '' }}>Civil Engineering</option>
                                <option value="Business Administration" {{ $selectedDept == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                                <option value="Mathematics" {{ $selectedDept == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                                <option value="Physics" {{ $selectedDept == 'Physics' ? 'selected' : '' }}>Physics</option>
                                <option value="Chemistry" {{ $selectedDept == 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
                                <option value="English" {{ $selectedDept == 'English' ? 'selected' : '' }}>English</option>
                                <option value="Economics" {{ $selectedDept == 'Economics' ? 'selected' : '' }}>Economics</option>
                                <option value="Other" {{ $selectedDept == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('department')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Batch</label>
                            <input type="text" class="form-control @error('batch') is-invalid @enderror"
                                placeholder="Batch (e.g., 2021)" name="batch" value="{{ old('batch', $user->batch) }}">
                            @error('batch')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Roll No</label>
                            <input type="text" class="form-control @error('roll') is-invalid @enderror"
                                placeholder="Roll Number" name="roll" value="{{ old('roll', $user->roll) }}">
                            @error('roll')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Registration No</label>
                            <input type="text" class="form-control @error('reg_no') is-invalid @enderror"
                                placeholder="Registration Number" name="reg_no" value="{{ old('reg_no', $user->reg_no) }}">
                            @error('reg_no')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        @endif

                        @if($user->role === 'Admin')
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror"
                                placeholder="Department (Optional)" name="department" value="{{ old('department', $user->department) }}">
                            @error('department')
                                <div class="alert alert-danger" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        @endif

                        <button type="submit" name="save" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            let initialized = false;
            let retryCount = 0;
            const maxRetries = 10;

            // Wait for DOM to be ready
            function initializeProfileUpload() {
                // Prevent multiple initializations
                if (initialized) {
                    return;
                }

                const profileInput = document.getElementById('profile_picture');
                const profilePreview = document.getElementById('profile-preview');

                if (!profileInput || !profilePreview) {
                    // Retry if elements not found (with limit)
                    if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(initializeProfileUpload, 100);
                    }
                    return;
                }

                // Mark as initialized to prevent duplicate listeners
                initialized = true;

                // Function to trigger file input
                function triggerFileInput(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const input = document.getElementById('profile_picture');
                    if (input) {
                        input.click();
                    }
                }

                // Function to handle hover effects
                function handleHover(e) {
                    if (e.type === 'mouseenter') {
                        this.style.transform = 'scale(1.05)';
                        this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.2)';
                    } else {
                        this.style.transform = 'scale(1)';
                        this.style.boxShadow = 'none';
                    }
                }

                // Attach click event to preview (only if not already attached)
                if (!profilePreview.hasAttribute('data-listener-attached')) {
                    profilePreview.setAttribute('data-listener-attached', 'true');
                    profilePreview.addEventListener('click', triggerFileInput);
                    profilePreview.addEventListener('mouseenter', handleHover);
                    profilePreview.addEventListener('mouseleave', handleHover);
                }

                // Handle file input change
                profileInput.addEventListener('change', function(e) {
                    previewImage(this);
                }, { once: false });

                // Preview image function
                window.previewImage = function(input) {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('profile-preview');
                            if (!preview) return;

                            if (preview.tagName === 'IMG') {
                                preview.src = e.target.result;
                            } else {
                                // Replace div with img
                                const img = document.createElement('img');
                                img.id = 'profile-preview';
                                img.className = 'profile-image-clickable';
                                img.src = e.target.result;
                                img.alt = '{{ $user->name }}';
                                img.style.cssText = 'width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease;';

                                // Reattach event listeners to new image
                                img.setAttribute('data-listener-attached', 'true');
                                img.addEventListener('click', triggerFileInput);
                                img.addEventListener('mouseenter', handleHover);
                                img.addEventListener('mouseleave', handleHover);

                                preview.parentNode.replaceChild(img, preview);
                            }
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                };
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeProfileUpload);
            } else {
                initializeProfileUpload();
            }

            // Fallback: try again after a short delay
            setTimeout(function() {
                if (!initialized) {
                    initializeProfileUpload();
                }
            }, 300);
        })();
    </script>
@endsection

