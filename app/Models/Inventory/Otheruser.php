<?php

namespace App\Models\Inventory;

use App\Models\Company;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use function App\Inventory\compid;

class Otheruser extends Model
{
    use LogsActivity;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Other user');
    }

    public static function classPath(): string
    {
        return (new ReflectionClass(self::class))->getName();
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $table = 'otherusers';

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'details',
        'address',
        'company_id',
    ];

    protected $appends = ['type', 'name_id_type'];

    public function getTypeAttribute()
    {
        return 'other';
    }

    public function getNameIdTypeAttribute()
    {
        return $this->name.'-'.$this->code.'-'.'other';
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeFromCompany($query)
    {
        return $query->where('otherusers.company_id', compid());
    }
}
