<?php

namespace App\Models\Inventory\Warehouse;

use App\Models\Expense\Expense;
use App\Models\Inventory\Partnumber;
use App\Models\Inventory\Product;
use App\Models\Inventory\Traits\LocationTraits;
use App\Pivot\WarehousePivot;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends Model
{
    use LogsActivity, LocationTraits, HasFactory;

    protected static $logAttributes = ['*'];

    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'company_id',
        'is_default',
        'default_location',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'products.pivot.location' => 'json',
    ];

    protected $appends = ['location_string'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Warehouse');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    /**
     * @return mixed
     */
    public function scopeFromCompany($query)
    {
        return $query->where('company_id', compid());
    }

    /**
     * use Hints: useful get to the last from
     * dropdown, this contain no timestamps
     *
     * @return mixed
     */
    public function scopeWarehouseList($query)
    {
        return $query->select('id', 'name', 'code', 'is_default')
            ->whereCompanyId(request()->input('company_id'))
            ->orderBy('name', 'ASC');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'unit_id', 'weight', 'location', 'pw_id')
            ->using(WarehousePivot::class)
            ->withTimestamps();
    }

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function partnumbers()
    {
        return $this->hasMany(Partnumber::class);
    }

    public function isles()
    {
        return $this->hasMany(WarehouseIsle::class);
    }

    public function storage()
    {
        return $this->hasManyThrough(
            WarehouseIsle::class,
            WarehouseRack::class,
            // WarehouseBin::class,
            // 'warhouse_rack_id',
            'warehouse_isle_id',
            'warehouse_id',
            'id',
            'id',
        // 'id'
        );
    }

    public function pickings()
    {
        return $this->hasMany(WarehousePicking::class);
    }
}
