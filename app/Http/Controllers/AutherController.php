<?php

namespace App\Http\Controllers;

use App\Models\auther;
use App\Http\Requests\StoreautherRequest;
use App\Http\Requests\UpdateautherRequest;
use Illuminate\Http\Request;

class AutherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = auther::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $suggestions = [];
        if ($request->filled('search')) {
            $suggestions = auther::where('name', 'like', "%{$request->search}%")
                ->limit(10)
                ->pluck('name')
                ->toArray();
        } else {
            $suggestions = auther::orderBy('id', 'desc')
                ->limit(10)
                ->pluck('name')
                ->toArray();
        }

        return view('auther.index', [
            'authors' => $query->latest()->paginate(5),
            'filters' => $request->all(),
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('auther.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreautherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreautherRequest $request)
    {
        auther::create($request->validated());

        return redirect()->route('authors');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\auther  $auther
     * @return \Illuminate\Http\Response
     */
    public function edit(auther $auther)
    {
        return view('auther.edit', [
            'auther' => $auther
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateautherRequest  $request
     * @param  \App\Models\auther  $auther
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateautherRequest $request, $id)
    {
        $auther = auther::find($id);
        $auther->name = $request->name;
        $auther->save();

        return redirect()->route('authors');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auther::findorfail($id)->delete();
        return redirect()->route('authors');
    }
}
