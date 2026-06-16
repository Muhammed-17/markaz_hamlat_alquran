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
