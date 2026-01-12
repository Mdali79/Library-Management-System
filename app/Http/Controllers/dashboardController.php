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

        // Common metrics - filtered by role
        if ($role === 'Student') {
            $studentRecord = student::where('user_id', $user->id)->first();
            $totalBooks = book::count(); // Students can see total books
            $totalMembers = 0; // Students don't see member count

            // Calculate issued books: books that are currently issued (not returned)
            // issue_status = 'N' means not returned, request_status = 'issued' means approved and issued
            $issuedBooksCount = 0;
            if ($studentRecord) {
                $issuedBooksCount = book_issue::where('student_id', $studentRecord->id)
                    ->where(function ($query) {
                        $query->where('issue_status', 'N')
                            ->orWhereNull('issue_status');
                    })
                    ->where(function ($query) {
                        $query->where('request_status', 'issued')
                            ->orWhere('request_status', 'approved');
                    })
                    ->count();
            }

            // Calculate returned books: books that have been returned
            // issue_status = 'Y' means returned, or return_day is not null
            $returnedBooksCount = 0;
            if ($studentRecord) {
                $returnedBooksCount = book_issue::where('student_id', $studentRecord->id)
                    ->where(function ($query) {
                        $query->where('issue_status', 'Y')
                            ->orWhereNotNull('return_day');
                    })
                    ->count();
            }

            $pendingFinesCount = $studentRecord ? Fine::where('student_id', $studentRecord->id)
                ->where('status', 'pending')
                ->count() : 0;
            $pendingFinesAmount = $studentRecord ? Fine::where('student_id', $studentRecord->id)
                ->where('status', 'pending')
                ->sum('amount') : 0;
        } else {
            $totalBooks = book::count();
            $totalMembers = student::count();
            $issuedBooksCount = book_issue::where(function ($query) {
                $query->where('issue_status', 'N')
                    ->orWhereNull('issue_status');
            })
                ->where(function ($query) {
                    $query->where('request_status', 'issued')
                        ->orWhere('request_status', 'approved');
                })
                ->count();
            $returnedBooksCount = book_issue::where(function ($query) {
                $query->where('issue_status', 'Y')
                    ->orWhereNotNull('return_day');
            })->count();
            $pendingFinesCount = Fine::where('status', 'pending')->count();
            $pendingFinesAmount = Fine::where('status', 'pending')->sum('amount');
        }

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

        // Student specific data
        if ($role === 'Student') {
            $studentRecord = student::where('user_id', $user->id)->first();

            if ($studentRecord) {
                $data['my_issued_books'] = book_issue::where('student_id', $studentRecord->id)
                    ->where('issue_status', 'N')
                    ->where('request_status', 'issued')
                    ->with('book')
                    ->get();

                $data['my_pending_requests'] = book_issue::where('student_id', $studentRecord->id)
                    ->where('request_status', 'pending')
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

        // Admin specific data
        if ($role === 'Admin') {
            $data['overdue_books'] = book_issue::where('issue_status', 'N')
                ->where('request_status', 'issued')
                ->where('return_date', '<', Carbon::now())
                ->with(['book', 'student'])
                ->get();

            $data['pending_reservations'] = BookReservation::where('status', 'pending')
                ->with(['book', 'student'])
                ->count();
        }

        // Admin specific - pending book requests
        if ($role === 'Admin') {
            $data['pending_book_requests'] = book_issue::where('request_status', 'pending')
                ->with(['book', 'student.user'])
                ->count();

            // Pending user registrations
            $data['pending_registrations'] = \App\Models\User::where('registration_status', 'pending')
                ->count();
        }

        // Get search suggestions for dashboard
        $searchSuggestions = [];
        $bookSuggestions = book::orderBy('id', 'desc')->limit(5)->pluck('name')->toArray();
        $authorSuggestions = auther::orderBy('id', 'desc')->limit(5)->pluck('name')->toArray();
        $categorySuggestions = category::orderBy('id', 'desc')->limit(5)->pluck('name')->toArray();
        $data['search_suggestions'] = array_merge($bookSuggestions, $authorSuggestions, $categorySuggestions);

        // Check if we should show welcome splash (only on login success)
        $data['show_welcome_splash'] = session()->has('show_welcome_splash') && session('show_welcome_splash');

        // Clear the session flag after checking (so it doesn't show on page reload)
        if ($data['show_welcome_splash']) {
            session()->forget('show_welcome_splash');
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
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[A-Za-z]{2,})(?=.*[0-9]{2,}).{8,}$/',
            ],
        ], [
            'password.regex' => 'Password must contain at least 2 letters, 2 numbers, and be 8+ characters long.',
        ]);

        // Additional validation
        $validator = \Validator::make($request->all(), []);
        $validator->after(function ($validator) use ($request) {
            $password = $request->input('password');
            $letters = preg_match_all('/[A-Za-z]/', $password);
            $numbers = preg_match_all('/[0-9]/', $password);
            $length = strlen($password);

            if ($length < 8) {
                $validator->errors()->add('password', 'Password must be at least 8 characters long.');
            } elseif ($letters < 2) {
                $validator->errors()->add('password', 'Password must contain at least 2 letters.');
            } elseif ($numbers < 2) {
                $validator->errors()->add('password', 'Password must contain at least 2 numbers.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

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
