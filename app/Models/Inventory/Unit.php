<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Unit');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $table = 'units';

    protected $guarded = ['id'];

    protected $casts=[
        'is_primary'=>'boolean'
    ];

    public function scopeUnitList($query)
    {
        return $query->select('id', 'key')->with('conversions')->has('conversions')->whereCompanyId(request()->input('company_id'))->orderBy('key', 'ASC');
    }

    public function conversions()
    {
        return $this->hasMany(UnitConversion::class, 'from_unit_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('parent_id')
            ->withTimestamps();
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
