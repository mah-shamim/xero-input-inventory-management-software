<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildeventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildevents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Bank\Transaction::class, 'transaction_id')->nullable()->constrained()->cascadeOnDelete();

            $table->decimal('quantity', 20, 4);
            $table->decimal('total_cost', 20, 4);
            $table->decimal('mcpu', 20, 4)->nullable(); //material cost per unit
            $table->decimal('expense_total', 20, 4)->nullable();
            $table->decimal('total_weight', 20, 4)->default(0);

            $table->string('location');
            $table->json('material_detail');
            $table->json('other_expenses')->nullable();
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
        Schema::dropIfExists('buildevents');
        Schema::enableForeignKeyConstraints();

    }
}
