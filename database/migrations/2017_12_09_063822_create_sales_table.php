<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Customer::class)->constrained()->cascadeOnDelete();
            $table->integer('table')->nullable();

            $table->string('ref')->unique()->index();
            $table->string('status')->index();
            $table->string('payment_status')->index();
            $table->string('biller')->index(); /* TODO: this will be changed to user_id */
            $table->string('salesman_code')->nullable();
            $table->decimal('shipping_cost', 14, 4)->nullable();
            $table->decimal('overall_discount', 14, 4)->nullable();
            $table->decimal('total', 20, 4);
            $table->decimal('previous_due', 16, 4)->default(0);
            $table->decimal('total_weight', 20, 4)->default(0);

            $table->text('note')->nullable();

            $table->timestamp('sales_date');
            $table->timestamps();
        });

        Schema::create('product_sale', function (Blueprint $table) {
            $table->bigIncrements('ps_id');
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Sale::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->integer('locatable_id')->unsigned()->nullable();
            $table->integer('unit_id');

            $table->decimal('quantity',36,18)->unsigned();
            $table->decimal('price', 20, 4);
            $table->decimal('discount', 4, 4);
            $table->decimal('subtotal', 20, 4);

            $table->decimal('weight', 20, 4)->default(0);
            $table->decimal('weight_total', 20, 4)->default(0);
            $table->decimal('adjustment', 20, 4)->nullable();
            $table->decimal('actual_subtotal', 20, 4)->default(0);
            $table->decimal('actual_quantity', 20, 4)->default(0);

            $table->string('locatable_type');
            $table->string('location_value');

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
        Schema::dropIfExists('sales');
        Schema::dropIfExists('product_sale');
        Schema::enableForeignKeyConstraints();

    }
}
