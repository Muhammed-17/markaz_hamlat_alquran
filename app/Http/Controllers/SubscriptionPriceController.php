<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPrice;
use Illuminate\Http\Request;

class SubscriptionPriceController extends Controller
{
    public function index()
    {
        $prices = SubscriptionPrice::all();

        return view('subscription_prices.index', compact('prices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'circle_level' => 'required|string',
            'education_level' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        SubscriptionPrice::updateOrCreate(
            [
                'circle_level' => $request->circle_level,
                'education_level' => $request->education_level
            ],
            [
                'amount' => $request->amount
            ]
        );

        return redirect()->route('subscription-prices.index')->with('success', 'تم حفظ سعر الاشتراك بنجاح');
    }

    public function destroy(SubscriptionPrice $subscriptionPrice)
    {
        $subscriptionPrice->delete();
        return redirect()->route('subscription-prices.index')->with('success', 'تم حذف السعر بنجاح');
    }
}
