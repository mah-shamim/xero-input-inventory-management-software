<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);

            $table->string('name');
            $table->string('code');
            $table->string('web_url')->nullable();
            $table->string('contact_name');
            $table->text('address1');
            $table->text('address2')->nullable();
            $table->text('contact_phone');

            $table->timestamp('trial_end_at')->nullable();
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
        Schema::dropIfExists('companies');
    }
}
