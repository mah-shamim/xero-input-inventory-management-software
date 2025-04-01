<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    protected $dates = [
        'purchase_date'
    ];

    protected $appends = [
        'purchase_date_formatted',
    ];

    protected $casts = [
        'sum_fields' => 'array',
    ];

    public static function classPath(): string
    {
        return (new ReflectionClass(self::class))->getName();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Purchase');
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
        'total',
        'status',
        'bill_no',
        'sum_fields',
        'company_id',
        'total_weight',
        'shipping_cost',
        'purchase_date',
        'payment_status',
        'overall_discount',
    ];

    public function getPurchaseDateFormattedAttribute(): string
    {
        return date(request()->input('user_date_format_php'), strtotime($this->purchase_date));
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(
                'purchase_quantity',
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
                'pp_id'
            )->withTimestamps();
    }

    public function scopeFromCompany($query)
    {
        return $query->where('purchases.company_id', compid());
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'product_purchase')
            ->withPivot('quantity', 'price', 'discount', 'subtotal', 'product_id', 'warehouse_id', 'weight', 'weight_total')
            ->withTimestamps();
    }

    public function returns()
    {
        return $this->morphMany(Returns::class, 'returnable');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function partnumbers()
    {
        return $this->hasMany(Partnumber::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(request()->input('user_date_format_php') . ' H:i:s');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
