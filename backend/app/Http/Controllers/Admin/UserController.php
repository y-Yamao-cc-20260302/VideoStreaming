<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($keyword = $request->string('keyword')->toString()) {
            $like = '%'.$keyword.'%';
            $query->where(fn ($q) => $q->where('email', 'ILIKE', $like)->orWhere('name', 'ILIKE', $like));
        }

        $users = $query->with('activeSubscription.plan')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['activeSubscription.plan']);
        $watchHistories = $user->watchHistories()
            ->with('video')
            ->orderByDesc('watched_at')
            ->limit(20)
            ->get();

        return view('admin.users.show', compact('user', 'watchHistories'));
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,suspended'],
        ]);

        $user->update($data);

        return back()->with('success', 'ステータスを変更しました');
    }
}
