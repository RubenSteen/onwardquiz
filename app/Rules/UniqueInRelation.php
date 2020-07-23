<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class UniqueInRelation implements Rule
{
    public $parent;

    public $column;

    public $relation;

    /**
     * Create a new rule instance.
     *
     * @param  Model  $parent
     * @param  String $column
     * @param  Model  $relation
     * @return void
     */
    public function __construct($parent, String $column, $relation)
    {
        $this->parent = $parent; // Model instance of the parent

        $this->column = $column; // The column

        $this->relation = $relation; // Model instance of the relation
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
        $query = DB::table($this->relation->getTable())
            ->where($this->column, $this->parent->id); // Get all the rows that belong to the parent.

            if ($this->relation->id !== null) {
                $query = $query->where('id', '!=', $this->relation->id); // Makes sure to not include it self.
            }
            
        return $query->where($attribute, $value) // Search for the given value on the attribute.
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
