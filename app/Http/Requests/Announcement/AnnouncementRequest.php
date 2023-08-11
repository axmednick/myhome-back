<?php

namespace App\Http\Requests\Announcement;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AnnouncementRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {


        $rules = [
            'announcement_type' => 'required|in:1,2',
            'property_type' => 'required|in:1,2,3,4,5,6,7',
            'apartment_type' => 'required|in:1,2',
            'house_area' => 'required_if:property_type,1,2,3,4,5,6,7',
            'area' => 'required_if:property_type,1,2,3,4,5,6',
            'email'=>'required',
            'is_repaired'=>'required_if:property_type,1,2,3,4,6,7'
        ];


        if (!auth('sanctum')->check()) {
            $rules += [
                'email' => 'required|unique:users,email|email',
                'phone' => 'required',
                'name' => 'required',
                'password' => 'required|min:4|max:32',

            ];
        }



        return $rules;
    }


    public function failedValidation(Validator $validator)

    {

        throw new HttpResponseException(response()->json([

            'success'   => false,

            'message'   => 'Validation errors',

            'data'      => $validator->errors()

        ]));

    }
}
