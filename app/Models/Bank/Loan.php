<?php

namespace App\Models\Bank;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'sanction',
        'interest',
        'interest_expense',
        'sanction_date',
        'sanction_paid',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function Loanwithdraws()
    {
        return $this->hasMany(Loanwithdraw::class);
    }
}
