<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        // Conditional validation based on role
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|unique:users',
            'contact' => 'nullable|string|max:20',
            'role' => 'required|in:Student,Admin',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Student requires student-specific fields
        if ($request->role === 'Student') {
            $rules['department'] = 'required|string|max:255|not_in:Select Department';
            $rules['batch'] = 'required|string|max:50';
            $rules['roll'] = 'required|string|max:50';
            $rules['reg_no'] = 'required|string|unique:users';
            // Admin department should not be submitted for Student
            $rules['admin_department'] = 'nullable';
        } else {
            // Admin - use admin_department instead
            $rules['admin_department'] = 'nullable|string|max:255';
            $rules['department'] = 'nullable|string|max:255'; // Ignore student department field
            $rules['batch'] = 'nullable|string|max:50';
            $rules['roll'] = 'nullable|string|max:50';
            $rules['reg_no'] = 'nullable|string|unique:users';
        }

        $validator = Validator::make($request->all(), $rules);

        // Custom validation message for department
        $validator->after(function ($validator) use ($request) {
            if ($request->role === 'Student') {
                // Get department value - should come from the dropdown
                $department = $request->input('department');
                // Check if it's empty, null, or the placeholder value
                if (empty($department) || $department === '' || $department === null || trim($department) === '' || $department === 'Select Department') {
                    $validator->errors()->add('department', 'The department field is required. Please select a department from the dropdown.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate verification code
        $verificationCode = Str::random(6);

        // All users (Student and Admin) require approval before they can login
        // Set is_verified = 0 and registration_status = 'pending' for all new registrations
        $registrationStatus = 'pending';
        $isVerified = false;

        // Create user
        // For Admin, only save department if provided, other fields should be null
        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'contact' => $request->contact,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'is_verified' => $isVerified,
            'registration_status' => $registrationStatus,
        ];

        // Add role-specific fields
        if ($request->role === 'Student') {
            // Student - all fields required
            // Use department field (from dropdown), ignore admin_department
            $userData['department'] = $request->department;
            $userData['batch'] = $request->batch;
            $userData['roll'] = $request->roll;
            $userData['reg_no'] = $request->reg_no;
        } else {
            // Admin - use admin_department if provided, otherwise null
            // Ignore the student department field
            $userData['department'] = $request->admin_department ?: null;
            $userData['batch'] = null;
            $userData['roll'] = null;
            $userData['reg_no'] = null;
        }

        $user = User::create($userData);

        // Student record will be created when admin approves the registration
        // (handled in UserRegistrationController::approve method)

        // Here you would send verification email/SMS
        // Mail::to($user->email)->send(new VerificationEmail($verificationCode));
        // Or send SMS with OTP

        // All registrations require approval
        return redirect()->route('login')->with('success', 'Registration submitted successfully! Your account is pending approval from an Administrator. You will be notified once approved and can then login.');
    }

    /**
     * Verify user account
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = User::where('verification_code', $request->verification_code)
            ->where('is_verified', false)
            ->first();

        if ($user) {
            $user->is_verified = true;
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();

            return redirect()->route('login')->with('success', 'Account verified successfully! You can now login.');
        }

        return redirect()->back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }
}
