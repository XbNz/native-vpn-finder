<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('server_network_details', function (Blueprint $table) {
            $table->id();
            $table->text('ip_address');
            $table->string('hostname')->nullable();
            $table->foreignId('vpn_server_id')->references('id')->on('vpn_servers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_network_details');
    }
};
