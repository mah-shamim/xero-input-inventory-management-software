<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasequotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasequotations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Supplier::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->constrained('users')->cascadeOnDelete();
            $table->integer('purchase_id')->nullable();

            $table->decimal('shipping_cost', 14, 4)->default(0);
            $table->decimal('overall_discount', 4, 4)->default(0);
            $table->decimal('total', 20, 4)->default(0);
            $table->decimal('total_weight', 20, 4)->default(0);

            $table->string('quotation_no')->index();
            $table->text('note')->nullable();

            $table->timestamp('quotation_date');
            $table->timestamps();
        });

        Schema::create('product_purchasequotation', function (Blueprint $table) {
            $table->id('pq_id');
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Purchasequotation::class)->constrained()->cascadeOnDelete();
            $table->integer('unit_id');

            $table->decimal('quantity', 36, 18)->unsigned();
            $table->decimal('price', 20, 4)->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('subtotal', 20, 4);
            $table->decimal('weight', 20, 4)->default(0);
            $table->decimal('weight_total', 20, 4)->default(0);

            $table->json('others')->nullable();

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
        Schema::dropIfExists('purchasequotations');
        Schema::dropIfExists('product_purchasequotation');
        Schema::enableForeignKeyConstraints();
    }
}
