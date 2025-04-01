<?php

namespace App\Models\Inventory;

use App\Models\Company;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use function App\Inventory\compid;

class Salequotation extends Model
{
    use LogsActivity;

    public $incrementing = true;

    protected $table = 'salequotations';

    protected $with = ['customer'];

    protected $fillable = [
        'note',
        'total',
        'sale_id',
        'company_id',
        'created_by',
        'customer_id',
        'quotation_no',
        'total_weight',
        'shipping_cost',
        'quotation_date',
        'overall_discount',
    ];

    protected $dates = ['quotation_date'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Sale Quotation');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'discount', 'subtotal', 'unit_id', 'weight', 'weight_total')
//            ->withPivot('quantity', 'price', 'discount', 'subtotal', 'unit_id', 'warehouse_id', 'others')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function scopeFromCompany($query)
    {
        return $query->where('salequotations.company_id', compid());
    }
}
