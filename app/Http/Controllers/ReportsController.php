<?php

namespace App\Http\Controllers;

use App\Models\book;
use App\Models\book_issue;
use App\Models\student;
use App\Models\Fine;
use App\Models\category;
use App\Services\FineCalculator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('report.index');
    }

    public function date_wise()
    {
        return view('report.dateWise', ['books' => '']);
    }

    public function generate_date_wise_report(Request $request)
    {
        $request->validate(['date' => "required|date"]);
        return view('report.dateWise', [
            'books' => book_issue::where('issue_date', $request->date)->latest()->get()
        ]);
    }

    public function month_wise()
    {
        return view('report.monthWise', ['books' => '']);
    }

    public function generate_month_wise_report(Request $request)
    {
        $request->validate(['month' => "required|date"]);
        return view('report.monthWise', [
            'books' => book_issue::where('issue_date', 'LIKE', '%' . $request->month . '%')->latest()->get(),
        ]);
    }

    public function not_returned()
    {
        return view('report.notReturned', [
            'books' => book_issue::where('issue_status', 'N')
                ->with(['book', 'student'])
                ->latest()
                ->get()
        ]);
    }

    /**
     * Book Report
     */
    public function bookReport(Request $request)
    {
        $query = book::with(['auther', 'category', 'publisher']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'available') {
                $query->where('available_quantity', '>', 0);
            } elseif ($request->status == 'unavailable') {
                $query->where('available_quantity', '=', 0);
            }
        }

        return view('report.bookReport', [
            'books' => $query->latest()->get(),
            'categories' => category::all(),
        ]);
    }

    /**
     * Member Report
     */
    public function memberReport(Request $request)
    {
        $query = student::with('user');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department')) {
            $query->where('department', 'like', "%{$request->department}%");
        }

        return view('report.memberReport', [
            'members' => $query->latest()->get(),
        ]);
    }

    /**
     * Return Report
     */
    public function returnReport(Request $request)
    {
        $query = book_issue::where('issue_status', 'Y')
            ->with(['book', 'student']);

        if ($request->filled('date_from')) {
            $query->whereDate('return_day', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_day', '<=', $request->date_to);
        }

        return view('report.returnReport', [
            'returns' => $query->latest()->get(),
        ]);
    }

    /**
     * Overdue Books Report
     */
    public function overdueReport()
    {
        $overdueBooks = book_issue::where('issue_status', 'N')
            ->where('return_date', '<', Carbon::now())
            ->with(['book', 'student'])
            ->get()
            ->map(function ($issue) {
                $result = FineCalculator::calculate($issue->return_date, null, null);
                $issue->days_overdue = $result['days_overdue'];
                return $issue;
            });

        return view('report.overdueReport', [
            'overdueBooks' => $overdueBooks,
        ]);
    }

    /**
     * Fine Collection Report
     */
    public function fineCollectionReport(Request $request)
    {
        $query = Fine::with(['bookIssue.book', 'student', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $fines = $query->latest()->get();

        return view('report.fineCollectionReport', [
            'fines' => $fines,
            'totalAmount' => $fines->sum('amount'),
            'paidAmount' => $fines->where('status', 'paid')->sum('amount'),
            'pendingAmount' => $fines->where('status', 'pending')->sum('amount'),
        ]);
    }

    /**
     * Category Wise Statistics
     */
    public function categoryStatistics()
    {
        $categories = category::withCount(['books as total_books'])
            ->with(['books' => function ($query) {
                $query->select('category_id', 'available_quantity', 'issued_quantity');
            }])
            ->get()
            ->map(function ($category) {
                $category->available_books = $category->books->sum('available_quantity');
                $category->issued_books = $category->books->sum('issued_quantity');
                return $category;
            });

        return view('report.categoryStatistics', [
            'categories' => $categories,
        ]);
    }

    /**
     * Export to PDF/Excel
     */
    public function export(Request $request)
    {
        $type = $request->type; // 'book', 'member', 'return', 'overdue', 'fine', 'category'
        $format = $request->format; // 'pdf', 'excel'

        // This would typically use libraries like dompdf or maatwebsite/excel
        // For now, we'll return a view that can be converted to PDF

        switch ($type) {
            case 'book':
                $data = book::with(['auther', 'category', 'publisher'])->get();
                return view('report.exports.bookExport', ['books' => $data]);

            case 'member':
                $data = student::with('user')->get();
                return view('report.exports.memberExport', ['members' => $data]);

            case 'return':
                $data = book_issue::where('issue_status', 'Y')->with(['book', 'student'])->get();
                return view('report.exports.returnExport', ['returns' => $data]);

            case 'overdue':
                $data = book_issue::where('issue_status', 'N')
                    ->where('return_date', '<', Carbon::now())
                    ->with(['book', 'student'])
                    ->get();
                return view('report.exports.overdueExport', ['overdueBooks' => $data]);

            case 'fine':
                $data = Fine::with(['bookIssue.book', 'student'])->get();
                return view('report.exports.fineExport', ['fines' => $data]);

            case 'category':
                $data = category::with('books')->get();
                return view('report.exports.categoryExport', ['categories' => $data]);

            default:
                return redirect()->back()->withErrors(['error' => 'Invalid export type']);
        }
    }
}
