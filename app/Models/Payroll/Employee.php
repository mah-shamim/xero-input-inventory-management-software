<?php

namespace App\Models\Payroll;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Employee');
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

    protected $guarded = ['id'];

    protected $dates = [
        'birth',
        'join_date',
    ];

    protected $appends = ['code', 'type', 'name_id_type'];

    public function getTypeAttribute()
    {
        return 'employee';
    }

    public function getCodeAttribute()
    {
        return $this->employee_id;
    }

    public function getNameIdTypeAttribute()
    {
        return $this->name.'-'.$this->employee_id.'-'.'employee';
    }

    public function scopeEmployeeList($query)
    {
        return $query->select('id', 'name', 'salary')
            ->whereCompanyId(request()->input('company_id'))
            ->orderBy('name', 'ASC');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function scopeFromCompany($query)
    {
        return $query->where('employees.company_id', compid());
    }
}
