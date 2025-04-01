<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Inventory\Category::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Account::class)->constrained()->cascadeOnDelete();
            $table->bigInteger('transaction_id')->nullable();
            $table->integer('payment_method');

            $table->decimal('amount', 20, 4)->default(0);

            $table->morphs('userable');
            $table->string('ref');
            $table->text('note')->nullable();

            $table->timestamp('expense_date');
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
        Schema::dropIfExists('expenses');
        Schema::enableForeignKeyConstraints();
    }
}
