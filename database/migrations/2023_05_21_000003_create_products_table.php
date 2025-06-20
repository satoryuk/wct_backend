<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('image')->nullable()->default(null);
            $table->text('description')->nullable();
            $table->double('price', 10, 2);
            $table->integer('stock_qty');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->date('expiry_date')->nullable()->default(null);
            $table->smallInteger('status')->default('1');
            $table->timestamps();
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            $table->foreign('brand_id')->references('brand_id')->on('brand')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};