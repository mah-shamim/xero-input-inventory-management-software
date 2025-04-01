<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Salestable extends Model
{
    protected $table = 'saletables';

    protected $fillable = [
        'name',
        'company_id',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'table');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
