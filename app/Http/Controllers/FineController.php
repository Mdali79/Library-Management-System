<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\book_issue;
use App\Models\settings;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        $query = Fine::with(['bookIssue.book', 'student', 'user']);

        // Students/Teachers see only their own fines
        if (in_array($role, ['Student', 'Teacher'])) {
            $student = \App\Models\student::where('user_id', $user->id)->first();
            if ($student) {
                $query->where('student_id', $student->id);
            } else {
                $query->where('id', 0); // No results if no student record
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id') && in_array($role, ['Admin', 'Librarian'])) {
            $query->where('student_id', $request->student_id);
        }

        // Calculate statistics based on role
        if (in_array($role, ['Student', 'Teacher'])) {
            $student = \App\Models\student::where('user_id', $user->id)->first();
            $pendingFines = $student ? Fine::where('student_id', $student->id)->where('status', 'pending')->sum('amount') : 0;
            $totalFines = $student ? Fine::where('student_id', $student->id)->sum('amount') : 0;
            $paidFines = $student ? Fine::where('student_id', $student->id)->where('status', 'paid')->sum('amount') : 0;
        } else {
            $pendingFines = Fine::where('status', 'pending')->sum('amount');
            $totalFines = Fine::sum('amount');
            $paidFines = Fine::where('status', 'paid')->sum('amount');
        }

        return view('fine.index', [
            'fines' => $query->latest()->paginate(15),
            'pendingFines' => $pendingFines,
            'totalFines' => $totalFines,
            'paidFines' => $paidFines,
            'role' => $role,
        ]);
    }

    /**
     * Show pending fines for a specific student
     */
    public function pending($studentId)
    {
        $fines = Fine::where('student_id', $studentId)
            ->where('status', 'pending')
            ->with(['bookIssue.book'])
            ->get();

        return view('fine.pending', [
            'fines' => $fines,
            'totalAmount' => $fines->sum('amount'),
        ]);
    }

    /**
     * Show fine history
     */
    public function history(Request $request)
    {
        $query = Fine::with(['bookIssue.book', 'student', 'user']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return view('fine.history', [
            'fines' => $query->latest()->paginate(20),
        ]);
    }

    /**
     * Process fine payment
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,online',
            'amount' => 'required|numeric|min:0',
        ]);

        $fine = Fine::findOrFail($id);
        
        if ($fine->status == 'paid') {
            return redirect()->back()->withErrors(['error' => 'Fine already paid.']);
        }

        $fine->status = 'paid';
        $fine->payment_method = $request->payment_method;
        $fine->paid_at = Carbon::now();
        $fine->notes = $request->notes ?? $fine->notes;
        $fine->save();

        return redirect()->back()->with('success', 'Fine paid successfully.');
    }

    /**
     * Calculate and create fines for overdue books
     */
    public function calculateOverdueFines()
    {
        $settings = settings::latest()->first();
        $overdueIssues = book_issue::where('issue_status', 'N')
            ->where('return_date', '<', Carbon::now())
            ->where('is_overdue', false)
            ->get();

        $created = 0;
        foreach ($overdueIssues as $issue) {
            $returnDate = Carbon::parse($issue->return_date);
            $daysOverdue = Carbon::now()->diffInDays($returnDate);
            
            if ($daysOverdue > $settings->fine_grace_period_days) {
                $chargeableDays = $daysOverdue - $settings->fine_grace_period_days;
                $fineAmount = $settings->fine_per_day * $chargeableDays;

                // Check if fine already exists
                $existingFine = Fine::where('book_issue_id', $issue->id)->first();
                
                if (!$existingFine) {
                    Fine::create([
                        'book_issue_id' => $issue->id,
                        'student_id' => $issue->student_id,
                        'user_id' => $issue->student->user_id ?? null,
                        'amount' => $fineAmount,
                        'days_overdue' => $daysOverdue,
                        'status' => 'pending',
                        'notes' => 'Auto-calculated fine for overdue book',
                    ]);
                    $created++;
                }

                $issue->is_overdue = true;
                $issue->fine_amount = $fineAmount;
                $issue->save();
            }
        }

        return redirect()->back()->with('success', "Calculated fines for {$created} overdue books.");
    }

    /**
     * Waive a fine
     */
    public function waive($id)
    {
        $fine = Fine::findOrFail($id);
        $fine->status = 'waived';
        $fine->notes = ($fine->notes ?? '') . ' | Waived by admin';
        $fine->save();

        return redirect()->back()->with('success', 'Fine waived successfully.');
    }
}
