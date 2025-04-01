<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTierPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_tier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('price_category_id')->unsigned()->index();
            $table->foreign('price_category_id')->references('id')->on('price_categories')->onDelete('cascade');
            $table->double("quantity");
            $table->decimal("price");
            $table->timestamp("start_date");
            $table->timestamp("end_date");
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
        Schema::dropIfExists('product_tier_prices');
        Schema::enableForeignKeyConstraints();
    }
}
