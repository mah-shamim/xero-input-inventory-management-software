<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if($this->input('company_detail')==true){
            return [
                'name'=>'required',
                'address1'=>'required',
                'contact_name'=>'required',
                'contact_phone.*'=>['required', new PhoneNumber],
                'code'=>'required|unique:companies,code,'.$this->input('company_id'),
            ];
        }else{
            return [
                'site_name' => 'required',
                'currency' => 'required',
                'default_email' => 'required|email',

                'design.topbar_color' => 'required',
                'design.sidebar_color' => 'required',

                'inventory.account_method' => 'required|in:avg,fifo',
                'inventory.profit_percent' => 'required|boolean',
                'inventory.quantity_label' => 'required',
                'inventory.shipping_cost_label' => 'required',

                'inventory.sale.stock_out_sale' => 'required|boolean',
                'inventory.sale.default_payment_mood' => 'required|integer',

                'inventory.purchase.default_payment_mood' => 'required|in:1,2,3',
            ];
        }
    }

    public function messages(): array
    {
        if(!$this->input('company_detail') || $this->input('company_detail')!=true){
            return [
                'design.topbar_color.required'=>'The Topbar Color field is required',
                'design.sidebar_color.required'=>'The Sidebar Color field is required',
                'inventory.sale.stock_out_sale.required' => 'The Stock Out Sale field is required',
                'inventory.sale.default_payment_mood.required' => 'required|integer',
                'inventory.purchase.default_payment_mood.required' => 'The Default Payment Method field is required',
                'inventory.account_method.required' => 'The Account Method field is required',
                'inventory.profit_percent.required' => 'The Profit Percent field is required',
                'inventory.quantity_label.required' => 'The Quantity Label field is required',
                'inventory.shipping_cost_label.required' => 'The Shipping Cost Label field is required',
            ];
        }else{
            return [];
        }
    }
}
