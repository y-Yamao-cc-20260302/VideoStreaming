@csrf
@isset($genre)@method('PATCH')@endisset
<div class="form-group"><label>名前 <span class="text-danger">*</span></label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $genre->name ?? '') }}" required></div>
<div class="form-group"><label>slug <span class="text-danger">*</span></label>
  <input type="text" name="slug" class="form-control" value="{{ old('slug', $genre->slug ?? '') }}" required></div>
<button class="btn btn-primary">保存</button>
<a href="{{ route('admin.genres.index') }}" class="btn btn-secondary">戻る</a>
