<?php

namespace App\Rules\inventory;

use Illuminate\Contracts\Validation\Rule;

class WarehouseIsleRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    // "isles.0.name" // app/Rules/Inventory/WarehouseIsleRule.php:28
    // "2" // app/Rules/Inventory/WarehouseIsleRule.php:28

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // dd($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
