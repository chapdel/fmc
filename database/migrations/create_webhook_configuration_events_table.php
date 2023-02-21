<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mailcoach_webhook_configurations', function (Blueprint $table) {
            $table->boolean('use_for_all_events')->default(true)->after('use_for_all_lists');
        });

        Schema::create('mailcoach_webhook_configuration_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('webhook_configuration_id');
            $table->string('name');
            $table->timestamps();

            $table
                ->foreign('webhook_configuration_id', 'wc_events_id')
                ->references('id')->on('mailcoach_webhook_configurations')
                ->cascadeOnDelete();
        });
    }
};
