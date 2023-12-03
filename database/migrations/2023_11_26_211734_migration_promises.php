<?php

use App\Models\Promise;
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
        Schema::create('promises', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->enum('status', [Promise::STATUS_PENDING, Promise::STATUS_FULFILLED, Promise::STATUS_REJECTED])->index();
            $table->string('subject')->index();
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->string('user_friendly_status');
            $table->json('user_friendly_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promises');
    }
};
