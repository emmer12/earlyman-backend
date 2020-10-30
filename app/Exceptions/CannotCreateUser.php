<?php

namespace App\Exceptions;

use Exception;

class CannotCreateUser extends Exception
{
    public static function duplicateEmail(string $email): self
    {
        return new static("The email address [$email] already exists.");
    }

    public static function duplicateUsername(string $username): self
    {
        return new static("The username [$username] already exists.");
    }
}
