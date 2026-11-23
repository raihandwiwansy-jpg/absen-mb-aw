<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['administrator', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'class' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'enrollment_date' => ['required', 'date'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'instrument_id' => ['nullable', 'exists:instruments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'organizational_period_id' => ['nullable', 'exists:organizational_periods,id'],
            'status' => ['required', 'in:active,inactive,alumni'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'gender' => 'jenis kelamin',
            'class' => 'kelas',
            'phone' => 'nomor telepon',
            'address' => 'alamat',
            'photo' => 'foto',
            'enrollment_date' => 'tanggal bergabung',
            'division_id' => 'divisi',
            'instrument_id' => 'instrumen',
            'position_id' => 'jabatan',
            'organizational_period_id' => 'periode organisasi',
            'status' => 'status',
        ];
    }

    /**
     * Get custom messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'enrollment_date.required' => 'Tanggal bergabung wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
