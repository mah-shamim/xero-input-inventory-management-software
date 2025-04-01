<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Traits\SaleReturn;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Sale extends Model
{
    use LogsActivity;

    public $incrementing = true;

    protected $fillable = [
        'ref',
        'note',
        'table',
        'total',
        'biller',
        'status',
        'sales_date',
        'company_id',
        'customer_id',
        'previous_due',
        'total_weight',
        'salesman_code',
        'shipping_cost',
        'payment_status',
        'overall_discount',
    ];

    protected $with = ['customer'];

    protected $dates = ['sales_date'];

    protected $appends = ['sales_date_formatted'];

    public static function classPath(): string
    {
        return (new ReflectionClass(self::class))->getName();
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Sale');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    public function getSalesDateFormattedAttribute(): string
    {
        return date(request()->input('user_date_format_php'), strtotime($this->sales_date));
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(
                'quantity',
                'price',
                'discount',
                'subtotal',
                'unit_id',
                'warehouse_id',
                'weight',
                'weight_total',
                'adjustment',
                'locatable_type',
                'locatable_id',
                'location_value',
                'ps_id'
            )
//            ->withPivot('quantity', 'price', 'discount', 'subtotal', 'unit_id', 'warehouse_id', 'others')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeFromCompany($query)
    {
        return $query->where('sales.company_id', compid());
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function sale_return()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function returns()
    {
        return $this->morphMany(Returns::class, 'returnable');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function partnumbers()
    {
        return $this->hasMany(Partnumber::class);
    }

    public function scopeIndexQuery($query)
    {
        return $query->select('sales.*', DB::raw("CONCAT(customers.name, '-' ,customers.code) as customer_name_id"))
            ->leftJoin('payments as p', function ($payment) {
                $payment->on('p.paymentable_id', '=', 'sales.id')
                    ->where('p.paymentable_type', '=', "App\Models\Inventory\Sale");
            })
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->withCount('products')
            ->withCount('payments')
            ->selectRaw('coalesce(sum(p.paid),0) as total_paid')
            ->selectRaw('coalesce( coalesce(sales.previous_due, 0) + sales.total - coalesce(sum(p.paid),0)) as due')
            ->groupBy('sales.id');
    }
}
