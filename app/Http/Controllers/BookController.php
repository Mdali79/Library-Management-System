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
        $query = book::with(['auther', 'category', 'publisher']);

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
                  ->orWhereHas('publisher', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('author')) {
            $query->where('auther_id', $request->author);
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

        return view('book.index', [
            'books' => $query->latest()->paginate(10),
            'authors' => auther::latest()->get(),
            'publishers' => publisher::latest()->get(),
            'categories' => category::latest()->get(),
            'filters' => $request->all(),
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
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/book_covers', $imageName);
            $data['cover_image'] = 'book_covers/' . $imageName;
        }

        // Set default quantities if not provided
        $data['total_quantity'] = $data['total_quantity'] ?? 1;
        $data['available_quantity'] = $data['total_quantity'];
        $data['issued_quantity'] = 0;
        $data['status'] = 'Y';

        book::create($data);
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

        // Update quantities - ensure available_quantity is not less than issued_quantity
        if (isset($data['total_quantity'])) {
            $data['available_quantity'] = max(0, $data['total_quantity'] - $book->issued_quantity);
        }

        $book->update($data);
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
