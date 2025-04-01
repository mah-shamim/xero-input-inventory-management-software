<?php

namespace App\Models\Bank;

use App\Models\Account;
use App\Models\Income\Income;
use App\Models\Inventory\BuildEvent;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Returns;
use App\Models\Inventory\Sale;
use App\Models\Payroll\Salary;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Transaction');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    protected $fillable = [
        'note',
        'date',
        'type',
        'amount',
        'ref_no',
        'bank_id',
        'refer_id',
        'company_id',
        'account_id',
        'transfer_id',
        'userable_id',
        'cheque_number',
        'userable_model',
        'payment_method',
        'transaction_number',
    ];

    protected $dates = ['date'];

    protected $appends = ['date_without_time', 'created_at_without_time'];

    public function scopeType($query, $arg)
    {
        return $query->where('type', $arg);
    }

    public function scopeCash($query)
    {
        return $query->where('payment_method', 'cash');
    }

    public function scopeCheque($query)
    {
        return $query->where('payment_method', 'cheque');
    }

    public function scopeCreditCard($query)
    {
        return $query->where('payment_method', 'credit_card');
    }

    public function scopeTypeDebit($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeTypeCredit($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeFromCompany($query)
    {
        return $query->where('transactions.company_id', compid());
    }

    public function scopeTransactionHasPurchaseForASupplier($query, $supplier_id)
    {
        return $query->whereHas('payment', function ($payments) use ($supplier_id) {
            $payments->where('paymentable_type', Purchase::classPath())
                ->whereHasMorph('paymentable', [Purchase::class], function ($paymentable) use ($supplier_id) {
                    $paymentable->where('supplier_id', $supplier_id);
                })
                ->orWhere('userable_model', "App\Inventory\Supplier");
        });
    }

    public function scopeTransactionHasSaleForACustomer($query, $customer_id)
    {
        $query->whereHas('payment', function ($payments) use ($customer_id) {
            $payments->where('paymentable_type', "App\Models\Inventory\Sale")
                ->whereHasMorph('paymentable', [Sale::class], function ($paymentable) use ($customer_id) {
                    $paymentable->where('customer_id', $customer_id);
                });
        })
            ->orWhere('transactions.userable_model', "App\Inventory\Customer");
    }

    public function getDateWithoutTimeAttribute()
    {
        return date(request()->input('user_date_format_php'), strtotime($this->date));
    }

    public function getCreatedAtWithoutTimeAttribute()
    {
        return date(request()->input('user_date_format_php'), strtotime($this->created_at));
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function returns()
    {
        return $this->hasMany(Returns::class);
    }

    public function transfer()
    {
        return $this->belongsTo(Bank::class);
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }

    public function refer()
    {
        return $this->belongsTo(self::class, 'id');
    }

    public function userable()
    {
        return $this->morphTo(__FUNCTION__, 'userable_model', 'userable_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function salary()
    {
        return $this->hasOne(Salary::class);
    }

    public function buildEvent()
    {
        return $this->belongsTo(BuildEvent::class);
    }
}
