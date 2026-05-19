<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::orderBy('price_jpy')->paginate(20);

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        SubscriptionPlan::create($data);

        return redirect()->route('admin.subscription-plans.index')->with('success', 'プランを登録しました');
    }

    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        return view('admin.subscription-plans.edit', ['plan' => $subscriptionPlan]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $data = $this->validateData($request, $subscriptionPlan->id);
        $subscriptionPlan->update($data);

        return redirect()->route('admin.subscription-plans.index')->with('success', 'プランを更新しました');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        try {
            $subscriptionPlan->delete();
        } catch (\Throwable $e) {
            return back()->with('error', '加入者が存在するため削除できません');
        }

        return redirect()->route('admin.subscription-plans.index')->with('success', 'プランを削除しました');
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        $rules = [
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:50', 'unique:subscription_plans,code'.($id ? ','.$id : '')],
            'price_jpy'   => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
