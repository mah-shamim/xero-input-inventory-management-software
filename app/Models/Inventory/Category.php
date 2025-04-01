<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * Class Category
 * @package App\Inventory
 */
class Category extends Model
{
    use LogsActivity, HasFactory;

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Category');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->ip = request()->ip();
        $activity->company_id = request()->input('company_id');
        $activity->request_data = json_encode(request()->all());
    }

    const TYPE_PRODUCT = 'PRODUCT';

    const TYPE_EXPENSE = 'EXPENSE';

    const TYPE_CUSTOMER = 'CUSTOMER';

    const TYPE_PRICE = 'PRICE';

    protected $appends = ['title'];

    protected $table = 'categories';

    protected $fillable = ['name', 'description', 'type', 'parent_id', 'company_id'];

    public function getTitleAttribute()
    {
        return $this['name'];
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }

    public static function getTypeProduct(): string
    {
        return self::TYPE_PRODUCT;
        //        return "PRODUCT";
    }

    public static function getTypeExpense(): string
    {
        return self::TYPE_EXPENSE;
    }

    public static function getTypeCustomer(): string
    {
        return self::TYPE_CUSTOMER;
    }

    public static function getTypePrice(): string
    {
        return self::TYPE_PRICE;
    }

    public function sub_category()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function parent_category()
    {
        return $this->hasMany(self::class, 'id', 'parent_id');
    }

    public function scopeCategoryList($query)
    {
        return $query->select('id', 'name', 'type', 'parent_id')->whereCompanyId(request()->input('company_id'))->orderBy('name', 'ASC');
    }

    public function scopeProductCategoryList($query)
    {
        return $query->select('id', 'name', 'type', 'parent_id')
            ->whereCompanyId(request()->input('company_id'))
            ->whereType('PRODUCT')
            ->orderBy('name', 'ASC');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function scopeCategoryListRaw()
    {
        $categories = Category::whereCompanyId(request()->input('company_id'))->get();
        $dataList = [];
        foreach ($categories as $category) {
            if ($category->parent_id > 0) {
                continue;
            }
            $result = $this->getChildCategories($category, $categories, []);
            $childDataList = $result['childDataList'];
            $childCount = $result['childCount'];
            if ($childCount > 0) {
                $data = array_merge($category->toArray(), ['children' => $childDataList]);
            } else {
                $data = $category->toArray();
            }
            $dataList = array_merge($dataList, [$data]);
        }

        return $dataList;

    }

    public function scopeCategoryWithLabelAndValue($query, $type)
    {
        $categories = Category::whereCompanyId(request()->input('company_id'))->whereType($type)->get();
        $dataList = [];
        foreach ($categories as $category) {
            if ($category->parent_id > 0) {
                continue;
            }
            $result = $this->getChildCategoriesWithLabelAndValue($category, $categories, [], []);
            $childDataList = $result['childDataList'];
            $childCount = $result['childCount'];
            if ($childCount > 0) {
                $data = [
                    [
                        'id' => $category->id,
                        'name' => $category->name,
                        'value' => $category->id,
                        'label' => $category->name,
                        'title' => $category->name,
                        'children' => $childDataList,
                    ],
                ];
            } else {
                $data = [
                    [
                        'id' => $category->id,
                        'name' => $category->name,
                        'value' => $category->id,
                        'label' => $category->name,
                        'title' => $category->name,
                    ],
                ];
            }
            $dataList = array_merge($dataList, $data);
        }

        return $dataList;
    }

    private function getChildCategoriesWithLabelAndValue(Category $category, $categories, $childDataList)
    {

        $childCount = 0;
        for ($i = 0; $i < $categories->count(); $i++) {
            if ($categories[$i]->parent_id == $category->id) {
                $array = $this->getChildCategoriesWithLabelAndValue($categories[$i], $categories, []);
                $childCount = $array['childCount'];
                if ($childCount > 0) {
                    $tempChildDataList = $array['childDataList'];
                    $childData = [
                        [
                            'id' => $categories[$i]->id,
                            'name' => $categories[$i]->name,
                            'value' => $categories[$i]->id,
                            'label' => $categories[$i]->name,
                            'title' => $categories[$i]->name,
                            'children' => $tempChildDataList,
                        ],
                    ];
                    $tempChildDataList = [];
                } else {
                    $childDataList = array_merge($childDataList, $array['childDataList']);
                    $childData = [
                        [
                            'id' => $categories[$i]->id,
                            'name' => $categories[$i]->name,
                            'value' => $categories[$i]->id,
                            'label' => $categories[$i]->name,
                            'title' => $categories[$i]->name,
                        ],
                    ];
                }
                $childDataList = array_merge($childDataList, $childData);
                $childCount++;
            }
        }
        $array['childDataList'] = $childDataList;
        $array['childCount'] = $childCount;

        return $array;
    }

    private function getChildCategories(Category $category, $categories, $childDataList)
    {

        $childCount = 0;
        for ($i = 0; $i < $categories->count(); $i++) {
            if ($categories[$i]->parent_id == $category->id) {
                $array = $this->getChildCategories($categories[$i], $categories, []);
                $childCount = $array['childCount'];
                if ($childCount > 0) {
                    $tempChildDataList = $array['childDataList'];
                    $childData = [array_merge($categories[$i]->toArray(), ['children' => $tempChildDataList])];
                    $tempChildDataList = [];
                } else {
                    $childDataList = array_merge($childDataList, $array['childDataList']);
                    //                    $childData = $categories[$i]->toArray();
                    $childData = [$categories[$i]->toArray()];
                }
                $childDataList = array_merge($childDataList, $childData);
                $childCount++;
            }
        }
        $array['childDataList'] = $childDataList;
        $array['childCount'] = $childCount;

        return $array;
    }
}
