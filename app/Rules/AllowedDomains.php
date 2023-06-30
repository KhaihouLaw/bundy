<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllowedDomains implements Rule
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
    public function passes($attribute, $email)
    {
        $allowedDomains = $this->getDomains();
        return in_array(explode("@", $email)[1], $allowedDomains, true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The email is rejected, only those who are part of the organization are allowed to register');;
    }

    public static function getDomains() {
        // return ['gmail.com', 'student.laverdad.edu.ph', 'laverdad.edu.ph'];
        return ['laverdad.edu.ph'];
    }
}
