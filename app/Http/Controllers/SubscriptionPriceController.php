<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPrice;
use App\Http\Requests\StoreSubscriptionPriceRequest;

class SubscriptionPriceController extends Controller
{
    public function index()
    {
        $prices = SubscriptionPrice::all();

        return view('subscription_prices.index', compact('prices'));
    }

    public function store(StoreSubscriptionPriceRequest $request)
    {
        $validated = $request->validated();

        $existing = SubscriptionPrice::where('circle_level', $validated['circle_level'])
            ->where('education_stage', $validated['education_stage'])
            ->first();

        // ✅ FIX: طبقة حماية إضافية (server-side) — لو السعر موجود فعلاً بنفس القيمة،
        // تجاهل التحديث بدل عمل UPDATE query غير ضروري.
        // ده احتياطي فقط؛ المنع الأساسي المفروض يكون بالـ JS في subscription_prices/index.blade.php
        if ($existing && (string) $existing->amount === (string) $validated['amount']) {
            return redirect()->route('subscription-prices.index')->with('info', 'لم يتم إجراء أي تعديل على السعر.');
        }

        SubscriptionPrice::updateOrCreate(
            [
                'circle_level'    => $validated['circle_level'],
                'education_stage' => $validated['education_stage'],
                // 'school_grade'    => $validated['school_grade'] ?? null,
            ],
            ['amount' => $validated['amount']]
        );

        return redirect()->route('subscription-prices.index')->with('success', 'تم حفظ سعر الاشتراك بنجاح');
    }

    public function destroy(SubscriptionPrice $subscriptionPrice)
    {
        $subscriptionPrice->delete();
        return redirect()->route('subscription-prices.index')->with('success', 'تم حذف السعر بنجاح');
    }
}
