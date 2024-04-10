<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    public static function validate($data, $rule)
    {
        $validator = Validator::make($data, $rule);

        if ($validator->fails())
            throw new ValidationException($validator);

        return $validator->validated();
    }

}


?>