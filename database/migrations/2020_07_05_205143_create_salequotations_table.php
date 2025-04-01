<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalequotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salequotations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->constrained('users')->cascadeOnDelete();
            $table->integer('sale_id')->nullable();
            $table->integer('quotation_no')->index();

            $table->decimal('shipping_cost', 14, 0)->default(0);
            $table->decimal('overall_discount', 14, 4)->nullable();
            $table->decimal('total', 20, 4);
            $table->decimal('total_weight', 20, 4)->default(0);

            $table->text('note')->nullable();

            $table->timestamp('quotation_date');
            $table->timestamps();
        });

        Schema::create('product_salequotation', function (Blueprint $table) {
            $table->bigIncrements('sq_id');
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Salequotation::class)->constrained()->cascadeOnDelete();
            $table->integer('unit_id');

            $table->decimal('quantity', 36, 18)->unsigned();
            $table->decimal('price', 20, 4);
            $table->decimal('discount', 14, 4);
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
        Schema::dropIfExists('salequotations');
        Schema::dropIfExists('product_salequotation');
        Schema::enableForeignKeyConstraints();
    }
}
