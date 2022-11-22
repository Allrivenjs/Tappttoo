<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tattoo_artists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->restrictOnDelete()->restrictOnUpdate();
            $table->string('name_company')->nullable();
            $table->string('base_price')->nullable()->default(null);
            $table->string('price_per_hour')->nullable()->default(null);
            $table->string('instagram')->nullable();
            $table->enum('status', \App\Enums\StatusArtist::toArray())->default('available');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tattoo_artists');
    }
};
