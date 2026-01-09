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
            'email' => 'nullable|email:rfc,dns|max:255|unique:users',
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

        // Custom validation message for department and email
        $validator->after(function ($validator) use ($request) {
            if ($request->role === 'Student') {
                // Get department value - should come from the dropdown
                $department = $request->input('department');
                // Check if it's empty, null, or the placeholder value
                if (empty($department) || $department === '' || $department === null || trim($department) === '' || $department === 'Select Department') {
                    $validator->errors()->add('department', 'The department field is required. Please select a department from the dropdown.');
                }
            }
            
            // Additional email validation - ensure email is valid if provided
            if ($request->filled('email')) {
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
