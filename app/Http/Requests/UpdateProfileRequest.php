<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->id === request()->user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "username" => ["max:255", "min:8"],
            "email" => ["email", "max:255", "unique:users,email"],
            "password" => ["confirmed", "min:8", "max:255"],
            "region" => ["string"],
            "phone_number" => ["numeric", "max:15"],
            "gender" => ["string", "regex:#^(male|female){1}$#"],
            "bio" => ["string", "max:500"],
            "birthday" => ["date"],
            "profile_image" => ["image", "mimes:png,jpg,jpeg"]
        ];
    }
}
