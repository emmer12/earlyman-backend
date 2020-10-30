<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CommentRequest extends FormRequest
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
            'body' => ['nullable'],
            'images' => ['nullable', 'array'],
            'property_id' => ['required', 'integer']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function body()
    {
        return $this->get('body');
    }

    public function property_id()
    {
        return $this->get('property_id');
    }

    public function images()
    {
        return $this->file('images');
    }
}
