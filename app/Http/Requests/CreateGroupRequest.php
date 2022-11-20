<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
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
            "name" => "required|max:35|alpha_dash|unique:groups,name",
            "description" => "required|max:255",
            "theme" => "max:10",
            "cover" => "mimes:png,jpg,jpeg"
        ];
    }
}
