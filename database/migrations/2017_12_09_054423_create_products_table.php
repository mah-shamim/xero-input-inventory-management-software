<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Inventory\Unit::class, 'base_unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Brand::class)->constrained()->cascadeOnDelete();

            $table->string('name')->index();
            $table->string('code')->index();
            $table->string('slug')->nullable();

            $table->decimal('buying_price', 20, 4)->nullable();
            $table->decimal('price', 20, 4)->nullable();
            $table->decimal('measurement', 4, 4)->nullable();


            $table->string('only_module')->nullable();
            $table->json('bom')->nullable();
            $table->boolean('manufacture_part_number')->default(false);
            $table->text('note')->nullable();

            $table->decimal('actual_quantity_sold', 20, 4)->default(0);
            $table->decimal('actual_quantity_purchased', 20, 4)->default(0);
            $table->decimal('average_sale_price', 20, 4)->default(0);
            $table->decimal('average_purchase_price', 20, 4)->default(0);
            $table->decimal('total_amount_sold', 20, 4)->default(0);
            $table->decimal('total_amount_purchased', 20, 4)->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
//        /*
//         * product doesn't have default warehouse,
//         * so pivot is important. Also product id is unique
//        */
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->bigIncrements('pw_id');
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Warehouse\Warehouse::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->constrained()->cascadeOnDelete();

            $table->decimal('quantity', 36,18)->unsigned();
            $table->decimal('weight', 20, 4)->default(0);

            $table->json('location');

            $table->timestamps();
            $table->unique(['warehouse_id', 'product_id']);
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->bigIncrements('pc_id');
            $table->foreignIdFor(\App\Models\Inventory\Category::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('product_unit', function (Blueprint $table) {
            $table->bigIncrements('pu_id');
            $table->foreignIdFor(\App\Models\Inventory\Unit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Inventory\Product::class)->constrained()->cascadeOnDelete();
            $table->integer('parent_id')->nullable()->default(0);

            $table->decimal('weight', 20, 4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_warehouse');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('product_unit');
        Schema::enableForeignKeyConstraints();
    }
}
