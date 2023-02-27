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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->text('icon')->nullable();
            $table->string('name');
            $table->text('description');
            $table->integer('price')->unsigned(); //Without decimals
            $table->integer('duration_in_days')->default(30);
            $table->timestamps();
        });
        Schema::create('users_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->foreignIdFor(\App\Models\Plan::class)->constrained();
            $table->foreignIdFor(\App\Models\Payment::class)->constrained(); //Payment ID
            $table->timestamp('expires_at');
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
        Schema::dropIfExists('plans');
    }
};
