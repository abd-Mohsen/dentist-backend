<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageProductTable extends Migration
{

    public function up()
    {
        Schema::create('image_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(Image::class);
        });
    }


    public function down()
    {
        Schema::dropIfExists('image_product');
    }
}