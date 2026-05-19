<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'video']);

        if ($videoId = $request->integer('video_id')) {
            $query->where('video_id', $videoId);
        }

        $reviews = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'レビューを削除しました');
    }
}
