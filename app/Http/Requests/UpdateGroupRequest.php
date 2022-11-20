<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user()->id;
        return request()->group->isSuperAdmin($user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => "max:35|alpha_dash|unique:groups,name," . request()->group->id,
            "description" => "max:255",
            "theme" => "max:10",
        ];
    }
}
