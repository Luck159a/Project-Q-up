<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->index(['docschId', 'status'], 'queues_docschid_status_index');
            $table->index(['userId', 'status'], 'queues_userid_status_index');
            $table->index(['docschId', 'period', 'status'], 'queues_docschid_period_status_index');
        });

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->index('schedule_date', 'doctor_schedules_schedule_date_index');
            $table->index(['user_id', 'schedule_date'], 'doctor_schedules_user_id_schedule_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropIndex('queues_docschid_status_index');
            $table->dropIndex('queues_userid_status_index');
            $table->dropIndex('queues_docschid_period_status_index');
        });

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropIndex('doctor_schedules_schedule_date_index');
            $table->dropIndex('doctor_schedules_user_id_schedule_date_index');
        });
    }
};