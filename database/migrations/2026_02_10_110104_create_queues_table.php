<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            // เชื่อมไปยังตาราง doctor_schedules
            $table->foreignId('docschId')->constrained('doctor_schedules')->onDelete('cascade');
            // เชื่อมไปยังตาราง users (ผู้ป่วยที่จอง)
            $table->foreignId('userId')->constrained('users')->onDelete('cascade');
            
            $table->string('labelNo')->nullable(); // เช่น A001, Q001
            $table->string('period');  // ช่วงเวลาที่คำนวณแล้ว เช่น 09:00 - 09:20
            $table->text('Note')->nullable();
            
            // 🌟 ปรับเป็น string เพื่อรองรับทั้ง waiting, in_service, completed, cancelled และภาษาไทย
            $table->string('status')->default('waiting');
            
            $table->unsignedBigInteger('created_by')->nullable(); // ID ของคนที่ทำรายการ (Admin หรือ User)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};