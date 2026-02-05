<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessPlanController extends Controller
{
    public function index()
    {
        $plans = BusinessPlan::with('product')->get();

        $plansWithAchievements = $plans->map(function ($plan) {
            $achievements = $this->calculateAchievements($plan);
            $plan->actual_quantity = $achievements['quantity'];
            $plan->actual_amount = $achievements['amount'];
            $plan->remaining_quantity = $plan->target_quantity - $achievements['quantity'];
            $plan->remaining_amount = $plan->target_amount - $achievements['amount'];
            return $plan;
        });

        return view('business-plans.index', compact('plansWithAchievements'));
    }

    public function create()
    {
        $products = Product::all();
        return view('business-plans.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'target_quantity' => 'required|integer|min:1',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        BusinessPlan::create($request->all());

        return redirect()->route('business-plans.index')->with('success', 'Business plan created successfully.');
    }

    public function show(BusinessPlan $businessPlan)
    {
        $achievements = $this->calculateAchievements($businessPlan);
        $businessPlan->actual_quantity = $achievements['quantity'];
        $businessPlan->actual_amount = $achievements['amount'];
        $businessPlan->remaining_quantity = $businessPlan->target_quantity - $achievements['quantity'];
        $businessPlan->remaining_amount = $businessPlan->target_amount - $achievements['amount'];

        return view('business-plans.show', compact('businessPlan'));
    }

    public function edit(BusinessPlan $businessPlan)
    {
        $products = Product::all();
        return view('business-plans.edit', compact('businessPlan', 'products'));
    }

    public function update(Request $request, BusinessPlan $businessPlan)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'target_quantity' => 'required|integer|min:1',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $businessPlan->update($request->all());

        return redirect()->route('business-plans.index')->with('success', 'Business plan updated successfully.');
    }

    public function destroy(BusinessPlan $businessPlan)
    {
        $businessPlan->delete();

        return redirect()->route('business-plans.index')->with('success', 'Business plan deleted successfully.');
    }

    private function calculateAchievements(BusinessPlan $plan)
    {
        $totalQuantity = DB::table('product_inventories')
            ->where('product_id', $plan->product_id)
            ->sum('quantity');

        $unitPrice = $plan->target_quantity > 0 ? $plan->target_amount / $plan->target_quantity : 0;
        $totalAmount = $totalQuantity * $unitPrice;

        return [
            'quantity' => $totalQuantity,
            'amount' => $totalAmount,
        ];
    }
}
