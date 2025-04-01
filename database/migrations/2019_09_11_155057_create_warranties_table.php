<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarrantiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();

            $table->decimal('quantity',10,2)->default(0);
            $table->enum('status',['Receive from Customer',
                'Send to Supplier',
                'Receive from Supplier',
                'Delivered to Customer',
                'Damaged']);
            $table->text('note')->nullable();
            $table->dateTime('warranty_date')->index();
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
        Schema::dropIfExists('warranties');
        Schema::enableForeignKeyConstraints();
    }
}
