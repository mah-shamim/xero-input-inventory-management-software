<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bank extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Bank');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $fillable = [
        'name',
        'type',
        'branch',
        'address',
        'account_no',
        'company_id',
    ];

    public function scopeFromCompany($query)
    {
        return $query->where('company_id', compid());
    }

    public function scopeTypeCash($query)
    {
        return $query->where('type', 'cr');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
