<?php

namespace App\Http\Controllers\Auth;

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
            'role' => 'required|in:Student,Teacher,Librarian,Admin',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Student/Teacher require student-specific fields
        if (in_array($request->role, ['Student', 'Teacher'])) {
            $rules['department'] = 'required|string|max:255';
            $rules['batch'] = 'required|string|max:50';
            $rules['roll'] = 'required|string|max:50';
            $rules['reg_no'] = 'required|string|unique:users';
        } else {
            // Admin/Librarian - these fields are optional
            $rules['department'] = 'nullable|string|max:255';
            $rules['batch'] = 'nullable|string|max:50';
            $rules['roll'] = 'nullable|string|max:50';
            $rules['reg_no'] = 'nullable|string|unique:users';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate verification code
        $verificationCode = Str::random(6);

        // Determine registration status based on role
        // Student/Teacher: Auto-approved
        // Admin/Librarian: Requires approval
        $registrationStatus = in_array($request->role, ['Student', 'Teacher']) ? 'approved' : 'pending';
        $isVerified = in_array($request->role, ['Student', 'Teacher']) ? true : false;

        // Create user
        // For Admin/Librarian, only save department if provided, other fields should be null
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
        if (in_array($request->role, ['Student', 'Teacher'])) {
            // Student/Teacher - all fields required
            $userData['department'] = $request->department;
            $userData['batch'] = $request->batch;
            $userData['roll'] = $request->roll;
            $userData['reg_no'] = $request->reg_no;
        } else {
            // Admin/Librarian - only department is optional, others should be null
            $userData['department'] = $request->department ?: null;
            $userData['batch'] = null;
            $userData['roll'] = null;
            $userData['reg_no'] = null;
        }

        $user = User::create($userData);

        // Create student record if role is Student, Teacher, or Librarian (only if approved)
        if (in_array($request->role, ['Student', 'Teacher', 'Librarian']) && $registrationStatus == 'approved') {
            student::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->contact,
                'role' => $request->role,
                'department' => $request->department,
                'batch' => $request->batch,
                'roll' => $request->roll,
                'reg_no' => $request->reg_no,
                'user_id' => $user->id,
                'borrowing_limit' => $request->role == 'Teacher' ? 10 : ($request->role == 'Librarian' ? 15 : 5),
                'class' => $request->department ?? 'General',
                'age' => 'N/A',
                'gender' => 'N/A',
                'address' => $request->department ?? 'N/A',
            ]);
        }

        // Here you would send verification email/SMS
        // Mail::to($user->email)->send(new VerificationEmail($verificationCode));
        // Or send SMS with OTP

        // Different messages based on registration status
        if ($registrationStatus == 'pending') {
            return redirect()->route('login')->with('success', 'Registration submitted successfully! Your account is pending approval from an Administrator or Librarian. You will be notified once approved.');
        } else {
            return redirect()->route('login')->with('success', 'Registration successful! You can now login with your credentials.');
        }
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
