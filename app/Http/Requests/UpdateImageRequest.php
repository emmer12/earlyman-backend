<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class UpdateImageRequest extends FormRequest
{
    /**
     * @var \Illuminate\Contracts\Validation\Validator
     */
    public $validator;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image'],
            'cover_image' => ['nullable', 'image']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function username()
    {
        return $this->get('username');
    }

    public function profile_image()
    {
        return $this->file('profile_image');
    }

    public function cover_image()
    {
        return $this->file('cover_image');
    }
}
