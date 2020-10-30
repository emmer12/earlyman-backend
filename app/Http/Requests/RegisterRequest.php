<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
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
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function firstname(): string
    {
        return $this->get('firstname');
    }

    public function lastname(): string
    {
        return $this->get('lastname');
    }

    public function email(): string
    {
        return $this->get('email');
    }

    public function username(): string
    {
        return $this->get('username');
    }

    public function password(): string
    {
        return $this->get('password');
    }

    public function is_admin()
    {
        return $this->get('is_admin');
    }
}
