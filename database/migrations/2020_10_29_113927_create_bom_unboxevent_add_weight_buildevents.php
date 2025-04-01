<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomUnboxeventAddWeightBuildevents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unboxevents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->constrained()->cascadeOnDelete();

            $table->decimal('quantity', 20, 4);

            $table->string('location');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('unboxevents');
        Schema::enableForeignKeyConstraints();

    }
}
