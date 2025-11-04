<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id'
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Jadwal harus dipilih',
            'schedule_id.exists' => 'Jadwal tidak valid',
            'seat_ids.required' => 'Minimal pilih 1 kursi',
            'seat_ids.array' => 'Format kursi tidak valid',
            'seat_ids.*.exists' => 'Kursi tidak valid'
        ];
    }
}