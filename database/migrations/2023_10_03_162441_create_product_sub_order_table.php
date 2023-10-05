<?php

use App\Models\Product;
use App\Models\SubOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_sub_order', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(SubOrder::class);
            $table->integer('quantity');
            $table->unsignedDecimal('price', 8, 2);

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('sub_order_id')->references('id')->on('sub_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sub_order');
    }
};
