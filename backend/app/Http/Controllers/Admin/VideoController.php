<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Video\StoreVideoRequest;
use App\Http\Requests\Admin\Video\UpdateVideoRequest;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Video::with(['category', 'genres']);

        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($genreId = $request->integer('genre_id')) {
            $query->whereHas('genres', fn ($q) => $q->where('genres.id', $genreId));
        }

        $publishedFilter = $request->string('published')->toString();
        if ($publishedFilter === '1') {
            $query->where('is_published', true);
        } elseif ($publishedFilter === '0') {
            $query->where('is_published', false);
        }

        if ($keyword = $request->string('keyword')->toString()) {
            $like = '%'.$keyword.'%';
            $query->where(fn ($q) => $q->where('title', 'ILIKE', $like)->orWhere('description', 'ILIKE', $like));
        }

        $videos = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();
        $genres = Genre::orderBy('name')->get();

        return view('admin.videos.index', compact('videos', 'categories', 'genres'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('sort_order')->get();
        $genres = Genre::orderBy('name')->get();

        return view('admin.videos.create', compact('categories', 'genres'));
    }

    public function store(StoreVideoRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['genre_ids', 'thumbnail']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $video = Video::create($data);
        $video->genres()->sync($request->input('genre_ids', []));

        return redirect()->route('admin.videos.index')->with('success', '動画を登録しました');
    }

    public function edit(Video $video): View
    {
        $video->load('genres');
        $categories = Category::orderBy('sort_order')->get();
        $genres = Genre::orderBy('name')->get();

        return view('admin.videos.edit', compact('video', 'categories', 'genres'));
    }

    public function update(UpdateVideoRequest $request, Video $video): RedirectResponse
    {
        $data = $request->safe()->except(['genre_ids', 'thumbnail']);

        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $video->update($data);
        $video->genres()->sync($request->input('genre_ids', []));

        return redirect()->route('admin.videos.index')->with('success', '動画を更新しました');
    }

    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', '動画を削除しました');
    }

    public function publish(Request $request, Video $video): RedirectResponse
    {
        $video->update(['is_published' => ! $video->is_published]);

        return back()->with('success', $video->is_published ? '公開しました' : '非公開にしました');
    }
}
