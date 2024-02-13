<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('server_network_details', function (Blueprint $table) {
            $table->id();
            $table->text('ip_address')->index();
            $table->tinyinteger('ip_version');
            $table->string('hostname')->index()->nullable();
            $table->float('round_trip_time', 6, 2)->nullable();
            $table->foreignId('vpn_server_id')->index()->references('id')->on('vpn_servers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_network_details');
    }
};
