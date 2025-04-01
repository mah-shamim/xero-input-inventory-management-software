<?php

namespace App\Models\Bank;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{
    protected $fillable = ['amount', 'interest', 'transaction_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanwithdraw()
    {
        return $this->belongsTo(Loanwithdraw::class);
    }
}
