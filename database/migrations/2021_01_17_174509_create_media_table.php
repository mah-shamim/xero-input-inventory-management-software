<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('order_column')->nullable();
            $table->uuid('uuid')->nullable();
            $table->morphs('model');

            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('responsive_images');

            $table->nullableTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
}
