<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserRegistrationController extends Controller
{
    /**
     * Display pending registrations (Admin/Librarian only)
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'Admin') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Access denied. Only Administrators can approve registrations.']);
        }

        $pendingRegistrations = User::where('registration_status', 'pending')
            ->with('approver')
            ->latest()
            ->paginate(15);

        return view('auth.pending_registrations', [
            'pendingRegistrations' => $pendingRegistrations,
        ]);
    }

    /**
     * Approve user registration (Admin/Librarian only)
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'Access denied. Only Administrators can approve registrations.']);
        }

        $pendingUser = User::findOrFail($id);
        
        if ($pendingUser->registration_status != 'pending') {
            return redirect()->back()->withErrors(['error' => 'This registration is not pending approval.']);
        }

        // Approve the registration
        $pendingUser->registration_status = 'approved';
        $pendingUser->is_verified = true;
        $pendingUser->approved_by = $user->id;
        $pendingUser->approved_at = Carbon::now();
        $pendingUser->save();

        // Create student record if role is Student
        if ($pendingUser->role === 'Student') {
            // Check if student record already exists
            $existingStudent = student::where('user_id', $pendingUser->id)->first();
            
            if (!$existingStudent) {
                student::create([
                    'name' => $pendingUser->name,
                    'email' => $pendingUser->email,
                    'phone' => $pendingUser->contact,
                    'role' => $pendingUser->role,
                    'department' => $pendingUser->department,
                    'batch' => $pendingUser->batch,
                    'roll' => $pendingUser->roll,
                    'reg_no' => $pendingUser->reg_no,
                    'user_id' => $pendingUser->id,
                    'borrowing_limit' => 5,
                    'class' => $pendingUser->department ?? 'General',
                    'age' => 'N/A',
                    'gender' => 'N/A',
                    'address' => $pendingUser->department ?? 'N/A',
                ]);
            }
        }

        return redirect()->route('registrations.pending')->with('success', 'Registration approved successfully! User can now login.');
    }

    /**
     * Reject user registration (Admin/Librarian only)
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'Access denied. Only Administrators can reject registrations.']);
        }

        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ], [
                'rejection_reason.required' => 'Please provide a reason for rejection.',
                'rejection_reason.max' => 'Rejection reason cannot exceed 500 characters.',
            ]);

            $pendingUser = User::findOrFail($id);
            
            if ($pendingUser->registration_status != 'pending') {
                return redirect()->back()->withErrors(['error' => 'This registration is not pending approval.']);
            }

            // Reject the registration
            $pendingUser->registration_status = 'rejected';
            $pendingUser->rejection_reason = trim($request->rejection_reason);
            $pendingUser->save();

            return redirect()->route('registrations.pending')->with('success', 'Registration rejected successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred while rejecting the registration. Please try again.']);
        }
    }
}
