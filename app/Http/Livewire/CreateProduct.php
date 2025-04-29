<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;
use Illuminate\Support\Str;

class CreateProduct extends Component
{
    use WithFileUploads;

    public $name;
    public $price;
    public $description;
    public $image;
    public $category;
    public $company;
    public $colors = ['#000000'];
    public $featured = false;
    public $freeShipping = false;
    public $inventory = 15;

    public $categories = ['office', 'kitchen', 'bedroom', 'electronics', 'furniture'];
    public $companies = ['ikea', 'liddy', 'marcos', 'apple', 'samsung'];

    protected $rules = [
        'name' => 'required|min:3|max:100',
        'price' => 'required|numeric|min:0',
        'description' => 'required|max:1000',
        'image' => 'nullable|image|max:1024',
        'category' => 'required|in:office,kitchen,bedroom,electronics,furniture',
        'company' => 'required|in:ikea,liddy,marcos,apple,samsung',
        'colors' => 'required|array|min:1',
        'inventory' => 'required|integer|min:0',
    ];

    public function addColor()
    {
        $this->colors[] = '#000000';
    }

    public function removeColor($index)
    {
        if (count($this->colors) > 1) {
            unset($this->colors[$index]);
            $this->colors = array_values($this->colors);
        }
    }

    public function saveProduct()
    {
        $this->validate();

        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Upload image if provided
            $imagePath = null;
            if ($this->image) {
                $imageName = Str::slug($this->name) . '-' . time() . '.' . $this->image->extension();
                $this->image->storeAs('public/products', $imageName);
                $imagePath = '/storage/products/' . $imageName;
            }

            // Create product in Stripe
            $stripeProduct = StripeProduct::create([
                'name' => $this->name,
                'description' => $this->description,
                'images' => $imagePath ? [url($imagePath)] : [],
                'metadata' => [
                    'category' => $this->category,
                    'company' => $this->company
                ]
            ]);

            // Create price in Stripe (in cents)
            $stripePrice = StripePrice::create([
                'product' => $stripeProduct->id,
                'unit_amount' => $this->price * 100,
                'currency' => 'usd',
            ]);

            // Create product in database
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'image' => $imagePath,
                'category' => $this->category,
                'company' => $this->company,
                'colors' => $this->colors,
                'featured' => $this->featured,
                'free_shipping' => $this->freeShipping,
                'inventory' => $this->inventory,
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePrice->id,
                'user_id' => Auth::id()
            ]);

            session()->flash('message', 'Product created successfully!');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->name = '';
        $this->price = '';
        $this->description = '';
        $this->image = null;
        $this->category = '';
        $this->company = '';
        $this->colors = ['#000000'];
        $this->featured = false;
        $this->freeShipping = false;
        $this->inventory = 15;
    }

    public function render()
    {
        return view('livewire.create-product');
    }
}
