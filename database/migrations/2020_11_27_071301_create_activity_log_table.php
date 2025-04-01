<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->index('log_name');
            $table->nullableMorphs('causer', 'causer');
            $table->nullableMorphs('subject', 'subject');
            $table->string('log_name')->nullable();
            $table->string('event')->nullable();
            $table->text('description');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->json('request_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
