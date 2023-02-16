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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('arabName');
            $table->string('engName');
            $table->bigInteger('classifications_id')->unsigned();
            $table->foreign('classifications_id')->references('id')->on('m_classifications')->onDelete('cascade');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->bigInteger('categoury_id')->unsigned();
            $table->foreign('categoury_id')->references('id')->on('categouries')->onDelete('cascade'); 
            $table->string('pharmacological_form'); //الشكل الدوائي
            $table->string('formula'); //التركيبة
            
            $table->string('drug_details')->default('none'); //تفاصيل الدواء
            $table->boolean('prescription')->default('0'); //الروشتة
            
            $table->string('image')->default('0');
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
        Schema::dropIfExists('medicines');
    }
};
