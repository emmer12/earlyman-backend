<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class ProfileRequest extends FormRequest
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
        $user_id = (auth()->user()) ? auth()->id() : NULL;

        return [
            'firstname' => ['nullable','string', 'max:255'],
            'lastname' => ['nullable','string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user_id . ',id'],
            'username' => ['required','string', 'max:255', 'unique:users,username,' . $user_id . ',id'],
            'phone' => ['nullable', 'max:20'],
            'address' => ['nullable'],
            'bio' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'string', 'max:255']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function firstname()
    {
        return $this->get('firstname');
    }

    public function lastname()
    {
        return $this->get('lastname');
    }

    public function email()
    {
        return $this->get('email');
    }

    public function username()
    {
        return $this->get('username');
    }

    public function bio()
    {
        return $this->input('bio');
    }

    public function phone()
    {
        return $this->get('phone');
    }

    public function address()
    {
        return $this->get('address');
    }
    
    public function location()
    {
        return $this->get('location');
    }

    public function birthday()
    {
        return $this->get('birthday');
    }
}
