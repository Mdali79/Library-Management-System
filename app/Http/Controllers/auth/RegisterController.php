<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\OtpVerificationMail;

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
     * IMPORTANT: This method only stores registration data in session and sends OTP.
     * User account is ONLY created in verifyOtp() after OTP verification.
     */
    public function register(Request $request)
    {
        \Log::info('Registration request received', [
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId()
        ]);

        // Conditional validation based on role
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email:rfc,dns|max:255|unique:users',
            'contact' => 'nullable|string|max:20',
            'role' => 'required|in:Student,Admin',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z]{2,})(?=.*[0-9]{2,}).{8,}$/',
            ],
            'terms' => 'required|accepted',
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

        // Custom validation message for department, email, and password
        $validator->after(function ($validator) use ($request) {
            // Password pattern validation
            if ($request->filled('password')) {
                $password = $request->input('password');
                $letters = preg_match_all('/[A-Za-z]/', $password);
                $numbers = preg_match_all('/[0-9]/', $password);
                $length = strlen($password);

                if ($length < 8) {
                    $validator->errors()->add('password', 'Password must be at least 8 characters long.');
                } elseif ($letters < 2) {
                    $validator->errors()->add('password', 'Password must contain at least 2 letters.');
                } elseif ($numbers < 2) {
                    $validator->errors()->add('password', 'Password must contain at least 2 numbers.');
                }
            }

            if ($request->role === 'Student') {
                // Get department value - should come from the dropdown
                $department = $request->input('department');
                // Check if it's empty, null, or the placeholder value
                if (empty($department) || $department === '' || $department === null || trim($department) === '' || $department === 'Select Department') {
                    $validator->errors()->add('department', 'The department field is required. Please select a department from the dropdown.');
                }
            }

            // Additional email validation - ensure email is valid
            $email = trim($request->input('email'));
            // Check if email format is valid using filter_var
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validator->errors()->add('email', 'Please enter a valid email address.');
            } else {
                // Additional check for proper domain format
                $parts = explode('@', $email);
                if (count($parts) !== 2 || empty($parts[0]) || empty($parts[1]) || !strpos($parts[1], '.')) {
                    $validator->errors()->add('email', 'Please enter a valid email address with a proper domain (e.g., user@example.com).');
                } else {
                    // Check if domain exists and has MX records (can receive emails)
                    $domain = $parts[1];
                    if (!$this->validateEmailDomain($domain)) {
                        $validator->errors()->add('email', 'The email domain does not exist or cannot receive emails. Please enter a valid email address.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(15);

        // Store registration data in session (don't create user yet)
        $registrationData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'contact' => $request->contact,
            'role' => $request->role,
            'password' => $request->password, // Store plain password to hash later
        ];

        // Add role-specific fields
        if ($request->role === 'Student') {
            $registrationData['department'] = $request->department;
            $registrationData['batch'] = $request->batch;
            $registrationData['roll'] = $request->roll;
            $registrationData['reg_no'] = $request->reg_no;
        } else {
            $registrationData['department'] = $request->admin_department ?: null;
            $registrationData['batch'] = null;
            $registrationData['roll'] = null;
            $registrationData['reg_no'] = null;
        }

        // Store in session
        session([
            'registration_data' => $registrationData,
            'otp_code' => $otp,
            'otp_expires_at' => $otpExpiresAt->timestamp,
        ]);

        // CRITICAL: Send OTP email - Registration CANNOT proceed without this
        \Log::info('Attempting to send OTP email', [
            'email' => $request->email,
            'name' => $request->name,
            'role' => $registrationData['role'],
            'session_id' => session()->getId()
        ]);

        try {
            Mail::to($request->email)->send(new OtpVerificationMail($otp, $request->name));
            \Log::info('OTP email sent successfully', [
                'email' => $request->email,
                'otp' => $otp,
                'session_id' => session()->getId()
            ]);
        } catch (\Exception $e) {
            // CRITICAL: Log the error and STOP registration
            \Log::error('CRITICAL: Failed to send OTP email - Registration stopped', [
                'email' => $request->email,
                'name' => $request->name,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId()
            ]);
            
            // Clear session to prevent any bypass attempts
            session()->forget(['registration_data', 'otp_code', 'otp_expires_at']);
            
            // Return error - DO NOT proceed with registration
            return redirect()->back()
                ->withErrors(['email' => 'Failed to send verification email. Please check your email configuration. Error: ' . $e->getMessage()])
                ->withInput();
        }

        // Verify session was saved before redirecting
        if (!session()->has('registration_data') || !session()->has('otp_code')) {
            \Log::error('CRITICAL: Session data lost after email send - Registration stopped', [
                'email' => $request->email,
                'has_registration_data' => session()->has('registration_data'),
                'has_otp_code' => session()->has('otp_code'),
                'session_id' => session()->getId()
            ]);
            session()->forget(['registration_data', 'otp_code', 'otp_expires_at']);
            return redirect()->back()
                ->withErrors(['error' => 'Session error occurred. Please try registering again.'])
                ->withInput();
        }

        \Log::info('Redirecting to OTP verification page', [
            'email' => $request->email,
            'session_id' => session()->getId()
        ]);

        // Redirect to OTP verification page
        return redirect()->route('verify.otp')->with('success', 'A verification code has been sent to your email address. Please check your inbox and enter the code below.');
    }

    /**
     * Show OTP verification page
     */
    public function showOtpVerification()
    {
        // Check if registration data exists in session
        if (!session()->has('registration_data') || !session()->has('otp_code')) {
            \Log::warning('OTP verification page accessed without session data', [
                'has_registration_data' => session()->has('registration_data'),
                'has_otp_code' => session()->has('otp_code'),
                'session_id' => session()->getId()
            ]);
            return redirect()->route('register')
                ->withErrors(['error' => 'Your registration session has expired or was not found. Please register again. If this problem persists, check your session configuration.']);
        }

        $registrationData = session('registration_data');
        $email = $registrationData['email'] ?? '';

        // Mask email for display
        $maskedEmail = $this->maskEmail($email);

        return view('auth.verify_otp', [
            'email' => $email,
            'maskedEmail' => $maskedEmail,
            'otpExpiresAt' => session('otp_expires_at'),
        ]);
    }

    /**
     * Verify OTP and create user account
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        // Check if registration data exists in session
        if (!session()->has('registration_data') || !session()->has('otp_code')) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Your registration session has expired. Please register again.']);
        }

        $storedOtp = session('otp_code');
        $otpExpiresAt = session('otp_expires_at');
        $registrationData = session('registration_data');

        // Check if OTP is expired
        if (Carbon::now()->timestamp > $otpExpiresAt) {
            return redirect()->back()
                ->withErrors(['otp' => 'The verification code has expired. Please request a new code.']);
        }

        // Verify OTP
        if ($request->otp != $storedOtp) {
            return redirect()->back()
                ->withErrors(['otp' => 'Invalid verification code. Please check and try again.']);
        }

        // OTP is valid, create user account
        \Log::info('OTP verified successfully - Creating user account', [
            'email' => $registrationData['email'],
            'username' => $registrationData['username'],
            'role' => $registrationData['role'],
            'session_id' => session()->getId()
        ]);

        $userData = [
            'name' => $registrationData['name'],
            'username' => $registrationData['username'],
            'email' => $registrationData['email'],
            'contact' => $registrationData['contact'],
            'role' => $registrationData['role'],
            'password' => Hash::make($registrationData['password']),
            'is_verified' => true, // Email verified via OTP
            'email_verified_at' => now(),
            'registration_status' => 'pending', // CRITICAL: Must be pending until admin approves
        ];

        // Add role-specific fields
        if ($registrationData['role'] === 'Student') {
            $userData['department'] = $registrationData['department'];
            $userData['batch'] = $registrationData['batch'];
            $userData['roll'] = $registrationData['roll'];
            $userData['reg_no'] = $registrationData['reg_no'];
        } else {
            $userData['department'] = $registrationData['department'];
            $userData['batch'] = null;
            $userData['roll'] = null;
            $userData['reg_no'] = null;
        }

        $user = User::create($userData);
        
        \Log::info('User created after OTP verification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'is_verified' => $user->is_verified,
            'registration_status' => $user->registration_status,
            'session_id' => session()->getId()
        ]);

        // Clear session data
        session()->forget(['registration_data', 'otp_code', 'otp_expires_at']);

        // Student record will be created when admin approves the registration
        // (handled in UserRegistrationController::approve method)

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! Your account has been created and is pending administrator approval. You will be able to login once approved.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        // Check if registration data exists in session
        if (!session()->has('registration_data')) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Your registration session has expired. Please register again.']);
        }

        $registrationData = session('registration_data');

        // Generate new OTP
        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(15);

        // Update session with new OTP
        session([
            'otp_code' => $otp,
            'otp_expires_at' => $otpExpiresAt->timestamp,
        ]);

        // Send new OTP email
        try {
            Mail::to($registrationData['email'])->send(new OtpVerificationMail($otp, $registrationData['name']));

            return redirect()->back()
                ->with('success', 'A new verification code has been sent to your email address.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to send verification email. Please try again.']);
        }
    }

    /**
     * Mask email for display (e.g., user@example.com -> u***@e***.com)
     */
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

        // Mask username (show first character, mask rest)
        $maskedUsername = strlen($username) > 1
            ? substr($username, 0, 1) . str_repeat('*', min(3, strlen($username) - 1))
            : $username;

        // Mask domain (show first character of domain name)
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

    /**
     * Verify user account (old method - kept for backward compatibility)
     * Note: This only verifies email, but admin approval is still required for login
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
            // Only set email verification, but keep registration_status as pending
            // Admin approval is still required for both Student and Admin roles
            $user->is_verified = true;
            $user->email_verified_at = now();
            $user->verification_code = null;
            // Do NOT change registration_status - it must remain 'pending' until admin approves
            $user->save();

            return redirect()->route('login')->with('success', 'Email verified successfully! However, your account still requires administrator approval before you can login.');
        }

        return redirect()->back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }

    /**
     * Validate if email domain exists and can receive emails
     *
     * @param string $domain
     * @return bool
     */
    private function validateEmailDomain($domain)
    {
        // Check if domain is valid format
        if (empty($domain) || !preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $domain)) {
            return false;
        }

        // Check if domain has MX records (mail exchange records)
        // This verifies the domain can receive emails
        $mxRecords = [];
        $hasMx = @getmxrr($domain, $mxRecords);

        // If no MX records, check if domain resolves (has A record)
        // Some mail servers use A records instead of MX records
        if (!$hasMx) {
            // Check if domain resolves to an IP address
            $ip = @gethostbyname($domain);
            if ($ip === $domain) {
                // Domain doesn't resolve to an IP (doesn't exist)
                return false;
            }
        }

        // Domain exists and can potentially receive emails
        return true;
    }
}
