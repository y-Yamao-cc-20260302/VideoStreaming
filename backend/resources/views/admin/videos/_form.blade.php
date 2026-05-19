@csrf
@isset($video)
  @method('PATCH')
@endisset

<div class="form-group">
  <label>タイトル <span class="text-danger">*</span></label>
  <input type="text" name="title" class="form-control" value="{{ old('title', $video->title ?? '') }}" required>
</div>
<div class="form-group">
  <label>説明</label>
  <textarea name="description" class="form-control" rows="4">{{ old('description', $video->description ?? '') }}</textarea>
</div>
<div class="form-row">
  <div class="form-group col-md-6">
    <label>カテゴリ <span class="text-danger">*</span></label>
    <select name="category_id" class="form-control" required>
      <option value="">選択してください</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(old('category_id', $video->category_id ?? null) == $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group col-md-6">
    <label>公開日</label>
    <input type="date" name="release_date" class="form-control" value="{{ old('release_date', isset($video) ? $video->release_date?->format('Y-m-d') : '') }}">
  </div>
</div>
<div class="form-group">
  <label>ジャンル</label>
  <select name="genre_ids[]" class="form-control" multiple size="6">
    @php
      $selectedGenres = old('genre_ids', isset($video) ? $video->genres->pluck('id')->all() : []);
    @endphp
    @foreach($genres as $g)
      <option value="{{ $g->id }}" @selected(in_array($g->id, $selectedGenres))>{{ $g->name }}</option>
    @endforeach
  </select>
  <small class="text-muted">Ctrl/Cmd + クリックで複数選択</small>
</div>
<div class="form-row">
  <div class="form-group col-md-8">
    <label>ストリーミング URL（HLS 等） <span class="text-danger">*</span></label>
    <input type="text" name="stream_url" class="form-control" value="{{ old('stream_url', $video->stream_url ?? '') }}" required>
  </div>
  <div class="form-group col-md-4">
    <label>尺（秒）</label>
    <input type="number" name="duration_sec" class="form-control" value="{{ old('duration_sec', $video->duration_sec ?? 0) }}" min="0">
  </div>
</div>
<div class="form-group">
  <label>サムネイル</label>
  <input type="file" name="thumbnail" class="form-control-file" accept="image/*">
  @isset($video)
    @if($video->thumbnail_path)
      <p class="mt-2"><img src="{{ asset('storage/'.$video->thumbnail_path) }}" alt="" style="max-height:120px;"></p>
    @endif
  @endisset
</div>
<div class="form-check mb-3">
  <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" @checked(old('is_published', $video->is_published ?? false))>
  <label class="form-check-label" for="is_published">公開する</label>
</div>
<button type="submit" class="btn btn-primary">保存</button>
<a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">戻る</a>
