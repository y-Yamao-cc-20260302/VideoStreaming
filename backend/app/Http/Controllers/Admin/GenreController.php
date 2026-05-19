<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        $genres = Genre::orderBy('name')->paginate(20);

        return view('admin.genres.index', compact('genres'));
    }

    public function create(): View
    {
        return view('admin.genres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:genres,name'],
            'slug' => ['required', 'string', 'max:50', 'unique:genres,slug'],
        ]);

        Genre::create($data);

        return redirect()->route('admin.genres.index')->with('success', 'ジャンルを登録しました');
    }

    public function edit(Genre $genre): View
    {
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:genres,name,'.$genre->id],
            'slug' => ['required', 'string', 'max:50', 'unique:genres,slug,'.$genre->id],
        ]);

        $genre->update($data);

        return redirect()->route('admin.genres.index')->with('success', 'ジャンルを更新しました');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();

        return redirect()->route('admin.genres.index')->with('success', 'ジャンルを削除しました');
    }
}
