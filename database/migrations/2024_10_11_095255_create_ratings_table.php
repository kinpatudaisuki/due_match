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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rater_id')->onDelete('cascade'); // 評価者
            $table->foreignId('rated_id')->onDelete('cascade'); // 被評価者
            $table->tinyInteger('rating'); // 評価 (1〜3段階)
            $table->timestamps();
        
            // 同じユーザーが同じ相手に複数回評価できないようにする制約
            $table->unique(['rater_id', 'rated_id']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
