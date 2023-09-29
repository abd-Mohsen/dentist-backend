<?php

use App\Models\Image;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsTable extends Migration
{

    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->foreignIdFor(Image::class)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('image_id')->references('id')->on('images')->onDelete('set null');
        });
    }


    public function down()
    {
        Schema::dropIfExists('brands');
    }
}