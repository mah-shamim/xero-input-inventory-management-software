<?php

namespace App\Models\Inventory;

use App\Models\Bank\Transaction;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionClass;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;
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
            ->useLogName('Supplier');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $casts = [
        'loggable' => 'boolean',
    ];

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'company',
        'loggable',
        'company_id',
    ];

    protected $appends = ['type', 'name_id_type', 'companyid', 'name_id'];

    public function getTypeAttribute(): string
    {
        return 'supplier';
    }

    public function getNameIdTypeAttribute(): string
    {
        return $this->name.'-'.$this->code.'-'.'supplier';
    }

    public function getNameIdAttribute(): string
    {
        return $this->name.'-'.$this->code;
    }

    public function getCompanyidAttribute(): string
    {
        return $this->company.'-'.$this->code;
    }

    public function scopeSupplierList($query)
    {
        return $query->select('id', 'company', 'name', 'code', 'phone')
            ->whereCompanyId(request()->input('company_id'))
            ->orderBy('company', 'ASC');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Orderpurchase::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function scopeFromCompany($query)
    {
        return $query->where('suppliers.company_id', compid());
    }

}
