<?php

namespace App\Http\Requests;

use App\Models\Queue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQueueStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // การเช็คสิทธิ์แบบละเอียด (เจ้าของคิว vs Staff/Admin) ทำใน QueueService::authorizeTransition
        // เพราะต้องรู้สถานะปัจจุบันของคิวที่ถูก lock ไว้ก่อน ไม่ใช่แค่ role เฉยๆ
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Queue::STATUSES)],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'กรุณาระบุสถานะที่ต้องการเปลี่ยน',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ];
    }
}