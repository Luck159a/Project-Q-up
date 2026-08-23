<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'docschId' => ['required', 'integer', 'exists:doctor_schedules,id'],
            'period' => ['required', 'string', 'max:50'],
            'Note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'docschId.required' => 'กรุณาเลือกตารางแพทย์ก่อนทำการจอง',
            'docschId.exists' => 'ไม่พบตารางแพทย์ที่เลือก',
            'period.required' => 'กรุณาเลือกช่วงเวลาที่ต้องการก่อนกดยืนยันครับ',
            'Note.max' => 'หมายเหตุยาวเกินไป (ไม่เกิน 1000 ตัวอักษร)',
        ];
    }
}