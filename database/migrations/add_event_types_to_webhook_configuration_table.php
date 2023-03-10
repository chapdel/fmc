<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mailcoach_webhook_configurations', function (Blueprint $table) {
            $table->boolean('enabled')->default(true)->after('id');
            $table->boolean('use_for_all_events')->default(true)->after('use_for_all_lists');
            $table->json('events')->nullable()->after('use_for_all_events');
        });
    }
};
