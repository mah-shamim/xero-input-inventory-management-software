<?php

use App\Models\Inventory\UnitConversion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @param int $status
 * @return string
 */
function getPurchaseRef($status = 0, $pid = null)
{
    $date = new Carbon();

    return $date->year . '-' . $date->day . '-' . $date->month . '-' . str_replace(':', '', $date->format('H:i:s')) . '-' . $status . '-' . $pid;

}

function generateRef($status = 0, $pid = null, $uniqueNumber = null): string
{
    $date = new Carbon();

    return $uniqueNumber
        ? $date->year . '-' . $date->day . '-' . $date->month . '-' . str_replace(':', '', $date->format('H:i:s')) . '-' . $status . '-' . $pid . $uniqueNumber
        : $date->year . '-' . $date->day . '-' . $date->month . '-' . str_replace(':', '', $date->format('H:i:s')) . '-' . $status . '-' . $pid;
}

function routeName(): string
{
    return \Illuminate\Support\Facades\Route::getCurrentRoute()->getName();
}

function itemPerPage(): int
{
    $pages = request()->get('itemsPerPage');

    return $pages ? $pages : getResultPerPage();
}

function getResultPerPage(): int
{
    return 15;
}

function moduleName($method): string
{
    return explode('.', $method)[0];
}

function subTotalCalculation($item)
{
    $total = $item['quantity'] * $item['unit_price'];
    $discount = isset($item['discount']) ? $item['discount'] : 0;
    $adjustment = isset($item['adjustment']) ? $item['adjustment'] : 0;
    //    $discount =isset($item['discount'])? $total * $item['discount'] / 100 : 0 ;
    return $total + $adjustment - $discount;
}

function convertUnit($fromUnitId, $toUnitId, $givenQty, $companyId)
{
    $array = [];
    $conversion = UnitConversion::where('from_unit_id', $fromUnitId)->where('to_unit_id', $toUnitId)->whereCompanyId($companyId)->first();
    if ($conversion == null) {
        $array['message'] = 'Unit mapping doest exits';

        return $array;
    }
    $array['quantity'] = $givenQty * $conversion->conversion_factor;

    return $array;
}

function quantityStrConversion($model)
{
    foreach ($model->products as $product) {
        $product->quantityStr = $product->getQuantityWithConversions($product);
    }
}

function customer_default()
{
    foreach (\App\Models\Inventory\Customer::where('company_id', request()->input('company_id'))->get() as $custom) {
        $custom->update(['is_default' => false]);
    }
}

function toggle_default($model)
{
    foreach ($model::where('company_id', request()->input('company_id'))->get() as $custom) {
        $custom->update(['is_default' => false]);
    }
}



function modelsWithSoftDeletes()
{
    return [
        'App\Models\Inventory\Supplier',
        'App\Models\Inventory\Customer',
        'App\Models\Inventory\Product',
        'App\Models\User',
        'App\Models\Company',
    ];
}

function modelsWithoutSoftDeletes()
{
    return [
        'App\Models\Inventory\Category',
        'App\Models\Payroll\Employee',
        'App\Models\Payroll\Salary',
        'App\Models\Income\Income',
        'App\Models\Expense\Expense',
        'App\Models\Setting',
        'App\Models\Inventory\Brand',
        'App\Models\Inventory\Warehouse',
        'App\Models\Inventory\Product',
        'App\Models\Inventory\Warranty',
        'App\Models\Inventory\Unit',
        'App\Models\Inventory\Sale',
        'App\Models\Inventory\Purchase',
        'App\Models\Inventory\ProductDamage',
        'App\Models\Inventory\Returns',
    ];
}

function google_recaptcha()
{
    $recaptcha = Cache::rememberForever('google_recaptcha', function () {
        return AdminSetting::where('entity', 'google_recaptcha')->where('is_active', 1)->first();
    });

    $values = [];
    if ($recaptcha) {
        $values['secret_key'] = $recaptcha->attribute['secret_key'];
        $values['site_key'] = $recaptcha->attribute['site_key'];
        $values['type'] = $recaptcha->attribute['type'];
        if ($values['type'] === 'v3') {
            $values['score'] = $recaptcha->attribute['score'];
        }

        return $values;
    } else {
        return false;
    }
}

function payment_method($val)
{
    switch ($val) {
        case 3:
            return 'cheque';
        case 2:
            return 'credit card';
        default:
            return 'cash';
    }
}

function payment_method_id($val)
{
    switch (trim($val)) {
        case 'cheque':
            return 3;
        case 'credit card':
            return 2;
        default:
            return 1;
    }
}


function accountSeedData($company_id)
{
    return [
        [
            'name' => 'Asset',
            'parent_id' => 0,
            'type' => 'group',
            'company_id' => $company_id,
        ],
        [
            'name' => 'Liability',
            'parent_id' => 0,
            'type' => 'group',
            'company_id' => $company_id,
        ],
        [
            'name' => 'Equity',
            'parent_id' => 0,
            'type' => 'group',
            'company_id' => $company_id,
        ],
        [
            'name' => 'Income',
            'parent_id' => 0,
            'type' => 'group',
            'company_id' => $company_id,
        ],
        [
            'name' => 'Expense',
            'parent_id' => 0,
            'type' => 'group',
            'company_id' => $company_id,
        ],
    ];
}

function compid()
{
    return request()->input('company_id');
}

function getModelName(Request $request): string
{
    return str_contains($request->url(), 'sale')
        ? 'sale'
        : 'purchase';
}

function batchSizeSetting(): int
{
    return 2000;
}

function payment_method_name_arr(): array
{
    return ['cash', 'cheque', 'credit card'];
}

function dateLowToHigh($dates): array
{
    $first_date = strtotime($dates[0]);
    $second_date = strtotime($dates[1]);

    return $first_date < $second_date
        ? [$dates[0], $dates[1]]
        : [$dates[1], $dates[0]];
}

function dateLowToHighWithTime($dates): array
{
    $startDate = dateLowToHigh($dates)[0];
    $endDate = dateLowToHigh($dates)[1];
    $explodeArrStart = explode('-', $startDate);
    $explodeArrEnd = explode('-', $endDate);
    $startDate = Carbon::createFromDate($explodeArrStart[0], $explodeArrStart[1], $explodeArrStart[2]);
    $endDate = Carbon::createFromDate($explodeArrEnd[0], $explodeArrEnd[1], $explodeArrEnd[2]);

    return [
        $startDate->copy()->startOfDay(),
        $endDate->copy()->endOfDay(),
    ];
}

function settings_seed(): array
{
    return [
        'currency' => fake()->currencyCode,
        "site_name" => fake()->name,
        "default_email" => fake()->email,
        "default_date_format"=>"YYYY-MM-DD",
        'design' => [
            "topbar_color" => '#E0E0E0FF',
            "sidebar_color" => '#F5F5F5FF'
        ],
        "inventory" => [
            "account_method" => "avg",
            "profit_percent" => false,
            "quantity_label" => "Quantity",
            "shipping_cost_label" => "Shipping Cost",
            "purchase" => [
                "default_payment_mood" => 1
            ],
            "sale" => [
                "default_payment_mood" => 1,
                "stock_out_sale" => true,
            ],
        ],
    ];
}

