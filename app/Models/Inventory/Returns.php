<?php

namespace App\Models\Inventory;

use App\Models\Bank\Transaction;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Returns extends Model
{
    use LogsActivity;

    /**
     * @var bool
     */
    public $incrementing = true;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Returns');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return mixed
     */
    public function scopeFromCompany($query)
    {
        return $query->where('company_id', compid());
    }

    /**
     * @return mixed
     */
    public function scopeSaleOnly($query)
    {
        return $query->where('returnable_type', Sale::class);
    }

    /**
     * @return mixed
     */
    public function scopePurchaseOnly($query)
    {
        return $query->where('returnable_type', Purchase::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function returnable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
