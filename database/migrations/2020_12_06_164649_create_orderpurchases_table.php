<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderpurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderpurchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Supplier::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Purchase::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Order::class)->nullable()->constrained()->cascadeOnDelete();
            $table->integer('order_no')->unsigned()->index();

            $table->boolean('is_cancelled')->default(false);

            $table->decimal('shipping_cost', 14, 4)->nullable();
            $table->decimal('overall_discount', 14, 4)->nullable();
            $table->decimal('total', 20, 4);
            $table->decimal('total_weight', 20, 4)->nullable();


            $table->text('note')->nullable();


            $table->timestamp('order_date');
            $table->timestamp('expected_receive_date')->nullable();
            $table->timestamps();
        });

        Schema::create('orderpurchase_product', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Orderpurchase::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->integer('unit_id');

            $table->decimal('quantity', 36, 18)->unsigned();
            $table->decimal('price', 20, 4);
            $table->decimal('discount');
            $table->decimal('subtotal', 20, 4);
            $table->decimal('weight', 20, 4)->nullable();
            $table->decimal('weight_total', 20, 4)->nullable();

            $table->json('others')->nullable();

            $table->timestamp('received_date')->nullable();
            $table->timestamps();
        });


        Schema::table('order_purchase_product', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Inventory\Orderpurchase::class)->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('orderpurchases');
        Schema::dropIfExists('orderpurchase_product');
        Schema::dropIfExists('order_purchase_product');
        Schema::enableForeignKeyConstraints();
    }
}
