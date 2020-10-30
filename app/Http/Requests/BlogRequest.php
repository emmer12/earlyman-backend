<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class BlogRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'body' => ['required'],
            'images' => ['nullable', 'array']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function title(): string
    {
        return $this->get('title');
    }

    public function body(): string
    {
        return $this->get('body');
    }

    public function images()
    {
        return $this->file('images');
    }
}
