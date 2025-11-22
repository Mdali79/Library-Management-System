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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        return view('book.issueBooks', [
            'books' => book_issue::Paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('book.issueBook_add', [
            'students' => student::with('user')->latest()->get(),
            'books' => book::where(function($query) {
                $query->where('available_quantity', '>', 0)
                      ->orWhereNull('available_quantity')
                      ->orWhere('status', 'Y');
            })->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Storebook_issueRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storebook_issueRequest $request)
    {
        $settings = settings::latest()->first();
        $student = student::find($request->student_id);
        $book = book::find($request->book_id);

        // Check borrowing limit
        $currentIssues = book_issue::where('student_id', $request->student_id)
            ->where('issue_status', 'N')
            ->count();
        
        $borrowingLimit = $student->borrowing_limit ?? 
            ($student->role == 'Teacher' ? $settings->max_borrowing_limit_teacher : 
             ($student->role == 'Librarian' ? $settings->max_borrowing_limit_librarian : 
              $settings->max_borrowing_limit_student));

        if ($currentIssues >= $borrowingLimit) {
            return redirect()->back()->withErrors(['error' => 'Borrowing limit reached. Maximum ' . $borrowingLimit . ' books allowed.']);
        }

        // Check availability
        if ($book->available_quantity <= 0) {
            return redirect()->back()->withErrors(['error' => 'Book is not available.']);
        }

        $issue_date = $request->issue_date ?? Carbon::now();
        $return_date = Carbon::parse($issue_date)->addDays($settings->return_days);
        
        $receiptNumber = 'ISSUE-' . strtoupper(Str::random(8));

        $bookIssue = book_issue::create([
            'student_id' => $request->student_id,
            'book_id' => $request->book_id,
            'issue_date' => $issue_date,
            'return_date' => $return_date,
            'issue_status' => 'N',
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

        return redirect()->route('book_issued')->with('success', 'Book returned successfully. Receipt: ' . $returnReceiptNumber . ($fine > 0 ? '. Fine: $' . number_format($fine, 2) : ''));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\book_issue  $book_issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        book_issue::find($id)->delete();
        return redirect()->route('book_issued');
    }
}
