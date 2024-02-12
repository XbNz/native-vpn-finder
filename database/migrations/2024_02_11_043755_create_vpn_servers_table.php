<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vpn_servers', function (Blueprint $table) {
            $table->id();
            $table->string('protocol')->nullable();
            $table->foreignId('vpn_provider_id')->index()->references('id')->on('vpn_providers');
            $table->foreignId('country_id')->index()->references('id')->on('countries');
            $table->foreignId('region_id')->index()->nullable()->references('id')->on('regions');
            $table->foreignId('city_id')->index()->nullable()->references('id')->on('cities');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vpn_servers');
    }
};
