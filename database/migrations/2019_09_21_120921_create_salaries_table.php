<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Payroll\Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Bank\Transaction::class)->nullable()->constrained()->cascadeOnDelete();
            $table->integer('payment_method');

            $table->decimal('current_salary', 14, 2)->default(0);
            $table->decimal('amount',10,4)->default(0);
            $table->decimal('festival_bonus',10,4)->default(0);
            $table->decimal('other_bonus',10,4)->default(0);
            $table->decimal('deduction',10,4)->default(0);
            $table->decimal('total',14,4)->default(0);

            $table->text('note')->nullable();

            $table->timestamp('salary_date');
            $table->timestamp('salary_month');
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
        Schema::dropIfExists('salaries');
        Schema::enableForeignKeyConstraints();
    }
}
