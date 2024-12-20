<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'announcement_type' => 'required|in:1,2',
            'property_type' => 'required|in:1,2,3,4,5,6,7',
            'price' => 'required',
                'area' => 'required',
        ];
    }
}
