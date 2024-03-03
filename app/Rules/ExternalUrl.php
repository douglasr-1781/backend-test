<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExternalUrl implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $url_host = parse_url($value, PHP_URL_HOST);
        $base_url_host = parse_url(env('APP_URL'), PHP_URL_HOST);
        
        if($url_host == $base_url_host || empty($url_host))
        {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A url enviada deve ser externa à aplicação.';
    }
}
