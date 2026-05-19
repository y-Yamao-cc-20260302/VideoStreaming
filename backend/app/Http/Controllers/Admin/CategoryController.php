<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:50', 'unique:categories,name'],
            'slug'       => ['required', 'string', 'max:50', 'unique:categories,slug'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを登録しました');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:50', 'unique:categories,name,'.$category->id],
            'slug'       => ['required', 'string', 'max:50', 'unique:categories,slug,'.$category->id],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを更新しました');
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            $category->delete();
        } catch (\Throwable $e) {
            return back()->with('error', '動画が存在するため削除できません');
        }

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを削除しました');
    }
}
