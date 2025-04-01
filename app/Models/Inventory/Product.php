<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Traits\LocationTraits;
use App\Models\Inventory\Traits\ProductAveragePrice;
use App\Models\Inventory\Traits\QuantityTraits;
use App\Models\Inventory\Traits\SaleReturn;
use App\Models\Inventory\Traits\WeightTraits;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Pivot\WarehousePivot;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory,
        LogsActivity,
        ProductAveragePrice,
        SoftDeletes,
        QuantityTraits,
        LocationTraits,
        WeightTraits;

    public $incrementing = true;

    //
    protected $table = 'products';

    protected $fillable = [
        'bom',
        'name',
        'code',
        'slug',
        'note',
        'price',
        'brand_id',
        'brand_id',
        'company_id',
        'only_module',
        'base_unit_id',
        'buying_price',
        'manufacture_part_number',
        'actual_quantity_purchased',
        'average_purchase_price',
        'total_amount_purchased',
        'actual_quantity_sold',
        'average_sale_price',
        'total_amount_sold',
    ];

    protected $casts = [
        'bom' => 'json',
        'warehouses.pivot.location' => 'json', //need to check that its working
        'location' => 'json',
    ];

    protected $appends = ['purchased_location', 'sale_location'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Product');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    /**
     * use Hints: useful get to the last from
     * dropdown, this contain no timestamps
     *
     * @param  null  $value
     * @return mixed
     */
    public function scopeProductList($query, $value = null)
    {
        if (! $value) {
            $data = $query->select(
                'id',
                'company_id',
                'name',
                'code',
                'bom',
                'price',
                'base_unit_id',
                'buying_price',
                'manufacture_part_number',
                DB::raw('count(product_unit.product_id) as unit_count')
            )
                ->leftJoin('product_unit', 'products.id', '=', 'product_unit.product_id');
        } else {
            $data = $query->select(
                'id',
                'company_id',
                'name',
                'bom',
                'code',
                'price',
                'base_unit_id',
                'buying_price',
                'manufacture_part_number',
                DB::raw('count(product_unit.product_id) as unit_count')
            )
                ->leftJoin('product_unit', 'products.id', '=', 'product_unit.product_id')
                ->where('name', 'LIKE', '%'.$value.'%')
                ->orWhere('code', 'LIKE', '%'.$value.'%');
        }
        $data->where(function ($data) {
            $data->where('products.company_id', '=', request()->input('company_id'));
        })->groupBy('products.id', 'product_unit.product_id')
            ->orderBy('name', 'ASC')
            ->take(10);

        return $data;
    }

    public function scopeProductWithMapping($query)
    {
        $productList = $this->scopeProductList($query)->get();
        foreach ($productList as $product) {
            (count($product->units) > 0) ? $product->is_mapped = true : $product->is_mapped = false;
        }

        return $productList;
    }

    public function warehouses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class)
            ->withPivot('quantity', 'unit_id', 'weight', 'location', 'pw_id')
            ->using(WarehousePivot::class)
            ->withTimestamps();
    }

    public function quantity_not_null()
    {
        return $this->belongsToMany(Warehouse::class)
            ->withPivot('quantity', 'unit_id', 'weight')
            ->wherePivot('quantity', '>', 0);
    }

    public function warehouseIsNull()
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse', 'product_id', 'warehouse_id')
            ->withPivot('quantity', 'weight')
            ->wherePivot(null)
            ->withTimestamps();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class)
            ->withPivot('parent_id', 'weight')
            ->withTimestamps();
    }

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function purchases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Purchase::class)
            ->withPivot(
                'quantity',
                'price',
                'discount',
                'subtotal',
                'unit_id',
                'warehouse_id',
                'purchase_quantity',
                'weight',
                'weight_total',
                'pp_id'
            )->withTimestamps();
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class)->withPivot('subtotal', 'quantity');
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot('subtotal', 'quantity');
    }

    public function sale_return(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SaleReturn::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public function bill_of_materials(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BillOfMaterial::class, 'main_id');
    }

    public function usedInBom(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BillOfMaterial::class, 'product_id');
    }

    public function buildevents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BuildEvent::class);
    }

    public function partnumbers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Partnumber::class);
    }

    public function partnumbers_sale(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->partnumbers()->whereNull('sale_id');
    }

    public function scopeFromCompany($query)
    {
        return $query->where('products.company_id', compid());
    }
}
