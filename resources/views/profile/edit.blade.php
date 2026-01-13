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
                            <div class="mb-3" style="position: relative;">
                                @php
                                    $hasProfilePicture = !empty($user->profile_picture) && $user->profile_picture !== null && $user->profile_picture !== '';
                                @endphp
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                    name="profile_picture" id="profile_picture" accept="image/*" style="display: none;">
                                <label for="profile_picture" style="cursor: pointer; display: inline-block; margin: 0; position: relative; z-index: 1;">
                                    @if($hasProfilePicture)
                                        @php
                                            // Use asset() which respects the current request host
                                            $profileImageUrl = asset('storage/' . $user->profile_picture);
                                            // Add cache buster to ensure fresh image after update
                                            $profileImageUrl .= '?v=' . time();
                                        @endphp
                                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                                            <img src="{{ $profileImageUrl }}"
                                                alt="{{ $user->name }}"
                                                id="profile-preview"
                                                class="profile-image-clickable"
                                                style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease; display: block; position: relative; z-index: 1;"
                                                onerror="this.onerror=null; this.style.display='none'; var fallback = document.getElementById('profile-preview-fallback'); if(fallback) fallback.style.display='flex';">
                                            <div id="profile-preview-fallback" class="profile-image-clickable" style="display: none; position: absolute; top: 0; left: 0; width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); align-items: center; justify-content: center; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease; z-index: 2;">
                                                <span style="font-size: 4rem; color: white;">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div id="profile-preview" class="profile-image-clickable"
                                            style="width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease;">
                                            <span style="font-size: 4rem; color: white;">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                    @endif
                                </label>
                            </div>
                            <small class="form-text text-muted">Click on the image above to upload. Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                            <div class="mt-3" id="remove-profile-section" style="{{ $hasProfilePicture ? '' : 'display: none;' }}">
                                <button type="button" class="btn btn-outline-danger btn-sm" id="remove-profile-btn" style="width: 100%;" {{ $hasProfilePicture ? '' : 'disabled' }}>
                                    <i class="fas fa-trash-alt"></i> Remove Profile Picture
                                </button>
                                <input type="hidden" name="remove_profile_picture" id="remove_profile_picture" value="0">
                                <small class="form-text text-danger mt-2" id="remove-warning" style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i> Profile picture will be removed and default avatar (human icon) will be used after you click "Update Profile".
                                </small>
                            </div>
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
                            <select name="batch" class="form-control @error('batch') is-invalid @enderror">
                                <option value="">Select Batch</option>
                                @php
                                    $selectedBatch = old('batch', $user->batch);
                                    $batches = [];
                                    // Generate batches from 90th to 120th
                                    for ($i = 90; $i <= 120; $i++) {
                                        $batches[] = $i . 'th';
                                    }
                                @endphp
                                @foreach($batches as $batch)
                                    <option value="{{ $batch }}" {{ $selectedBatch == $batch ? 'selected' : '' }}>{{ $batch }}</option>
                                @endforeach
                            </select>
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

                        <button type="submit" name="save" class="btn btn-primary btn-lg btn-block" style="background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
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
                const profilePreviewFallback = document.getElementById('profile-preview-fallback');

                if (!profileInput) {
                    // Retry if input not found (with limit)
                    if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(initializeProfileUpload, 100);
                    }
                    return;
                }

                // Mark as initialized to prevent duplicate listeners
                initialized = true;

                // Test if file input is accessible
                console.log('Profile input found:', profileInput);
                console.log('Profile preview found:', profilePreview);

                // Direct click handler for label (backup method)
                const label = profileInput ? profileInput.nextElementSibling : null;
                if (label && label.tagName === 'LABEL') {
                    label.addEventListener('click', function(e) {
                        console.log('Label clicked');
                        if (profileInput) {
                            profileInput.click();
                        }
                    });
                }

                // Also add click handler directly to preview elements as backup
                function triggerFileSelect(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Preview clicked, triggering file input');
                    if (profileInput) {
                        profileInput.click();
                    }
                }

                if (profilePreview) {
                    profilePreview.addEventListener('click', triggerFileSelect);
                }
                if (profilePreviewFallback) {
                    profilePreviewFallback.addEventListener('click', triggerFileSelect);
                }

                // Function to handle hover effects
                function handleHover(e) {
                    const target = e.currentTarget;
                    if (e.type === 'mouseenter') {
                        target.style.transform = 'scale(1.05)';
                        target.style.boxShadow = '0 8px 16px rgba(0,0,0,0.2)';
                    } else {
                        target.style.transform = 'scale(1)';
                        target.style.boxShadow = 'none';
                    }
                }

                // Attach hover effects to preview image (if exists)
                if (profilePreview) {
                    if (!profilePreview.hasAttribute('data-hover-attached')) {
                        profilePreview.setAttribute('data-hover-attached', 'true');
                        profilePreview.addEventListener('mouseenter', handleHover);
                        profilePreview.addEventListener('mouseleave', handleHover);
                    }
                }

                // Attach hover effects to fallback div (if exists)
                if (profilePreviewFallback) {
                    if (!profilePreviewFallback.hasAttribute('data-hover-attached')) {
                        profilePreviewFallback.setAttribute('data-hover-attached', 'true');
                        profilePreviewFallback.addEventListener('mouseenter', handleHover);
                        profilePreviewFallback.addEventListener('mouseleave', handleHover);
                    }
                }

                // Handle file input change - use multiple methods to ensure it works
                function handleFileChange(e) {
                    console.log('File input changed', this.files);
                    if (this.files && this.files[0]) {
                        previewImage(this);
                    }
                }

                // Remove any existing listeners and add new one
                profileInput.removeEventListener('change', handleFileChange);
                profileInput.addEventListener('change', handleFileChange);

                // Also listen for input event as backup
                profileInput.addEventListener('input', handleFileChange);

                // Preview image function - make it globally accessible
                window.previewImage = function(input) {
                    console.log('previewImage called', input);
                    if (!input) {
                        input = document.getElementById('profile_picture');
                    }
                    if (!input || !input.files || !input.files[0]) {
                        console.log('No file selected');
                        return;
                    }

                    const file = input.files[0];
                    console.log('File selected:', file.name, file.type, file.size);

                    // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('Please select an image file');
                        return;
                    }

                    // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image size must be less than 2MB');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        console.log('FileReader loaded, result length:', e.target.result.length);
                        const preview = document.getElementById('profile-preview');
                        const fallback = document.getElementById('profile-preview-fallback');

                        if (!preview) {
                            console.error('Preview element not found');
                            alert('Error: Preview element not found');
                            return;
                        }

                        // Hide fallback if it exists
                        if (fallback) {
                            fallback.style.display = 'none';
                        }

                        if (preview.tagName === 'IMG') {
                            // Update existing image
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                            console.log('Updated existing image src');
                        } else {
                            // Replace div with img
                            const img = document.createElement('img');
                            img.id = 'profile-preview';
                            img.className = 'profile-image-clickable';
                            img.src = e.target.result;
                            img.alt = '{{ $user->name }}';
                            img.style.cssText = 'width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd; cursor: pointer; transition: all 0.3s ease; display: block; margin: 0 auto;';

                            // Reattach hover effects to new image
                            img.setAttribute('data-hover-attached', 'true');
                            img.addEventListener('mouseenter', handleHover);
                            img.addEventListener('mouseleave', handleHover);

                                // Get the label parent to replace within it
                                const label = preview.closest('label');
                                if (label) {
                                    // Replace preview within label
                                    label.replaceChild(img, preview);
                                    console.log('Replaced div with img inside label');
                                    // Reattach click handler to new image
                                    img.addEventListener('click', triggerFileSelect);
                                } else {
                                    // Fallback: replace in parent
                                    preview.parentNode.replaceChild(img, preview);
                                    console.log('Replaced div with img in parent');
                                    // Reattach click handler to new image
                                    img.addEventListener('click', triggerFileSelect);
                                }

                                // Update the preview reference for future use
                                window.currentProfilePreview = img;
                        }

                        // Show remove button section when new image is selected
                        const removeSection = document.getElementById('remove-profile-section');
                        const removeBtn = document.getElementById('remove-profile-btn');
                        if (removeSection) {
                            removeSection.style.display = 'block';
                        }
                        if (removeBtn) {
                            removeBtn.disabled = false;
                        }

                        // Reset remove button when new image is selected
                        const removeInput = document.getElementById('remove_profile_picture');
                        if (removeInput) {
                            removeInput.value = '0';
                        }
                        if (removeBtn) {
                            removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Remove Profile Picture';
                            removeBtn.classList.remove('btn-outline-secondary');
                            removeBtn.classList.add('btn-outline-danger');
                        }
                        const removeWarning = document.getElementById('remove-warning');
                        if (removeWarning) {
                            removeWarning.style.display = 'none';
                        }
                    };

                    reader.onerror = function(error) {
                        console.error('FileReader error:', error);
                        alert('Error reading file. Please try again.');
                    };

                    reader.onprogress = function(e) {
                        if (e.lengthComputable) {
                            const percentLoaded = Math.round((e.loaded / e.total) * 100);
                            console.log('FileReader progress:', percentLoaded + '%');
                        }
                    };

                    reader.readAsDataURL(file);
                };

                // Handle remove profile picture button
                const removeBtn = document.getElementById('remove-profile-btn');
                const removeInput = document.getElementById('remove_profile_picture');
                const removeWarning = document.getElementById('remove-warning');
                const removeSection = document.getElementById('remove-profile-section');
                let isRemoving = false;
                let originalImageSrc = null;

                // Store original image source if it exists
                const preview = document.getElementById('profile-preview');
                if (preview && preview.tagName === 'IMG') {
                    originalImageSrc = preview.src;
                }

                if (removeBtn && removeInput) {

                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        isRemoving = !isRemoving;

                        if (isRemoving) {
                            // Set hidden input to 1 (remove)
                            removeInput.value = '1';

                            // Clear file input
                            const fileInput = document.getElementById('profile_picture');
                            if (fileInput) {
                                fileInput.value = '';
                            }

                            // Update button and show warning
                            removeBtn.innerHTML = '<i class="fas fa-undo"></i> Cancel Removal';
                            removeBtn.classList.remove('btn-outline-danger');
                            removeBtn.classList.add('btn-outline-secondary');

                            if (removeWarning) {
                                removeWarning.style.display = 'block';
                            }
                        } else {
                            // Set hidden input to 0 (don't remove)
                            removeInput.value = '0';

                            // Update button and hide warning
                            removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Remove Profile Picture';
                            removeBtn.classList.remove('btn-outline-secondary');
                            removeBtn.classList.add('btn-outline-danger');

                            if (removeWarning) {
                                removeWarning.style.display = 'none';
                            }
                        }
                    });
                }
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

            // Also try after window load
            window.addEventListener('load', function() {
                if (!initialized) {
                    initializeProfileUpload();
                }
            });
        })();
    </script>
@endsection

