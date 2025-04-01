<?php

namespace App\Models\Inventory;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use function App\Inventory\compid;

class Order extends Model
{
    use LogsActivity;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Sale Order');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $fillable = [
        'ref',
        'note',
        'table',
        'total',
        'status',
        'biller',
        'user_id',
        'sale_id',
        'order_no',
        'company_id',
        'order_date',
        'is_canceled',
        'total_weight',
        'salesman_code',
        'shipping_cost',
        'payment_status',
        'delivered_date',
        'overall_discount',
        'customer_order_no',
        'expected_shipping_date',
    ];

    protected $dates = [
        'order_date',
        'expected_shipping_date',
    ];

    protected $appends = [
        'order_date_formatted',
    ];

    public function getOrderDateFormattedAttribute()
    {
        return date(request()->input('user_date_format_php'), strtotime($this->order_date));
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'discount', 'subtotal', 'unit_id', 'warehouse_id', 'weight', 'weight_total')
            ->withTimestamps();
    }

    public function order_payment()
    {
        return $this->belongsToMany(OrderPayment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function orderpurchases()
    {
        return $this->hasMany(Orderpurchase::class);
    }

    public function scopeFromCompany($query)
    {
        return $query->where('orders.company_id', compid());
    }
}
