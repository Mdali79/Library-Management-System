<?php

namespace App\Http\Controllers;

use App\Models\book;
use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class BookReadingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get CSE category IDs
     */
    private function getCSECategoryIds()
    {
        $cseCategoryKeywords = [
            'Computer Science',
            'Computer Sciences',
            'CSE',
            'Programming',
            'Software Engineering',
            'Data Structures',
            'Algorithms',
            'Database',
            'Web Development',
            'Machine Learning',
            'Artificial Intelligence',
            'Programming Languages',
            'Data Structures & Algorithms',
            'Database Systems',
            'Computer Networks',
            'Operating Systems',
            'Mobile Development',
            'Cybersecurity',
            'Cloud Computing',
            'Computer Architecture',
            'Software Testing',
            'Project Management'
        ];

        $cseCategories = category::where(function ($q) use ($cseCategoryKeywords) {
            foreach ($cseCategoryKeywords as $keyword) {
                $q->orWhere('name', 'like', "%{$keyword}%");
            }
        })->pluck('id');

        if ($cseCategories->isEmpty()) {
            // Fallback: get all categories if no CSE categories found
            $cseCategories = category::pluck('id');
        }

        return $cseCategories;
    }

    /**
     * Display a listing of CSE books available for reading
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Only allow students to access
        if (Auth::user()->role !== 'Student') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Online reading is only available for students.']);
        }

        $cseCategoryIds = $this->getCSECategoryIds();

        $query = book::with(['auther', 'authors', 'category', 'publisher'])
            ->whereIn('category_id', $cseCategoryIds)
            ->whereNotNull('pdf_file')
            ->where('status', 'Y');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('auther', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('authors', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $books = $query->latest()->paginate(12);

        return view('reading.index', compact('books'));
    }

    /**
     * Display the reading interface for a specific book
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Only allow students to access
        if (Auth::user()->role !== 'Student') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Online reading is only available for students.']);
        }

        $book = book::with(['auther', 'authors', 'category', 'publisher'])->findOrFail($id);

        // Check if book is CSE book
        if (!$book->isCSEBook()) {
            return redirect()->route('reading.index')->withErrors(['error' => 'This book is not available for online reading.']);
        }

        // Check if PDF file exists
        if (!$book->hasPdfFile()) {
            return redirect()->route('reading.index')->withErrors(['error' => 'PDF file not available for this book.']);
        }

        $previewPages = $book->preview_pages ?? 50;

        return view('reading.show', compact('book', 'previewPages'));
    }

    /**
     * Serve PDF file for reading (with access control)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPdfPreview($id)
    {
        // Only allow students to access
        if (Auth::user()->role !== 'Student') {
            abort(403, 'Unauthorized access');
        }

        $book = book::findOrFail($id);

        // Check if book is CSE book
        if (!$book->isCSEBook()) {
            abort(403, 'This book is not available for online reading.');
        }

        // Check if PDF file exists
        if (!$book->hasPdfFile()) {
            abort(404, 'PDF file not found.');
        }

        $filePath = storage_path('app/public/' . $book->pdf_file);

        if (!file_exists($filePath)) {
            abort(404, 'PDF file not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($book->pdf_file) . '"',
            'Cache-Control' => 'public, max-age=3600',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
