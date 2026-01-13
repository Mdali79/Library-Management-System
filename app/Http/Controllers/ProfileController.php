<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\student;

class ProfileController extends Controller
{
    /**
     * Show the user profile
     */
    public function show()
    {
        $user = Auth::user();
        // Refresh user to ensure we have the latest data
        $user->refresh();

        \Log::info('Profile show page accessed', [
            'user_id' => $user->id,
            'has_profile_picture' => !empty($user->profile_picture),
            'profile_picture_path' => $user->profile_picture ?? 'null'
        ]);

        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Add role-specific validation rules
        if ($user->role === 'Student') {
            $validationRules['department'] = 'nullable|string|max:255';
            $validationRules['batch'] = 'nullable|string|max:50';
            $validationRules['roll'] = 'nullable|string|max:50';
            $validationRules['reg_no'] = 'nullable|string|max:50|unique:users,reg_no,' . $user->id;
        } elseif ($user->role === 'Admin') {
            $validationRules['department'] = 'nullable|string|max:255';
        }

        $request->validate($validationRules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
        ];

        // Add role-specific fields
        if ($user->role === 'Student') {
            $data['department'] = $request->department;
            $data['batch'] = $request->batch;
            $data['roll'] = $request->roll;
            $data['reg_no'] = $request->reg_no;
        } elseif ($user->role === 'Admin') {
            $data['department'] = $request->department;
        }

        // Handle profile picture removal
        if ($request->has('remove_profile_picture') && $request->remove_profile_picture == '1') {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }
            $data['profile_picture'] = null;
        }
        // Handle profile picture upload (only if not removing)
        elseif ($request->hasFile('profile_picture')) {
            \Log::info('Profile picture upload detected', [
                'user_id' => $user->id,
                'file_name' => $request->file('profile_picture')->getClientOriginalName(),
                'file_size' => $request->file('profile_picture')->getSize(),
                'file_type' => $request->file('profile_picture')->getMimeType()
            ]);

            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
                \Log::info('Old profile picture deleted', ['path' => $user->profile_picture]);
            }

            $image = $request->file('profile_picture');
            $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image->getClientOriginalName());
            $storedPath = $image->storeAs('public/profile_pictures', $imageName);

            if ($storedPath) {
                $data['profile_picture'] = 'profile_pictures/' . $imageName;
                \Log::info('Profile picture stored successfully', [
                    'stored_path' => $storedPath,
                    'profile_picture' => $data['profile_picture']
                ]);
            } else {
                \Log::error('Failed to store profile picture');
                return redirect()->back()
                    ->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.'])
                    ->withInput();
            }
        }

        $user->update($data);

        // Refresh the user model to get updated attributes
        $user->refresh();

        \Log::info('Profile updated', [
            'user_id' => $user->id,
            'has_profile_picture' => !empty($user->profile_picture),
            'profile_picture_path' => $user->profile_picture
        ]);

        // Verify the image file exists
        if (!empty($user->profile_picture)) {
            $imageExists = Storage::disk('public')->exists($user->profile_picture);
            \Log::info('Profile picture verification', [
                'path' => $user->profile_picture,
                'exists' => $imageExists,
                'full_path' => Storage::disk('public')->path($user->profile_picture)
            ]);
        }

        // Update student record if user is a Student
        if ($user->role === 'Student') {
            $studentRecord = student::where('user_id', $user->id)->first();
            if ($studentRecord) {
                $studentRecord->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->contact,
                    'department' => $user->department,
                    'batch' => $user->batch,
                    'roll' => $user->roll,
                    'reg_no' => $user->reg_no,
                ]);
            }
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully');
    }
}
