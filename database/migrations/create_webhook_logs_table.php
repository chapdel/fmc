<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mailcoach_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('webhook_configuration_id');
            $table->string('webhook_event_type');
            $table->string('event_type');
            $table->uuid('webhook_call_uuid')->index();
            $table->smallInteger('attempt')->nullable();
            $table->string('webhook_url');
            $table->json('payload');
            $table->json('response');
            $table->smallInteger('status_code')->nullable();
            $table->timestamps();

            $table
                ->foreign('webhook_configuration_id', 'wc_wls')
                ->references('id')->on('mailcoach_webhook_configurations')
                ->cascadeOnDelete();
        });
    }
};
