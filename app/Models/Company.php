<?php

namespace App\Models;

use App\Models\Inventory\Otheruser;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Models\Rbac\Role;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Company');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
//        $activity->company_id = null;
        $activity->request_data = json_encode(request()->all());
    }

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'code',
        'web_url',
        'address1',
        'address2',
        'is_active',
        'contact_name',
        'contact_phone',
    ];

    protected $casts = [
        'contact_phone' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function loggable()
    {
        return $this->user()->where('loggable', true);
    }

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function otherusers()
    {
        return $this->hasMany(Otheruser::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}
