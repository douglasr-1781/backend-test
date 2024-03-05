<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmptyQueryParam implements Rule
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
        $params = explode('&', parse_url($value)['query']);

        foreach($params as $param)
        {
            if(empty(explode('=', $param)[1]))
            {
                return false;
            }
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
        return 'A url possui parâmetros vazios.';
    }
}
