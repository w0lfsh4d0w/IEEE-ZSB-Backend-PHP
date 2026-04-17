<?php

namespace http\forms;

use core\ValidationException;
use core\Validator;

class LoginForm
{
    protected $errors = [];

    public function __construct(public array $attributes)
    {
        if (!Validator::email($attributes['email'])) {
            $errors['email'] = 'Please provide a valid email address.';
        }
        if (!Validator::string($attributes['password'], 100)) {
            $this->errors['password'] = 'Please provide a valid password.';
        }
    }
    public static function  validate($attributes)
    {
        $instance = new static($attributes);
        if ($instance->failed()) {
            ValidationException::throw($instance->errors(), $instance->attributes);
        }
        // if it valid  
        return $instance;
    }
    public function failed()
    {
        return count($this->errors);
    }
    public function errors()
    {
        return $this->errors;
    }
    function error($field, $message)
    {
        $this->errors[$field] = $message;
    }
}
