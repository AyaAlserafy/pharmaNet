<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pharmacy_midicine', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_id')->unsigned();
            $table->unsignedBigInteger('medicine_id')->unsigned();
            $table->string('caliber'); //العيار
            $table->bigInteger('quantity'); //الكمية
            $table->bigInteger('purchasing_price'); //سعر الشراء
            $table->bigInteger('selling_price'); //سعر المبيع
            $table->date('production_date'); //تاريخ الصنع
            $table->date('expiration_date'); //تاريخ انتهاء الصلاحية
            $table->float('discount')->nullable(); //الخصم
            
        });
        Schema::table('pharmacy_midicine', function($table)
        { $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pharmacy_midicine');
    }
};
