<?php

namespace App\Http\Controllers;

use App\Models\book_issue;
use App\Http\Requests\Storebook_issueRequest;
use App\Http\Requests\Updatebook_issueRequest;
use App\Models\auther;
use App\Models\book;
use App\Models\settings;
use App\Models\student;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Students see only their own issues
        if ($role == 'Student') {
            $studentRecord = student::where('user_id', $user->id)->first();
            if ($studentRecord) {
                $books = book_issue::where('student_id', $studentRecord->id)
                    ->with(['book', 'student'])
                    ->latest()
                    ->paginate(10);
            } else {
                // Return empty paginated collection
                $books = book_issue::where('id', 0)->paginate(10);
            }
        } else {
            // Admin see all issues
            $books = book_issue::with(['book', 'student', 'approver'])
                ->latest()
                ->paginate(10);
        }

        return view('book.issueBooks', [
            'books' => $books,
            'role' => $role
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $role = $user->role;

        // If student/teacher, show self-service form
        if ($role === 'Student') {
            return $this->studentRequestForm();
        }

        // Admin can issue to any member
        return view('book.issueBook_add', [
            'students' => student::with('user')->latest()->get(),
            'books' => book::where(function ($query) {
                $query->where('available_quantity', '>', 0)
                    ->orWhereNull('available_quantity')
                    ->orWhere('status', 'Y');
            })->get(),
        ]);
    }

    /**
     * Student self-service request form
     */
    private function studentRequestForm()
    {
        $user = Auth::user();
        $student = student::where('user_id', $user->id)->first();

        if (!$student) {
            // Instead of redirecting, show an error message on the same page
            return view('book.student_request', [
                'student' => null,
                'books' => collect(),
                'error' => 'Student profile not found. Please contact administrator to complete your profile setup.'
            ]);
        }

        return view('book.student_request', [
            'student' => $student,
            'books' => book::with(['auther', 'authors', 'category', 'publisher'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $settings = settings::latest()->first();

        // If student/teacher making request
        if ($role === 'Student') {
            return $this->studentRequest($request);
        }

        // For Admin, validate with FormRequest
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'issue_date' => 'nullable|date',
        ]);

        // Admin direct issue (existing functionality)
        $student = student::find($validated['student_id']);
        $book = book::find($validated['book_id']);

        // Check borrowing limit
        $currentIssues = book_issue::where('student_id', $validated['student_id'])
            ->where('issue_status', 'N')
            ->where('request_status', '!=', 'rejected')
            ->count();

        $borrowingLimit = $student->borrowing_limit ?? $settings->max_borrowing_limit_student;

        if ($currentIssues >= $borrowingLimit) {
            return redirect()->back()->withErrors(['error' => 'Borrowing limit reached. Maximum ' . $borrowingLimit . ' books allowed.']);
        }

        // Check availability
        if ($book->available_quantity <= 0) {
            return redirect()->back()->withErrors(['error' => 'Book is not available.']);
        }

        $issue_date = $validated['issue_date'] ?? Carbon::now();
        $return_date = Carbon::parse($issue_date)->addDays($settings->return_days);

        $receiptNumber = 'ISSUE-' . strtoupper(Str::random(8));

        $bookIssue = book_issue::create([
            'student_id' => $validated['student_id'],
            'book_id' => $validated['book_id'],
            'issue_date' => $issue_date,
            'return_date' => $return_date,
            'issue_status' => 'N',
            'request_status' => 'issued', // Direct issue by admin/librarian
            'approved_by' => $user->id,
            'approved_at' => Carbon::now(),
            'issue_receipt_number' => $receiptNumber,
            'is_overdue' => false,
            'fine_notified' => false,
        ]);

        // Update book quantities
        $book->available_quantity = max(0, $book->available_quantity - 1);
        $book->issued_quantity = $book->issued_quantity + 1;
        $book->status = $book->available_quantity > 0 ? 'Y' : 'N';
        $book->save();

        return redirect()->route('book_issued')->with('success', 'Book issued successfully. Receipt: ' . $receiptNumber);
    }

    /**
     * Student book request
     */
    private function studentRequest(Request $request)
    {
        $user = Auth::user();
        $student = student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->back()->withErrors(['error' => 'Student profile not found.']);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $settings = settings::latest()->first();
        $book = book::find($request->book_id);

        // Check borrowing limit (including pending requests)
        $currentIssues = book_issue::where('student_id', $student->id)
            ->where('issue_status', 'N')
            ->whereIn('request_status', ['pending', 'approved', 'issued'])
            ->count();

        $borrowingLimit = $student->borrowing_limit ?? $settings->max_borrowing_limit_student;

        if ($currentIssues >= $borrowingLimit) {
            return redirect()->back()->withErrors(['error' => 'Borrowing limit reached. Maximum ' . $borrowingLimit . ' books allowed.']);
        }

        // Check if already requested
        $existingRequest = book_issue::where('student_id', $student->id)
            ->where('book_id', $request->book_id)
            ->whereIn('request_status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            return redirect()->back()->withErrors(['error' => 'You already have a pending or approved request for this book.']);
        }

        $issue_date = Carbon::now();
        $return_date = Carbon::parse($issue_date)->addDays($settings->return_days);

        $bookIssue = book_issue::create([
            'student_id' => $student->id,
            'book_id' => $request->book_id,
            'issue_date' => $issue_date,
            'return_date' => $return_date,
            'issue_status' => 'N',
            'request_status' => 'pending', // Pending approval
            'is_overdue' => false,
            'fine_notified' => false,
        ]);

        return redirect()->route('book_issued')->with('success', 'Book request submitted successfully. Waiting for librarian approval.');
    }

    /**
     * Show pending requests for Admin approval
     */
    public function pendingRequests()
    {
        $user = Auth::user();

        if ($user->role !== 'Admin') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Access denied. Only Admins can approve requests.']);
        }

        $pendingRequests = book_issue::where('request_status', 'pending')
            ->with(['book', 'student.user'])
            ->latest()
            ->paginate(15);

        return view('book.pending_requests', [
            'pendingRequests' => $pendingRequests,
        ]);
    }

    /**
     * Approve book request (Admin)
     */
    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'Access denied. Only Admins can approve requests.']);
        }

        $bookIssue = book_issue::with(['book', 'student'])->findOrFail($id);

        if ($bookIssue->request_status != 'pending') {
            return redirect()->back()->withErrors(['error' => 'This request is not pending approval.']);
        }

        $settings = settings::latest()->first();
        $book = $bookIssue->book;
        $student = $bookIssue->student;

        // Check availability
        if ($book->available_quantity <= 0) {
            return redirect()->back()->withErrors(['error' => 'Book is not available. Cannot approve request.']);
        }

        // Check borrowing limit
        $currentIssues = book_issue::where('student_id', $student->id)
            ->where('issue_status', 'N')
            ->whereIn('request_status', ['approved', 'issued'])
            ->count();

        $borrowingLimit = $student->borrowing_limit ?? $settings->max_borrowing_limit_student;

        if ($currentIssues >= $borrowingLimit) {
            return redirect()->back()->withErrors(['error' => 'Student has reached borrowing limit. Cannot approve request.']);
        }

        // Approve and issue
        $receiptNumber = 'ISSUE-' . strtoupper(Str::random(8));

        $bookIssue->request_status = 'issued';
        $bookIssue->issue_status = 'N';
        $bookIssue->approved_by = $user->id;
        $bookIssue->approved_at = Carbon::now();
        $bookIssue->issue_receipt_number = $receiptNumber;
        $bookIssue->save();

        // Update book quantities
        $book->available_quantity = max(0, $book->available_quantity - 1);
        $book->issued_quantity = $book->issued_quantity + 1;
        $book->status = $book->available_quantity > 0 ? 'Y' : 'N';
        $book->save();

        return redirect()->route('book_issue.pending')->with('success', 'Book request approved and issued successfully. Receipt: ' . $receiptNumber);
    }

    /**
     * Reject book request (Admin)
     */
    public function rejectRequest(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'Access denied. Only Admins can reject requests.']);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $bookIssue = book_issue::findOrFail($id);

        if ($bookIssue->request_status != 'pending') {
            return redirect()->back()->withErrors(['error' => 'This request is not pending approval.']);
        }

        $bookIssue->request_status = 'rejected';
        $bookIssue->rejection_reason = $request->rejection_reason;
        $bookIssue->save();

        return redirect()->route('book_issue.pending')->with('success', 'Book request rejected successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bookIssue = book_issue::with(['book', 'student'])->findOrFail($id);
        $settings = settings::latest()->first();

        // Calculate fine if overdue
        $fine = 0;
        $daysOverdue = 0;
        $returnDate = Carbon::parse($bookIssue->return_date);
        $today = Carbon::now();

        if ($today->gt($returnDate)) {
            $daysOverdue = $today->diffInDays($returnDate);
            // Only charge fine if past grace period
            if ($daysOverdue > $settings->fine_grace_period_days) {
                $chargeableDays = $daysOverdue - $settings->fine_grace_period_days;
                $fine = $settings->fine_per_day * $chargeableDays;
            }
        }

        return view('book.issueBook_edit', [
            'bookIssue' => $bookIssue,
            'fine' => $fine,
            'daysOverdue' => $daysOverdue,
            'settings' => $settings,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updatebook_issueRequest  $request
     * @param  \App\Models\book_issue  $book_issue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bookIssue = book_issue::findOrFail($id);
        $settings = settings::latest()->first();
        $returnDate = Carbon::parse($bookIssue->return_date);
        $today = Carbon::now();

        // Calculate fine
        $fine = 0;
        $daysOverdue = 0;
        if ($today->gt($returnDate)) {
            $daysOverdue = $today->diffInDays($returnDate);
            if ($daysOverdue > $settings->fine_grace_period_days) {
                $chargeableDays = $daysOverdue - $settings->fine_grace_period_days;
                $fine = $settings->fine_per_day * $chargeableDays;
            }
        }

        // Generate return receipt number
        $returnReceiptNumber = 'RETURN-' . strtoupper(Str::random(8));

        // Update book issue
        $bookIssue->issue_status = 'Y';
        $bookIssue->return_day = $today;
        $bookIssue->fine_amount = $fine;
        $bookIssue->book_condition = $request->book_condition ?? 'good';
        $bookIssue->damage_notes = $request->damage_notes;
        $bookIssue->return_receipt_number = $returnReceiptNumber;
        $bookIssue->is_overdue = $daysOverdue > 0;
        $bookIssue->save();

        // Update book quantities
        $book = book::find($bookIssue->book_id);
        $book->available_quantity = $book->available_quantity + 1;
        $book->issued_quantity = max(0, $book->issued_quantity - 1);
        $book->status = 'Y';
        $book->save();

        // Create fine record if applicable
        if ($fine > 0) {
            Fine::create([
                'book_issue_id' => $bookIssue->id,
                'student_id' => $bookIssue->student_id,
                'user_id' => $bookIssue->student->user_id ?? null,
                'amount' => $fine,
                'days_overdue' => $daysOverdue,
                'status' => 'pending',
                'notes' => 'Auto-calculated fine for overdue return',
            ]);
        }

        return redirect()->route('book_issued')->with('success', 'Book returned successfully. Receipt: ' . $returnReceiptNumber . ($fine > 0 ? '. Fine: ' . number_format($fine, 2) . ' tk' : ''));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\book_issue  $book_issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $bookIssue = book_issue::findOrFail($id);

        // Students can only cancel their own pending requests
        if ($user->role === 'Student') {
            $student = student::where('user_id', $user->id)->first();
            if ($bookIssue->student_id != $student->id || $bookIssue->request_status != 'pending') {
                return redirect()->back()->withErrors(['error' => 'You can only cancel your own pending requests.']);
            }
            $bookIssue->delete();
            return redirect()->route('book_issued')->with('success', 'Request cancelled successfully.');
        }

        // Admin can delete any
        book_issue::find($id)->delete();
        return redirect()->route('book_issued');
    }
}
