<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
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
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->string('barcode')->nullable();
            $table->integer('quantity');
            $table->integer('sell_quantity')->default(0);
            $table->integer('max_purchase_qty');
            $table->integer('min_purchase_qty');
            $table->boolean('active')->default(false);
            $table->foreignIdFor(User::class); // check if this is right, meaning if the product can belong yo many users based on barcode or not
            //$table->foreignIdFor(Category::class); //many to many
            $table->foreignIdFor(Brand::class);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Schema::dropIfExists('products');
    }
}