<?php

namespace App\Models\Expense;

use App\Models\Account;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Warehouse\Warehouse;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    use LogsActivity;

    public $incrementing = true;

    protected $guarded = ['id'];

    protected $dates = ['expense_date'];

    public static function classPath(): string
    {
        return (new ReflectionClass(self::class))->getName();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Expense');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function userable()
    {
        return $this->morphTo(__FUNCTION__, 'userable_type', 'userable_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeFromCompany($query)
    {
        return $query->where('expenses.company_id', compid());
    }

    public function scopeSumOfAccount($query)
    {
        return $query->join('accounts', 'accounts.id', '=', 'account_id')
            ->select('accounts.name as account_name', DB::raw('sum(amount) as total_amount'))
            ->groupBy('expenses.account_id');
    }
}
