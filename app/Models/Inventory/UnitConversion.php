<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UnitConversion extends Model
{
    use LogsActivity;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Unit Conversion');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $guarded = ['id'];

    public function from_unit()
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }

    public function to_unit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');

    }

    public function scopeFromCompany($query)
    {
        return $query->where('company_id', compid());
    }
}
