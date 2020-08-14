<?php

namespace App\Rules;

use DB;
use Illuminate\Contracts\Validation\Rule;

class MoreThan4PublishedQuestionsNeeded implements Rule
{
    public $map;

    /**
     * Create a new rule instance.
     *
     * @param  \App\Map  $parent
     * @return void
     */
    public function __construct($map)
    {
        $this->map = $map; // Model instance of the parent
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
        // You can always set published to false
        if ($value == false) {
            return true;
        }

        // You can only set published to true if more then 4 Published questions are present
        if ($this->map->questions->where('published', true)->count() >= 4) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cannot be :attribute, the map requires to have a minimum of 4 questions published';
    }
}
