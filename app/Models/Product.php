<?php

// First, let's create the Product model
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Review;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'category',
        'company',
        'colors',
        'featured',
        'free_shipping',
        'inventory',
        'stripe_product_id',
        'stripe_price_id',
        'user_id'
    ];

    protected $casts = [
        'colors' => 'array',
        'featured' => 'boolean',
        'free_shipping' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

// Now, let's create the migration for the products table
// database/migrations/xxxx_xx_xx_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->string('image')->default('default.jpg');
            $table->string('category');
            $table->string('company');
            $table->json('colors');
            $table->boolean('featured')->default(false);
            $table->boolean('free_shipping')->default(false);
            $table->integer('inventory')->default(15);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('num_of_reviews')->default(0);
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}