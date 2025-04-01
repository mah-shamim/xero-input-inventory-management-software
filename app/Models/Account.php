<?php

namespace App\Models;

use App\Models\Bank\Transaction;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model
{
    use LogsActivity;

    /**
     * @var bool
     */
    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Account');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'parent_id',
        'company_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function scopeMain($query)
    {
        return $query->where('parent_id', '<', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sub()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->sub()->with('children');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transactions()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * @return mixed
     */
    public function scopeFromCompany($query)
    {
        return $query->where('accounts.company_id', compid());
    }

    public function scopeAccountByGroupText($query)
    {
        return $query->leftjoin('accounts as a', 'a.id', 'accounts.parent_id')
            ->where('accounts.company_id', compid())
            ->where('accounts.parent_id', '>', 0);
    }

    public function scopeTypeLedger($query)
    {
        return $query->where('type', 'ledger');
    }

    public function scopeTypeLedgerUnique($query)
    {
        return $query->where('type', 'ledger')->groupBy('name');
    }

    public function scopeOnlyExpense($query)
    {
        $expense_id = self::where('type', 'group')->where('name', 'Expense')->fromCompany()->first()->id;

        return $query->where('parent_id', $expense_id);
    }

    public function scopeOnlyIncome($query)
    {
        $income_id = self::where('type', 'group')->where('name', 'Income')->fromCompany()->first()->id;

        return $query->where('parent_id', $income_id);
    }
}
