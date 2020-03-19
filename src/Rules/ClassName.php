<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Rules;

use Illuminate\Contracts\Validation\Rule;

class ClassName implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes($attribute, $value)
    {
        return class_exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return app('translator')->get('laravel-media-library::validation.class_name');
    }
}
