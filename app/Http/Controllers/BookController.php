<?php

namespace App\Http\Controllers;

use App\Models\book;
use App\Http\Requests\StorebookRequest;
use App\Http\Requests\UpdatebookRequest;
use App\Models\auther;
use App\Models\category;
use App\Models\publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = book::with(['auther', 'authors', 'category', 'publisher']);

        // Advanced search filters
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
                  })
                  ->orWhereHas('publisher', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('author')) {
            $query->where(function($q) use ($request) {
                $q->where('auther_id', $request->author)
                  ->orWhereHas('authors', function($q2) use ($request) {
                      $q2->where('authers.id', $request->author);
                  });
            });
        }

        if ($request->filled('isbn')) {
            $query->where('isbn', 'like', "%{$request->isbn}%");
        }

        if ($request->filled('edition')) {
            $query->where('edition', 'like', "%{$request->edition}%");
        }

        if ($request->filled('publisher')) {
            $query->where('publisher_id', $request->publisher);
        }

        if ($request->filled('publication_year')) {
            $query->where('publication_year', $request->publication_year);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status == 'available') {
                $query->where('available_quantity', '>', 0);
            } elseif ($request->status == 'unavailable') {
                $query->where('available_quantity', '=', 0);
            }
        }

        // Get keyword suggestions for search
        $suggestions = [];
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Get book name suggestions
            $bookSuggestions = book::where('name', 'like', "%{$searchTerm}%")
                ->limit(5)
                ->pluck('name')
                ->toArray();
            // Get author name suggestions
            $authorSuggestions = auther::where('name', 'like', "%{$searchTerm}%")
                ->limit(5)
                ->pluck('name')
                ->toArray();
            // Get ISBN suggestions
            $isbnSuggestions = book::where('isbn', 'like', "%{$searchTerm}%")
                ->limit(5)
                ->pluck('isbn')
                ->filter()
                ->toArray();
            
            $suggestions = array_merge($bookSuggestions, $authorSuggestions, $isbnSuggestions);
            $suggestions = array_unique(array_slice($suggestions, 0, 10));
        } else {
            // Get popular suggestions
            $bookSuggestions = book::orderBy('id', 'desc')->limit(5)->pluck('name')->toArray();
            $authorSuggestions = auther::orderBy('id', 'desc')->limit(5)->pluck('name')->toArray();
            $suggestions = array_merge($bookSuggestions, $authorSuggestions);
        }
        
        return view('book.index', [
            'books' => $query->latest()->paginate(10),
            'authors' => auther::latest()->get(),
            'publishers' => publisher::latest()->get(),
            'categories' => category::latest()->get(),
            'filters' => $request->all(),
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('book.create',[
            'authors' => auther::latest()->get(),
            'publishers' => publisher::latest()->get(),
            'categories' => category::latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorebookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorebookRequest $request)
    {
        $data = $request->validated();
        
        // Extract authors data before creating book
        $authorsData = $data['authors'] ?? [];
        unset($data['authors']);
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/book_covers', $imageName);
            $data['cover_image'] = 'book_covers/' . $imageName;
        }

        // Handle PDF file upload
        if ($request->hasFile('pdf_file')) {
            $pdf = $request->file('pdf_file');
            // Validate PDF file type
            if ($pdf->getClientMimeType() !== 'application/pdf') {
                return redirect()->back()->withErrors(['pdf_file' => 'Only PDF files are allowed.'])->withInput();
            }
            // Validate file size (max 50MB)
            if ($pdf->getSize() > 50 * 1024 * 1024) {
                return redirect()->back()->withErrors(['pdf_file' => 'PDF file size must not exceed 50MB.'])->withInput();
            }
            $pdfName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $pdf->getClientOriginalName());
            $pdf->storeAs('public/book_pdfs', $pdfName);
            $data['pdf_file'] = 'book_pdfs/' . $pdfName;
        }

        // Set preview_pages default if not provided
        if (!isset($data['preview_pages']) || empty($data['preview_pages'])) {
            $data['preview_pages'] = 50; // Default to 50 pages
        }

        // Set default quantities if not provided
        $data['total_quantity'] = $data['total_quantity'] ?? 1;
        $data['available_quantity'] = $data['total_quantity'];
        $data['issued_quantity'] = 0;
        $data['status'] = 'Y';

        // Set auther_id for backward compatibility (use first author)
        $firstAuthor = $authorsData[0] ?? null;
        if ($firstAuthor && isset($firstAuthor['id'])) {
            $data['auther_id'] = $firstAuthor['id'];
        }

        $book = book::create($data);
        
        // Sync authors to pivot table (no roles needed)
        $authorIds = [];
        foreach ($authorsData as $author) {
            if (isset($author['id']) && !empty($author['id'])) {
                $authorIds[] = $author['id'];
            }
        }
        $book->authors()->sync($authorIds);
        
        return redirect()->route('books')->with('success', 'Book added successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(book $book)
    {
        // Load existing authors with their roles
        $book->load('authors');
        
        return view('book.edit',[
            'authors' => auther::latest()->get(),
            'publishers' => publisher::latest()->get(),
            'categories' => category::latest()->get(),
            'book' => $book
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatebookRequest  $request
     * @param  \App\Models\book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatebookRequest $request, $id)
    {
        $book = book::find($id);
        $data = $request->validated();
        
        // Extract authors data before updating book
        $authorsData = $data['authors'] ?? [];
        unset($data['authors']);
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image && Storage::exists('public/' . $book->cover_image)) {
                Storage::delete('public/' . $book->cover_image);
            }
            $image = $request->file('cover_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/book_covers', $imageName);
            $data['cover_image'] = 'book_covers/' . $imageName;
        }

        // Handle PDF file upload
        if ($request->hasFile('pdf_file')) {
            $pdf = $request->file('pdf_file');
            // Validate PDF file type
            if ($pdf->getClientMimeType() !== 'application/pdf') {
                return redirect()->back()->withErrors(['pdf_file' => 'Only PDF files are allowed.'])->withInput();
            }
            // Validate file size (max 50MB)
            if ($pdf->getSize() > 50 * 1024 * 1024) {
                return redirect()->back()->withErrors(['pdf_file' => 'PDF file size must not exceed 50MB.'])->withInput();
            }
            // Delete old PDF if exists
            if ($book->pdf_file && Storage::exists('public/' . $book->pdf_file)) {
                Storage::delete('public/' . $book->pdf_file);
            }
            $pdfName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $pdf->getClientOriginalName());
            $pdf->storeAs('public/book_pdfs', $pdfName);
            $data['pdf_file'] = 'book_pdfs/' . $pdfName;
        }

        // Set preview_pages default if not provided
        if (!isset($data['preview_pages']) || empty($data['preview_pages'])) {
            $data['preview_pages'] = $book->preview_pages ?? 50; // Keep existing or default to 50
        }

        // Update quantities - ensure available_quantity is not less than issued_quantity
        if (isset($data['total_quantity'])) {
            $data['available_quantity'] = max(0, $data['total_quantity'] - $book->issued_quantity);
        }

        // Set auther_id for backward compatibility (use first author)
        $firstAuthor = $authorsData[0] ?? null;
        if ($firstAuthor && isset($firstAuthor['id'])) {
            $data['auther_id'] = $firstAuthor['id'];
        }

        $book->update($data);
        
        // Sync authors to pivot table (no roles needed)
        $authorIds = [];
        foreach ($authorsData as $author) {
            if (isset($author['id']) && !empty($author['id'])) {
                $authorIds[] = $author['id'];
            }
        }
        $book->authors()->sync($authorIds);
        
        return redirect()->route('books')->with('success', 'Book updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        book::find($id)->delete();
        return redirect()->route('books');
    }
}
