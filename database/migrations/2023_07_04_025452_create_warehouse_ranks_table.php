<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_racks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\WarehouseIsle::class, 'warehouse_isle_id')->constrained('warehouse_isles')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('storage');
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
        Schema::dropIfExists('warehouse_racks');
        Schema::enableForeignKeyConstraints();
    }
};
