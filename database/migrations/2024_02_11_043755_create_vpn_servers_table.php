<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vpn_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vpn_provider_id')->references('id')->on('vpn_providers');
            $table->foreignId('country_id')->references('id')->on('countries');
            $table->foreignId('city_id')->nullable()->references('id')->on('cities');
            $table->string('protocol')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vpn_servers');
    }
};
