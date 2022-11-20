<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "username" => ["required", "max:255", "min:8"],
            "email" => ["required", "email", "max:255", "unique:users,email"],
            "password" => ["required", "confirmed", "min:8", "max:255"],
            "region" => ["string"],
            "phone_number" => ["numeric", "max:15"],
            "gender" => ["string", "regex:#^(male|female){1}$#"],
            "bio" => ["string", "max:500"],
            "birthday" => ["required", "date"],
            "profile_image" => ["mimes:png,jpg,jpeg"]
        ];
    }
}
