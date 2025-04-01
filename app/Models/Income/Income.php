<?php

namespace App\Models\Income;

use App\Models\Inventory\Category;
use App\Models\Inventory\Warehouse\Warehouse;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class Income extends Model
{
    protected $guarded = ['id'];

    public static function classPath(): string
    {
        return (new ReflectionClass(self::class))->getName();
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);

    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
