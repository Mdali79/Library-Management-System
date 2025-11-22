<?php

namespace App\Http\Controllers;

use App\Models\BookReservation;
use App\Models\book;
use App\Models\student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BookReservation::with(['book', 'student', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        return view('reservation.index', [
            'reservations' => $query->latest()->paginate(15),
        ]);
    }

    /**
     * Reserve a book online
     */
    public function reserve(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $book = book::findOrFail($request->book_id);
        
        // Check if book is available
        if ($book->available_quantity > 0) {
            return redirect()->back()->withErrors(['error' => 'Book is currently available. Please issue it directly.']);
        }

        // Check if already reserved by this student
        $existingReservation = BookReservation::where('book_id', $request->book_id)
            ->where('student_id', $request->student_id)
            ->whereIn('status', ['pending', 'available'])
            ->first();

        if ($existingReservation) {
            return redirect()->back()->withErrors(['error' => 'You already have a pending reservation for this book.']);
        }

        $reservation = BookReservation::create([
            'book_id' => $request->book_id,
            'student_id' => $request->student_id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'reserved_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addDays(7), // Reservation expires in 7 days
        ]);

        return redirect()->back()->with('success', 'Book reserved successfully. You will be notified when it becomes available.');
    }

    /**
     * Cancel a reservation
     */
    public function cancel($id)
    {
        $reservation = BookReservation::findOrFail($id);
        
        if ($reservation->status == 'issued') {
            return redirect()->back()->withErrors(['error' => 'Cannot cancel an issued reservation.']);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        return redirect()->back()->with('success', 'Reservation cancelled successfully.');
    }

    /**
     * Notify members when book becomes available
     */
    public function notifyAvailable($bookId)
    {
        $book = book::findOrFail($bookId);
        
        if ($book->available_quantity <= 0) {
            return redirect()->back()->withErrors(['error' => 'Book is not available yet.']);
        }

        $pendingReservations = BookReservation::where('book_id', $bookId)
            ->where('status', 'pending')
            ->orderBy('reserved_at', 'asc')
            ->get();

        $notified = 0;
        foreach ($pendingReservations as $reservation) {
            if ($book->available_quantity > 0) {
                $reservation->status = 'available';
                $reservation->notified_at = Carbon::now();
                $reservation->save();
                $notified++;
                
                // Here you would send email/SMS notification
                // Mail::to($reservation->student->email)->send(new BookAvailableNotification($reservation));
            }
        }

        return redirect()->back()->with('success', "Notified {$notified} members about book availability.");
    }

    /**
     * Process reservation when book is issued
     */
    public function markAsIssued($id)
    {
        $reservation = BookReservation::findOrFail($id);
        $reservation->status = 'issued';
        $reservation->save();

        return redirect()->back()->with('success', 'Reservation marked as issued.');
    }
}
