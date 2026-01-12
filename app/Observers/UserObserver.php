<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     * Log and verify user creation to track registration flow.
     */
    public function creating(User $user): void
    {
        // Log stack trace to see where user is being created from
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $caller = '';
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && isset($trace['function'])) {
                $caller = $trace['class'] . '::' . $trace['function'];
                if (strpos($caller, 'RegisterController') !== false || 
                    strpos($caller, 'UserRegistrationController') !== false ||
                    strpos($caller, 'UserObserver') === false) {
                    break;
                }
            }
        }

        Log::info('User being created', [
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'is_verified' => $user->is_verified,
            'registration_status' => $user->registration_status,
            'created_from' => $caller
        ]);

        // CRITICAL: Ensure registration_status is set
        // If not set, default to 'pending' to prevent unauthorized access
        if (empty($user->registration_status)) {
            Log::warning('User created without registration_status - setting to pending', [
                'email' => $user->email,
                'username' => $user->username
            ]);
            $user->registration_status = 'pending';
        }

        // If user is created with is_verified=true but registration_status is not 'approved',
        // ensure registration_status is 'pending' (requires admin approval)
        if ($user->is_verified && $user->registration_status !== 'approved') {
            // This is normal for OTP-verified users - they still need admin approval
            // But log it to track the flow
            if ($user->registration_status !== 'pending') {
                Log::warning('User created with is_verified=true but invalid registration_status', [
                    'email' => $user->email,
                    'username' => $user->username,
                    'is_verified' => $user->is_verified,
                    'registration_status' => $user->registration_status,
                    'setting_to' => 'pending'
                ]);
                $user->registration_status = 'pending';
            }
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Log::info('User created successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'is_verified' => $user->is_verified,
            'registration_status' => $user->registration_status,
            'email_verified_at' => $user->email_verified_at
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log important status changes
        if ($user->isDirty('registration_status') || $user->isDirty('is_verified')) {
            Log::info('User status updated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'registration_status_old' => $user->getOriginal('registration_status'),
                'registration_status_new' => $user->registration_status,
                'is_verified_old' => $user->getOriginal('is_verified'),
                'is_verified_new' => $user->is_verified
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
