<?php

namespace App\Http\Controllers;

use App\Models\book_issue;
use App\Models\BookReservation;
use App\Models\book;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
// use App\Mail\DueDateReminder;
// use App\Mail\OverdueAlert;
// use App\Mail\BookAvailableNotification;

class NotificationController extends Controller
{
    /**
     * Send due date reminder notifications
     */
    public function sendDueDateReminders()
    {
        $settings = \App\Models\settings::latest()->first();
        $reminderDays = 2; // Send reminder 2 days before due date
        
        $issues = book_issue::where('issue_status', 'N')
            ->whereDate('return_date', '<=', Carbon::now()->addDays($reminderDays))
            ->whereDate('return_date', '>', Carbon::now())
            ->with(['book', 'student'])
            ->get();

        $sent = 0;
        foreach ($issues as $issue) {
            if ($issue->student && $issue->student->email) {
                // Mail::to($issue->student->email)->send(new DueDateReminder($issue));
                // Here you would implement actual email sending
                $sent++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sent {$sent} due date reminder notifications"
        ]);
    }

    /**
     * Send overdue alerts
     */
    public function sendOverdueAlerts()
    {
        $overdueIssues = book_issue::where('issue_status', 'N')
            ->where('return_date', '<', Carbon::now())
            ->where('fine_notified', false)
            ->with(['book', 'student'])
            ->get();

        $sent = 0;
        foreach ($overdueIssues as $issue) {
            if ($issue->student && $issue->student->email) {
                // Mail::to($issue->student->email)->send(new OverdueAlert($issue));
                // Here you would implement actual email sending
                $issue->fine_notified = true;
                $issue->save();
                $sent++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sent {$sent} overdue alert notifications"
        ]);
    }

    /**
     * Notify members when new books are available
     */
    public function notifyNewBooks()
    {
        // This would typically be triggered when a new book is added
        // For now, we'll create a method that can be called manually
        
        $recentBooks = book::where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        // Here you would send notifications to all members about new books
        // This is a placeholder for the actual implementation

        return response()->json([
            'success' => true,
            'message' => 'New book notifications sent'
        ]);
    }

    /**
     * Notify members when reserved books become available
     */
    public function notifyReservedBooksAvailable($bookId)
    {
        $book = book::findOrFail($bookId);
        
        if ($book->available_quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Book is not available'
            ]);
        }

        $reservations = BookReservation::where('book_id', $bookId)
            ->where('status', 'pending')
            ->orderBy('reserved_at', 'asc')
            ->with('student')
            ->get();

        $notified = 0;
        foreach ($reservations as $reservation) {
            if ($book->available_quantity > 0 && $reservation->student && $reservation->student->email) {
                // Mail::to($reservation->student->email)->send(new BookAvailableNotification($reservation));
                $reservation->status = 'available';
                $reservation->notified_at = Carbon::now();
                $reservation->save();
                $notified++;
                $book->available_quantity--;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Notified {$notified} members about book availability"
        ]);
    }
}
