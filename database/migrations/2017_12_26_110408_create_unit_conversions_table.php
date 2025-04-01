<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitConversionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class, 'from_unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class, 'to_unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();

            $table->decimal('conversion_factor', 36,18)->unsigned();
            $table->timestamps();

            $table->unique(['from_unit_id', 'to_unit_id']);
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
        Schema::dropIfExists('unit_conversions');
        Schema::enableForeignKeyConstraints();
    }
}
