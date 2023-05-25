<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mailcoach_webhook_configurations', function (Blueprint $table) {
            $table->integer('failed_attempts')->default(0)->after('use_for_all_events');
        });
    }
};
