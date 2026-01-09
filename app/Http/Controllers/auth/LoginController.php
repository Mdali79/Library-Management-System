<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => "required",
            'password' => "required",
        ]);

        // First, find the user by username to check verification status BEFORE attempting login
        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user) {
            // Check if user is verified (is_verified must be 1) BEFORE attempting authentication
            if (!$user->is_verified) {
                return redirect()->back()->withErrors([
                    'username' => 'Your account is not verified yet. Please wait for an Administrator to approve your registration. You will be able to login once your account is verified.'
                ])->withInput($request->only('username'));
            }
            
            // Check if registration is approved
            if ($user->registration_status == 'pending') {
                return redirect()->back()->withErrors([
                    'username' => 'Your account is pending approval. Please wait for an Administrator to approve your registration.'
                ])->withInput($request->only('username'));
            }
            
            if ($user->registration_status == 'rejected') {
                return redirect()->back()->withErrors([
                    'username' => 'Your registration has been rejected. Please contact the administrator for more information.'
                ])->withInput($request->only('username'));
            }
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            return redirect('/dashboard');
        } else {
            return redirect()->back()->withErrors(['username' => 'Invalid username or password'])->withInput($request->only('username'));
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    // change_password method
    public function changePassword(Request $request)
    {
        $request->validate([
            'c_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        if (password_verify($request->c_password, $user->password)) {
            $user->password = bcrypt($request->password);
            $user->save();
            return redirect()->back()->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->withErrors(['c_password' => 'Old password is incorrect']);
        }
    }
}
