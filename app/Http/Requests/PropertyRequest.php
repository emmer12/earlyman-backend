<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class PropertyRequest extends FormRequest
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
            'tags' => ['array'],
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

    public function tags()
    {
        return $this->get('tags');
    }

    public function images()
    {
        return $this->file('images');
    }
}
