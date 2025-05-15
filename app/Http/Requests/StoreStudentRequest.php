<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // app/Http/Requests/StoreStudentRequest.php
    public function rules(): array
    {
        return [
            'classroom_id'     => 'required|exists:classrooms,id',
            'name'             => 'required|string|max:100',
            'nik'              => 'required|string|max:20|unique:students,nik',
            'birth_date'       => 'required|date',
            'gender'           => 'required|in:L,P',
            'address'          => 'required|string|max:255',
            'medical_history'  => 'nullable|string',
            /* foto opsional */
            'photo'            => 'nullable|image|max:2048',
            /* data orang tua (jika ada langsung)  */
            'mother_name'      => 'nullable|string|max:100',
            'father_name'      => 'nullable|string|max:100',
            // dst…
        ];
    }

}
