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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|unique:users',
            'contact' => 'nullable|string|max:20',
            'role' => 'required|in:Student,Teacher,Librarian,Admin',
            'department' => 'nullable|string|max:255',
            'batch' => 'nullable|string|max:50',
            'roll' => 'nullable|string|max:50',
            'reg_no' => 'nullable|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate verification code
        $verificationCode = Str::random(6);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'contact' => $request->contact,
            'role' => $request->role,
            'department' => $request->department,
            'batch' => $request->batch,
            'roll' => $request->roll,
            'reg_no' => $request->reg_no,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'is_verified' => false, // Set to true if email/SMS verification is implemented
        ]);

        // Create student record if role is Student, Teacher, or Librarian
        if (in_array($request->role, ['Student', 'Teacher', 'Librarian'])) {
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
            ]);
        }

        // Here you would send verification email/SMS
        // Mail::to($user->email)->send(new VerificationEmail($verificationCode));
        // Or send SMS with OTP

        return redirect()->route('login')->with('success', 'Registration successful! Please verify your email/contact to activate your account.');
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
