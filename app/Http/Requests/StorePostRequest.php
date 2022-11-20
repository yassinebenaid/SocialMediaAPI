<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            "type" => ["required", "regex:#^(text|media){1}$#"],
            "title" => "max:50",
            "body" => ["required_if:type,text", "max:500"],
            "image" => ["required_if:type,media", "mimes:png,jpg,gpeg"]
        ];
    }
}
