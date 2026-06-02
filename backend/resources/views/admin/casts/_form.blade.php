@csrf
@isset($cast)
  @method('PATCH')
@endisset

<div class="form-group">
    <label>名前<span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ $cast->name }}">
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label>性別</label>
        <select name="gender" class="form-control">
            <option value="1" @selected(old('gender',$cast->gender ?? '') == 1)>男性</option>
            <option value="2" @selected(old('gender',$cast->gender ?? '') == 2)>女性</option>
            <option value="3" @selected(old('gender',$cast->gender ?? '') == 3)>そのほか</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>誕生日</label>
        <input type="date" name="birthday" class="form-control" value="{{ $cast->birthday }}">
    </div>
</div>
<div class="form-group">
    <label>職業</label>
    <select name="occupation_id" class="form-control">
        @foreach($occupations as $occupation)
            <option value="{{ $occupation->id }}" @selected(old('occupation_id',$cast->occupation_id ?? null) == $occupation->id)>{{ $occupation->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>写真</label>
    <input type="file" name="picture" class="form-control-file" accept="image/*" >
    @isset($cast)
        @if($cast->picture_path)
            <p class="mt-2"><img src="{{ asset('storage/'.$cast->picture_path) }}" alt="" style="max-height:120px;"></p>
        @endif
    @endisset
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_publish" id="is_publish" value="1" @checked(old('is_publish', $cast->is_publish ?? false))>
    <label class="form-check-label" for="is_publish">公開する</label>
</div>
<button type="submit" class="btn btn-primary">保存</button>
<a href="{{ route('admin.casts.index') }}" class="btn btn-secondary">戻る</a>