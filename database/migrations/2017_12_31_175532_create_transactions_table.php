<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Bank\Bank::class)->constrained()->cascadeOnDelete();
            $table->integer('account_id')->nullable();
            $table->integer('refer_id')->nullable();
            $table->integer('transfer_id')->nullable();
            $table->integer('ref_no')->index()->nullable();

            $table->decimal('amount', 14, 4)->default(0);

            $table->nullableMorphs('userable');
            $table->enum('type', ['debit', 'credit']);
            $table->text('note')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('payment_method');
            $table->string('transaction_number')->nullable();

            $table->dateTime('date')->useCurrent();
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
        Schema::dropIfExists('transactions');
        Schema::enableForeignKeyConstraints();
    }
}
