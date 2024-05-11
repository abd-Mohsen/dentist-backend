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
            $table->unsignedDecimal('price', 8, 2);
            $table->unsignedDecimal('weight', 6, 2);//kg
            $table->unsignedDecimal('length', 6, 2);//m
            $table->unsignedDecimal('width', 6, 2);//m
            $table->unsignedDecimal('height', 6, 2);//m
            $table->string('sku')->unique();
            $table->string('upc');
            $table->integer('quantity')->default(0);
            $table->integer('sell_quantity')->default(0);
            $table->integer('max_purchase_qty')->default(0);
            $table->integer('min_purchase_qty')->default(0);
            $table->boolean('active')->default(false);
            $table->unsignedBigInteger('owner_id'); 
            $table->foreignIdFor(Brand::class);
            $table->timestamps();
            $table->softDeletes();
            // do comment and rating (opinion) table

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('products');
    }
}