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

class Purchasequotation extends Model
{
    use LogsActivity;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Purchase Quotation');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $table = 'purchasequotations';

    protected $fillable = [
        'note',
        'total',
        'company_id',
        'created_by',
        'supplier_id',
        'purchase_id',
        'total_weight',
        'quotation_no',
        'shipping_cost',
        'quotation_date',
        'overall_discount',
    ];

    protected $with = ['supplier'];

    protected $dates = ['quotation_date'];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'discount', 'subtotal', 'unit_id', 'weight', 'weight_total')
            ->withTimestamps();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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

    public function scopeFromCompany($query)
    {
        return $query->where('purchasequotations.company_id', compid());
    }
    //
}
