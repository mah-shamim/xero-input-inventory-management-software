<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Supplier::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();

            $table->decimal('shipping_cost', 14, 4)->nullable();
            $table->decimal('overall_discount', 14, 4)->nullable();
            $table->decimal('total', 20, 4);
            $table->decimal('total_weight', 20, 4)->nullable();

            $table->json('sum_fields')->nullable();
            $table->string('bill_no')->index();
            $table->string('ref')->unique()->index();
            $table->string('status')->index();
            $table->string('payment_status')->index();
            $table->text('note')->nullable();

            $table->timestamp('purchase_date');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_purchase', function (Blueprint $table) {
            $table->bigIncrements('pp_id');
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Purchase::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->bigInteger('unit_id')->index();
            $table->integer('delivery_stamps')->default(1);
            $table->integer('locatable_id')->unsigned()->nullable();

            $table->decimal('purchase_quantity', 14, 4);
            $table->decimal('quantity',36,18)->unsigned();
            $table->decimal('price', 20, 4);
            $table->decimal('discount', 10, 4)->nullable();
            $table->decimal('subtotal', 20, 4);
            $table->decimal('actual_subtotal', 20, 4)->default(0);
            $table->decimal('actual_quantity', 20, 4)->default(0);
            $table->decimal('weight', 20, 4)->nullable();
            $table->decimal('weight_total', 20, 4)->nullable();
            $table->decimal('adjustment', 20, 4)->nullable();

            $table->string('locatable_type');
            $table->string('location_value');
            $table->json('others')->nullable();

            $table->timestamp('delivery_date')->useCurrent();
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
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('product_purchase');
        Schema::enableForeignKeyConstraints();
    }
}
