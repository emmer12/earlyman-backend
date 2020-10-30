<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    protected $errors;

    public function __construct($errors)
    {
        $this->$errors = $errors;
    }

    public static function errors($value)
    {
        return new static($value);
    }
}
