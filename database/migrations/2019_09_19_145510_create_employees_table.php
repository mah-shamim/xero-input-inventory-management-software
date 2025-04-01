<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Payroll\Department::class)->constrained()->cascadeOnDelete();

            $table->string('employee_id');
            $table->string('avatar')->default('/images/avatar.jpg');

            $table->string('name')->index();
            $table->string('designation')->nullable();
            $table->string('contract_type')->nullable();
            $table->decimal('salary',10,4)->default(0);

            $table->string('mobile')->nullable();
            $table->string('nid')->nullable();
            $table->string('emergency')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('birth')->nullable();
            $table->string('join_date')->nullable();
            $table->text('note')->nullable();

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
        Schema::dropIfExists('employees');
        Schema::enableForeignKeyConstraints();
    }
}
