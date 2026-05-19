@csrf
@isset($notice)@method('PATCH')@endisset
<div class="form-group"><label>タイトル <span class="text-danger">*</span></label>
  <input type="text" name="title" class="form-control" value="{{ old('title', $notice->title ?? '') }}" required></div>
<div class="form-group"><label>本文 <span class="text-danger">*</span></label>
  <textarea name="body" class="form-control" rows="6" required>{{ old('body', $notice->body ?? '') }}</textarea></div>
<div class="form-row">
  <div class="form-group col-md-6"><label>公開日時 <span class="text-danger">*</span></label>
    <input type="datetime-local" name="published_at" class="form-control"
      value="{{ old('published_at', isset($notice) ? $notice->published_at?->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required></div>
  <div class="form-group col-md-6"><label>終了日時</label>
    <input type="datetime-local" name="expired_at" class="form-control"
      value="{{ old('expired_at', isset($notice) ? $notice->expired_at?->format('Y-m-d\TH:i') : '') }}"></div>
</div>
<button class="btn btn-primary">保存</button>
<a href="{{ route('admin.notices.index') }}" class="btn btn-secondary">戻る</a>
