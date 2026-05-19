@csrf
@isset($category)
  @method('PATCH')
@endisset
<div class="form-group">
  <label>名前 <span class="text-danger">*</span></label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
</div>
<div class="form-group">
  <label>slug <span class="text-danger">*</span></label>
  <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug ?? '') }}" required>
</div>
<div class="form-group">
  <label>表示順</label>
  <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
</div>
<button class="btn btn-primary">保存</button>
<a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">戻る</a>
