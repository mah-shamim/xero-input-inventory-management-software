<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Bank\Transaction::class)->nullable()->constrained()->cascadeOnDelete();
            $table->integer('transfer_id')->nullable();
            $table->morphs('paymentable');

            $table->decimal('paid', 20, 4)->default(0);

            $table->string('payment_type');
            $table->string('transaction_number')->nullable();


            $table->dateTime('date')->useCurrent();
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
        Schema::dropIfExists('payments');
        Schema::enableForeignKeyConstraints();
    }
}
