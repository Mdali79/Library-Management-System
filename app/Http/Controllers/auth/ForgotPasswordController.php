<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form (enter email).
     */
    public function showForgotForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * Send OTP to email and redirect to verify OTP page.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()
                ->withErrors(['email' => 'No account found with this email address.'])
                ->withInput();
        }

        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(15);

        session([
            'password_reset_email' => $request->email,
            'password_reset_otp_code' => $otp,
            'password_reset_otp_expires_at' => $otpExpiresAt->timestamp,
        ]);

        try {
            Mail::to($request->email)->send(new ForgotPasswordOtpMail($otp, $user->name));
        } catch (\Exception $e) {
            Log::error('Forgot password OTP email failed', ['email' => $request->email, 'error' => $e->getMessage()]);
            return redirect()->back()
                ->withErrors(['email' => 'Failed to send verification code. Please try again.'])
                ->withInput();
        }

        return redirect()->route('password.verify.otp')
            ->with('success', 'A verification code has been sent to your email. Please enter it below.');
    }

    /**
     * Show OTP verification page for password reset.
     */
    public function showVerifyOtp()
    {
        if (!session()->has('password_reset_email') || !session()->has('password_reset_otp_code')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Session expired. Please request a new verification code.']);
        }

        $email = session('password_reset_email');
        $maskedEmail = $this->maskEmail($email);

        return view('auth.forgot_password_verify_otp', [
            'maskedEmail' => $maskedEmail,
            'otpExpiresAt' => session('password_reset_otp_expires_at'),
        ]);
    }

    /**
     * Verify OTP and redirect to reset password form.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        if (!session()->has('password_reset_email') || !session()->has('password_reset_otp_code')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Session expired. Please request a new verification code.']);
        }

        $storedOtp = session('password_reset_otp_code');
        $otpExpiresAt = session('password_reset_otp_expires_at');

        if (Carbon::now()->timestamp > $otpExpiresAt) {
            return redirect()->back()
                ->withErrors(['otp' => 'The verification code has expired. Please request a new code.']);
        }

        if ($request->otp != $storedOtp) {
            return redirect()->back()
                ->withErrors(['otp' => 'Invalid verification code. Please check and try again.']);
        }

        session(['password_reset_verified' => true]);

        return redirect()->route('password.reset.form')
            ->with('success', 'Email verified. Enter your new password below.');
    }

    /**
     * Resend OTP for password reset.
     */
    public function resendOtp()
    {
        if (!session()->has('password_reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Session expired. Please enter your email again.']);
        }

        $email = session('password_reset_email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            session()->forget(['password_reset_email', 'password_reset_otp_code', 'password_reset_otp_expires_at']);
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Invalid session. Please try again.']);
        }

        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(15);

        session([
            'password_reset_otp_code' => $otp,
            'password_reset_otp_expires_at' => $otpExpiresAt->timestamp,
        ]);

        try {
            Mail::to($email)->send(new ForgotPasswordOtpMail($otp, $user->name));
            return redirect()->back()
                ->with('success', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to send verification email. Please try again.']);
        }
    }

    /**
     * Show reset password form (new password + confirm).
     */
    public function showResetForm()
    {
        if (!session()->has('password_reset_email') || !session()->get('password_reset_verified')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Session expired. Please start the process again.']);
        }

        return view('auth.reset_password');
    }

    /**
     * Update password and redirect to login.
     */
    public function resetPassword(Request $request)
    {
        if (!session()->has('password_reset_email') || !session()->get('password_reset_verified')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Session expired. Please start the process again.']);
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z]{2,})(?=.*[0-9]{2,}).{8,}$/',
            ],
        ], [
            'password.regex' => 'Password must contain at least 2 letters and 2 numbers.',
        ]);

        $user = User::where('email', session('password_reset_email'))->firstOrFail();
        $user->update(['password' => Hash::make($request->password)]);

        session()->forget(['password_reset_email', 'password_reset_otp_code', 'password_reset_otp_expires_at', 'password_reset_verified']);

        return redirect()->route('login')
            ->with('success', 'Your password has been reset. You can now login with your new password.');
    }

    private function maskEmail($email)
    {
        if (empty($email)) {
            return '';
        }
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }
        $username = $parts[0];
        $domain = $parts[1];
        $maskedUsername = strlen($username) > 1
            ? substr($username, 0, 1) . str_repeat('*', min(3, strlen($username) - 1))
            : $username;
        $domainParts = explode('.', $domain);
        if (count($domainParts) >= 2) {
            $domainName = $domainParts[0];
            $domainExt = implode('.', array_slice($domainParts, 1));
            $maskedDomain = substr($domainName, 0, 1) . str_repeat('*', min(2, strlen($domainName) - 1)) . '.' . $domainExt;
        } else {
            $maskedDomain = substr($domain, 0, 1) . str_repeat('*', min(2, strlen($domain) - 1));
        }
        return $maskedUsername . '@' . $maskedDomain;
    }
}
