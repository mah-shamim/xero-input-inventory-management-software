<?php

namespace App\Rules\inventory;

use App\Models\Inventory\Warehouse\Warehouse;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LocationRules implements Rule
{
    protected string $message = 'The Location is not Exist';

    /**
     * @var null
     */
    protected $product;

    /**
     * Create a new rule instance.
     *
     * @param  null  $product
     */
    public function __construct($product = null)
    {
        //
        $this->product = $product;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value) {
            $arr = explode('-', $value);
            $model = explode('.', routeName());
            if ($this->product && count($this->product) && $model[0] === 'sales') {
                $product_id = $this->product[0];
                $pw = DB::table('product_warehouse')
                    ->where('product_id', $product_id)
                    ->where('warehouse_id', $arr[1])
                    ->whereNotNull('location->'.$value)
                    ->first();
                if ($pw) {
                    //                    $location = json_decode($pw->location, true);
                    //                    if(!$location[$value]['quantity']>=$this->quantity){
                    //                        $this->message = 'There is no enough quantity exist in the location';
                    //                       return false;
                    //                    }
                    return true;
                } else {
                    $this->message = 'Product is not exist in the location';

                    return false;
                }
            }
            $warehouse = Warehouse::with('isles.racks.bins', 'pickings')
                ->where('id', $arr[1])->where('company_id', $arr[0])
                ->first();

            return in_array($value, $warehouse['location_string']);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
