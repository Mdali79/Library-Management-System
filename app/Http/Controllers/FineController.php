<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\book_issue;
use App\Models\settings;
use App\Models\student;
use App\Services\FineCalculator;
use App\Services\SslCommerzService;
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

        // Students see only their own fines
        if ($role === 'Student') {
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

        if ($request->filled('student_id') && $role === 'Admin') {
            $query->where('student_id', $request->student_id);
        }

        // Calculate statistics based on role
        if ($role === 'Student') {
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
     * Process fine payment (cash or Admin marking online). Students paying online use initiateSslPayment.
     */
    public function pay(Request $request, $id)
    {
        $user = auth()->user();
        $role = $user->role;

        $request->validate([
            'payment_method' => 'required|in:cash,online',
            'amount' => 'required|numeric|min:0',
        ]);

        $fine = Fine::findOrFail($id);

        if ($fine->status == 'paid') {
            return redirect()->back()->withErrors(['error' => 'Fine already paid.']);
        }

        if ($role === 'Student') {
            $student = student::where('user_id', $user->id)->first();
            if (!$student || $fine->student_id != $student->id) {
                return redirect()->back()->withErrors(['error' => 'You can only pay your own fines.']);
            }
            if ($request->payment_method === 'online') {
                return redirect()->back()->withErrors(['error' => 'Use "Pay with Card/Bank (SSL)" to pay online.']);
            }
        }

        $fine->status = 'paid';
        $fine->payment_method = $request->payment_method;
        $fine->paid_at = Carbon::now();
        $fine->notes = ($fine->notes ?? '') . ' | Paid by ' . ($role === 'Student' ? 'student' : $role);
        $fine->save();

        return redirect()->back()->with('success', 'Fine paid successfully.');
    }

    /**
     * Initiate SSLCommerz payment (student only). Redirects to gateway.
     */
    public function initiateSslPayment($id)
    {
        $user = auth()->user();
        if ($user->role !== 'Student') {
            return redirect()->route('fines.index')->withErrors(['error' => 'SSL payment is for students only.']);
        }

        $student = student::where('user_id', $user->id)->first();
        if (!$student) {
            return redirect()->route('fines.index')->withErrors(['error' => 'Student profile not found.']);
        }

        $fine = Fine::with('student.user')->findOrFail($id);
        if ($fine->student_id != $student->id) {
            return redirect()->route('fines.index')->withErrors(['error' => 'You can only pay your own fines.']);
        }
        if ($fine->status !== 'pending') {
            return redirect()->route('fines.index')->withErrors(['error' => 'Fine is not pending.']);
        }
        if ($fine->amount <= 0) {
            return redirect()->route('fines.index')->withErrors(['error' => 'Invalid fine amount.']);
        }

        $tranId = 'FINE-' . $fine->id . '-' . uniqid();

        $service = new SslCommerzService();
        $student = $fine->student;
        $address = trim($student->address ?? '');
        $result = $service->createSession([
            'tran_id' => $tranId,
            'total_amount' => (float) $fine->amount,
            'currency' => 'BDT',
            'success_url' => url()->route('fines.payment.success', ['fine_id' => $fine->id]),
            'fail_url' => url()->route('fines.payment.fail', ['fine_id' => $fine->id]),
            'cancel_url' => url()->route('fines.payment.cancel', ['fine_id' => $fine->id]),
            'cus_name' => $student->name ?? $user->name,
            'cus_email' => $student->user->email ?? $user->email ?? 'student@example.com',
            'cus_phone' => $student->phone ?? '00000000000',
            'cus_add1' => $address !== '' ? $address : 'Library Member',
            'cus_city' => 'N/A',
            'cus_country' => 'Bangladesh',
            'product_name' => 'Library Fine #' . $fine->id,
            'product_category' => 'Fine',
        ]);

        if (!$result['success']) {
            return redirect()->route('fines.index')->withErrors(['error' => $result['error'] ?? 'Could not start payment.']);
        }

        return redirect()->away($result['gateway_url']);
    }

    /**
     * SSLCommerz success callback. Validate and mark fine paid. No auth required (gateway redirect).
     */
    public function paymentSuccess(Request $request)
    {
        $fineId = $request->get('fine_id');
        $valId = $request->get('val_id') ?? $request->get('tran_id');

        if (!$fineId || !$valId) {
            return redirect()->route('fines.payment.result', ['status' => 'fail', 'message' => 'Invalid callback.']);
        }

        $fine = Fine::find($fineId);
        if (!$fine) {
            return redirect()->route('fines.payment.result', ['status' => 'fail', 'message' => 'Fine not found.']);
        }

        if ($fine->status === 'paid' && $fine->transaction_id === $valId) {
            return redirect()->route('fines.payment.result', ['status' => 'success', 'message' => 'Payment already recorded.']);
        }

        $service = new SslCommerzService();
        $validation = $service->validateTransaction($valId);

        if (!$validation['valid'] || ($validation['status'] ?? '') !== 'VALID') {
            return redirect()->route('fines.payment.result', ['status' => 'fail', 'message' => 'Payment validation failed or transaction was not successful.']);
        }

        $gatewayAmount = $validation['amount'] ?? null;
        if ($gatewayAmount !== null && abs((float) $gatewayAmount - (float) $fine->amount) > 0.01) {
            return redirect()->route('fines.payment.result', ['status' => 'fail', 'message' => 'Payment amount mismatch.']);
        }

        $fine->status = 'paid';
        $fine->payment_method = 'online';
        $fine->paid_at = Carbon::now();
        $fine->transaction_id = $valId;
        $fine->gateway_status = $validation['status'] ?? 'VALID';
        $fine->notes = ($fine->notes ?? '') . ' | Paid via SSL (Gateway)';
        $fine->save();

        // Sync related book_issue so due/fine amount display is updated
        $issue = $fine->bookIssue;
        if ($issue) {
            $issue->fine_amount = 0;
            $issue->save();
        }

        return redirect()->route('fines.payment.result', ['status' => 'success', 'message' => 'Fine paid successfully via bKash/SSL.']);
    }

    /**
     * SSLCommerz fail callback. No auth required (gateway redirect).
     */
    public function paymentFail(Request $request)
    {
        return redirect()->route('fines.payment.result', ['status' => 'fail', 'message' => 'Payment failed or was declined. Your fine remains pending.']);
    }

    /**
     * SSLCommerz cancel callback. No auth required (gateway redirect).
     */
    public function paymentCancel(Request $request)
    {
        return redirect()->route('fines.payment.result', ['status' => 'cancelled', 'message' => 'Payment was cancelled. Your fine remains pending.']);
    }

    /**
     * Payment result page (success / fail / cancelled). No auth required.
     */
    public function paymentResult(Request $request)
    {
        $status = $request->query('status', 'fail');
        $message = $request->query('message', '');
        $allowed = ['success', 'fail', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $status = 'fail';
        }
        return view('fine.payment_result', [
            'status' => $status,
            'message' => $message,
        ]);
    }

    /**
     * SSLCommerz IPN (Instant Payment Notification). Validate and mark fine paid.
     */
    public function paymentIpn(Request $request)
    {
        $valId = $request->input('val_id') ?? $request->input('tran_id');
        $fineId = $request->input('fine_id');

        if (!$valId) {
            return response()->json(['error' => 'Missing val_id'], 400);
        }

        $fine = $fineId ? Fine::find($fineId) : Fine::where('transaction_id', $valId)->first();
        if (!$fine) {
            return response()->json(['error' => 'Fine not found'], 404);
        }

        if ($fine->status === 'paid' && $fine->transaction_id === $valId) {
            return response()->json(['message' => 'Already processed']);
        }

        $service = new SslCommerzService();
        $validation = $service->validateTransaction($valId);

        if (!$validation['valid'] || ($validation['status'] ?? '') !== 'VALID') {
            return response()->json(['error' => 'Validation failed'], 400);
        }

        $gatewayAmount = $validation['amount'] ?? null;
        if ($gatewayAmount !== null && abs((float) $gatewayAmount - (float) $fine->amount) > 0.01) {
            return response()->json(['error' => 'Amount mismatch'], 400);
        }

        $fine->status = 'paid';
        $fine->payment_method = 'online';
        $fine->paid_at = Carbon::now();
        $fine->transaction_id = $valId;
        $fine->gateway_status = $validation['status'] ?? 'VALID';
        $fine->notes = ($fine->notes ?? '') . ' | Paid via SSL IPN';
        $fine->save();

        return response()->json(['message' => 'OK']);
    }

    /**
     * Calculate and create/update fines for overdue books (calendar-day logic).
     * Running again updates existing pending fines so amount grows with days.
     */
    public function calculateOverdueFines()
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'You do not have permission to calculate overdue fines.']);
        }

        $settings = settings::latest()->first();
        if (!$settings) {
            return redirect()->back()->withErrors(['error' => 'Library settings are not configured. Please set fine settings in Settings.']);
        }

        $overdueIssues = book_issue::with('student')
            ->where('issue_status', 'N')
            ->where('return_date', '<', Carbon::now())
            ->get();

        $created = 0;
        $updated = 0;
        foreach ($overdueIssues as $issue) {
            $result = FineCalculator::calculate($issue->return_date, null, $settings);
            $fineAmount = $result['amount'];
            $daysOverdue = $result['days_overdue'];

            if ($fineAmount <= 0) {
                $issue->is_overdue = $daysOverdue > 0;
                $issue->fine_amount = 0;
                $issue->save();
                continue;
            }

            $existingFine = Fine::where('book_issue_id', $issue->id)->first();

            if ($existingFine) {
                if ($existingFine->status === 'pending') {
                    $existingFine->amount = $fineAmount;
                    $existingFine->days_overdue = $daysOverdue;
                    $existingFine->save();
                    $updated++;
                }
            } else {
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

        $message = "Calculated fines for overdue books: {$created} new, {$updated} updated.";
        return redirect()->back()->with('success', $message);
    }

    /**
     * Waive a fine
     */
    public function waive($id)
    {
        $user = auth()->user();
        $role = $user->role;

        // Only Admin can waive fines
        if ($role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'You do not have permission to waive fines.']);
        }

        $fine = Fine::findOrFail($id);

        if ($fine->status == 'waived') {
            return redirect()->back()->withErrors(['error' => 'Fine already waived.']);
        }

        $fine->status = 'waived';
        $fine->notes = ($fine->notes ?? '') . ' | Waived by ' . $role . ' (' . $user->name . ') on ' . Carbon::now()->format('Y-m-d H:i:s');
        $fine->save();

        return redirect()->back()->with('success', 'Fine waived successfully.');
    }
}
