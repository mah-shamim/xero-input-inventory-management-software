<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SettingsRules implements Rule
{
    protected $allowed = [
        'currency',
        'inventory',
        'site_name',
        'default_email',
    ];

    protected $inventory = [
        'stock_out_sale',
        'account_method',
        'shipping_cost_label',
        'quantity_label',
        'table_order',
    ];

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

        foreach ($value as $name => $v) {
            if (in_array($name, $this->allowed) && $v != null) {
                if ($name == 'inventory') {
                    foreach ($v as $item => $j) {
                        if (! in_array($item, $this->inventory) && ! $v != null) {
                            $this->return_false();
                        }
                    }
                }
            } else {
                return $this->return_false();
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
        return 'The validation error message.';
    }

    /**
     * @return bool
     */
    protected function return_false()
    {
        return false;
    }
}
