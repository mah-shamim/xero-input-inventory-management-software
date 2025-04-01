<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Sale::class)->nullable()->constrained()->cascadeOnDelete();
            $table->integer('payment_status');
            $table->integer('payment_type');

            $table->boolean('is_canceled');
            $table->boolean('is_default')->default(false);

            $table->decimal('shipping_cost', 14, 4)->nullable();
            $table->decimal('total_weight', 20, 4)->nullable();
            $table->decimal('overall_discount', 14, 4)->nullable();
            $table->decimal('total', 20, 4);
            $table->decimal('paid', 20, 4)->default(0);
            $table->decimal('previous_due', 16, 4)->default(0);

            $table->string('order_no')->unique();
            $table->string('customer_order_no')->nullable();
            $table->string('status');
            $table->string('biller');
            $table->string('salesman_code')->nullable();
            $table->string('order_date');
            $table->string('table')->nullable();
            $table->text('note')->nullable();

            $table->timestamp('delivered_date')->nullable();
            $table->timestamps();
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->integer('unit_id');

            $table->decimal('quantity', 36, 18)->unsigned();
            $table->decimal('price', 20, 4);
            $table->decimal('discount', 14, 4);
            $table->decimal('subtotal', 20, 4);

            $table->decimal('weight', 20, 4)->nullable();
            $table->decimal('weight_total', 20, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('order_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Supplier::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();

            $table->boolean('is_canceled')->default(false);

            $table->decimal('shipping_cost', 14, 4)->nullable();
            $table->decimal('overall_discount', 14, 4)->nullable();
            $table->decimal('total', 20, 4);

            $table->string('order_no')->unique();
            $table->string('status');
            $table->string('order_date');
            $table->text('note')->nullable();

            $table->timestamps();
        });


        Schema::create('order_purchase_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->integer('unit_id');

            $table->decimal('quantity', 36, 18);
            $table->decimal('price', 20, 4);
            $table->decimal('subtotal', 20, 4);
            $table->decimal('discount');
            $table->timestamps();
        });

        Schema::create('order_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Order::class)->constrained()->cascadeOnDelete();

            $table->decimal('paid', 20, 4);

            $table->string('payment_type');
            $table->string('transaction_number')->nullable();
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
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_product');
        Schema::dropIfExists('order_purchases');
        Schema::dropIfExists('order_purchase_product');
        Schema::dropIfExists('order_payment');
        Schema::enableForeignKeyConstraints();
    }
}
