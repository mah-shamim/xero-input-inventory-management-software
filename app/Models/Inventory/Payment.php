<?php

namespace App\Models\Inventory;

use App\Models\Bank\Transaction;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use SoftDeletes, LogsActivity;

    public $incrementing = true;

    protected $fillable = [
        'date',
        'paid',
        'payment_type',
        'transaction_id',
        'transaction_number',
    ];

    protected $dates = ['date'];

    protected $appends = [
        'payment_date_formatted',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Payment');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    public function getPaymentDateFormattedAttribute(): string
    {
        return date(request()->input('user_date_format_php'), strtotime($this->date));
    }

    public function scopeFromCompany($query)
    {
        return $query->whereHas('paymentable', function ($payment) {
            $payment->where('company_id', compid());
        });
    }

    public function scopeIncomeOnly($query)
    {
        return $query->where('paymentable_type', "App\Income\Income");
    }

    public function scopeSaleOnly($query)
    {
        return $query->where('paymentable_type', "App\Models\Inventory\Sale");
    }

    public function scopePurchaseOnly($query)
    {
        return $query->where('paymentable_type', "App\Models\Inventory\Purchase");
    }

    public function scopeExpenseOnly($query)
    {
        return $query->where('paymentable_type', "App\Models\Expense\Expense");
    }

    public function scopeCash($query)
    {
        return $query->where('payment_type', '1');
    }

    public function scopeCheque($query)
    {
        return $query->where('payment_type', '3');
    }

    public function scopeCreditCard($query)
    {
        return $query->where('payment_type', '2');
    }

    public function paymentable()
    {
        return $this->morphTo();
    }

    public function paymentMorph()
    {
        return $this->morphMany(Payment::class, 'paymentable', 'paymentable_type', 'paymentable_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject');
    }
}
