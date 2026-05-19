@csrf
@isset($plan)@method('PATCH')@endisset
<div class="form-row">
  <div class="form-group col-md-6"><label>名前 <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required></div>
  <div class="form-group col-md-6"><label>コード <span class="text-danger">*</span></label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $plan->code ?? '') }}" required></div>
</div>
<div class="form-group"><label>月額（円） <span class="text-danger">*</span></label>
  <input type="number" name="price_jpy" class="form-control" value="{{ old('price_jpy', $plan->price_jpy ?? 0) }}" min="0" required></div>
<div class="form-group"><label>説明</label>
  <textarea name="description" class="form-control" rows="4">{{ old('description', $plan->description ?? '') }}</textarea></div>
<div class="form-check mb-3">
  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true))>
  <label class="form-check-label" for="is_active">提供中</label>
</div>
<button class="btn btn-primary">保存</button>
<a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">戻る</a>
