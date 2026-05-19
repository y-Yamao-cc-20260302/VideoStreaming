<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        $notices = Notice::orderByDesc('published_at')->paginate(20);

        return view('admin.notices.index', compact('notices'));
    }

    public function create(): View
    {
        return view('admin.notices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Notice::create($data);

        return redirect()->route('admin.notices.index')->with('success', 'お知らせを登録しました');
    }

    public function edit(Notice $notice): View
    {
        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice): RedirectResponse
    {
        $data = $this->validateData($request);
        $notice->update($data);

        return redirect()->route('admin.notices.index')->with('success', 'お知らせを更新しました');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $notice->delete();

        return redirect()->route('admin.notices.index')->with('success', 'お知らせを削除しました');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string'],
            'published_at' => ['required', 'date'],
            'expired_at'   => ['nullable', 'date', 'after:published_at'],
        ]);
    }
}
