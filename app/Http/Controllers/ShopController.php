<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Database\Eloquent\Builder; // Import the Builder class

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('inventory', '>', 0);

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Handle company filter
        if ($request->has('company') && !empty($request->company)) {
            $query->where('company', $request->company);
        }

        // Handle price filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Handle sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        // Get all categories and companies for filters
        $categories = Product::distinct()->pluck('category');
        $companies = Product::distinct()->pluck('company');

        return view('shop.index', compact('products', 'categories', 'companies'));
    }

    public function show($id)
    {
        $product = Product::with('reviews')->findOrFail($id);
        return view('shop.show', compact('product'));
    }

    public function checkout($id)
    {
        $product = Product::findOrFail($id);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $product->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success', ['id' => $product->id]),
                'cancel_url' => route('shop.show', ['id' => $product->id]),
            ]);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Stripe Checkout Error: ' . $e->getMessage(), [
                'product_id' => $id,
                'exception' => $e,
            ]);
            return redirect()->back()->with('error', 'Unable to initiate checkout. Please try again.');
        }
    }

    public function checkoutSuccess($id)
    {
        $product = Product::findOrFail($id);

        // Reduce inventory by 1
        $product->decrement('inventory');

        return view('shop.success', compact('product'));
    }
}
