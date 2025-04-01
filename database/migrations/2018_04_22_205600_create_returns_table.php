<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->integer('transaction_id')->nullable();

            $table->decimal('quantity', 20, 4)->default(0);
            $table->decimal('amount', 20, 4)->default(0);
            $table->morphs('returnable');

            $table->text('terms')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('returns');
        Schema::enableForeignKeyConstraints();
    }
}
