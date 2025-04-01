<?php

namespace App\Rules\inventory;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SalesItemRule implements Rule
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
        foreach ($value as $val) {
            $validator = Validator::make($val, [
                'product_id' => 'required',
                'unit_price' => 'required',
                'price' => 'required',
                'warehouse' => 'required',
                'unit' => 'required',
                'quantity' => 'required',
            ]);

            if ($validator->fails()) {
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
        return 'Items data should not be Empty!!';
    }
}
