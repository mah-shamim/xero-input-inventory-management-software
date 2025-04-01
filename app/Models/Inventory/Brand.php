<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    protected $table = 'brands';

    protected $fillable = ['name', 'description', 'company_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Brand');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    /**
     * use Hints: useful get to the last from
     * dropdown, this contain no timestamps
     *
     * @return mixed
     */
    public function scopeBrandList($query)
    {
        return $query->select('id', 'name')->whereCompanyId(request()->input('company_id'))->orderBy('name', 'ASC');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');

    }
}
