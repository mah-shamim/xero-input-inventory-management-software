<?php

namespace App\Models\Inventory;

use App\Bank\Transaction;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BuildEvent extends Model
{
    use LogsActivity;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Build');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $table = 'buildevents';

    protected $fillable = [
        'mcpu',
        'unit_id',
        'quantity',
        'total_cost',
        'company_id',
        'warehouse_id',
        'total_weight',
        'expense_total',
        'other_expenses',
        'transaction_id',
        'material_detail',
        'location',
    ];

    protected $casts = [
        'other_expenses' => 'json',
        'material_detail' => 'json',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function scopeFromCompany($query)
    {
        return $query->where('buildevents.company_id', compid());
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
