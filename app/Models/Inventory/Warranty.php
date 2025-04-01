<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Warranty extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Warranty');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $guarded = ['id'];

    protected $dates = ['warranty_date'];

    protected $appends = [
        'warranty_date_formatted',
    ];

    public function getWarrantyDateFormattedAttribute(): string
    {
        return date(request()->input('user_date_format_php'), strtotime($this->warranty_date));
    }

    public function scopeWarrantyList($query)
    {
        return $query->select('id', 'name')->whereCompanyId(request()->input('company_id'))->orderBy('name', 'ASC');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }
}
