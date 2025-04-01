<?php

namespace App\Models\Bank;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Loanwithdraw extends Model
{
    protected $guarded = ['id'];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanTransactions()
    {
        return $this->hasMany(LoanTransaction::class);
    }
}
