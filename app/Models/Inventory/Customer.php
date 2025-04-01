<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionClass;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    public static function classPath(): string
    {
        return (new ReflectionClass(self::class))->getName();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Customer');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'details',
        'address',
        'loggable',
        'is_default',
        'company_id',
    ];

    protected $casts = [
        'loggable' => 'boolean',
        'is_default' => 'boolean',
    ];

    use SoftDeletes;

    /**
     * use Hints: useful get to the last from
     * dropdown, this contain no timestamps
     *
     * @param $query
     * @return mixed
     */
    protected $appends = ['type', 'name_id_type', 'name_id'];

    public function getTypeAttribute(): string
    {
        return 'customer';
    }

    public function getNameIdTypeAttribute(): string
    {
        return $this->name.'-'.$this->code.'-'.'customer';
    }

    public function getNameIdAttribute(): string
    {
        return $this->name.'-'.$this->code;
    }

    public function scopeCustomerList($query)
    {
        return $query->select('id', 'name', 'code', 'phone', 'created_at', 'address', 'is_default')
            ->whereCompanyId(request()->input('company_id'))
            ->orderBy('id', 'ASC');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function scopeFromCompany($query)
    {
        return $query->where('customers.company_id', compid());
    }
}
