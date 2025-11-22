<?php

namespace App\Http\Controllers;

use App\Http\Requests\changePasswordRequest;
use App\Models\auther;
use App\Models\book;
use App\Models\book_issue;
use App\Models\category;
use App\Models\publisher;
use App\Models\student;
use App\Models\Fine;
use App\Models\BookReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class dashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role ?? 'Admin';

        // Common metrics
        $totalBooks = book::count();
        $totalMembers = student::count();
        $issuedBooksCount = book_issue::where('issue_status', 'N')->count();
        $returnedBooksCount = book_issue::where('issue_status', 'Y')->count();
        $pendingFinesCount = Fine::where('status', 'pending')->count();
        $pendingFinesAmount = Fine::where('status', 'pending')->sum('amount');

        // Monthly activity data for chart (last 12 months)
        $monthlyActivity = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthlyActivity[] = [
                'month' => $month->format('M Y'),
                'issued' => book_issue::whereBetween('issue_date', [$monthStart, $monthEnd])->count(),
                'returned' => book_issue::whereBetween('return_day', [$monthStart, $monthEnd])->whereNotNull('return_day')->count(),
            ];
        }

        // Role-based data
        $data = [
            'authors' => auther::count(),
            'publishers' => publisher::count(),
            'categories' => category::count(),
            'books' => $totalBooks,
            'students' => $totalMembers,
            'issued_books' => $issuedBooksCount,
            'returned_books' => $returnedBooksCount,
            'pending_fines_count' => $pendingFinesCount,
            'pending_fines_amount' => $pendingFinesAmount,
            'monthly_activity' => $monthlyActivity,
            'role' => $role,
        ];

        // Student/Teacher/Librarian specific data
        if (in_array($role, ['Student', 'Teacher', 'Librarian'])) {
            $studentRecord = student::where('user_id', $user->id)->first();
            
            if ($studentRecord) {
                $data['my_issued_books'] = book_issue::where('student_id', $studentRecord->id)
                    ->where('issue_status', 'N')
                    ->with('book')
                    ->get();
                
                $data['my_pending_fines'] = Fine::where('student_id', $studentRecord->id)
                    ->where('status', 'pending')
                    ->with('bookIssue.book')
                    ->get();
                
                $data['my_reservations'] = BookReservation::where('student_id', $studentRecord->id)
                    ->whereIn('status', ['pending', 'available'])
                    ->with('book')
                    ->get();
                
                $data['my_borrowing_limit'] = $studentRecord->borrowing_limit ?? 5;
                $data['my_current_borrowed'] = count($data['my_issued_books']);
            }
        }

        // Admin/Librarian specific data
        if (in_array($role, ['Admin', 'Librarian'])) {
            $data['overdue_books'] = book_issue::where('issue_status', 'N')
                ->where('return_date', '<', Carbon::now())
                ->with(['book', 'student'])
                ->get();
            
            $data['pending_reservations'] = BookReservation::where('status', 'pending')
                ->with(['book', 'student'])
                ->count();
        }

        return view('dashboard', $data);
    }

    public function change_password_view()
    {
        return view('reset_password');
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'c_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        if (password_verify($request->c_password, $user->password)) {
            $user->password = bcrypt($request->password);
            $user->save();
            return redirect()->route("dashboard")->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->withErrors(['c_password' => 'Old password is incorrect']);
        }
    }
}
