<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('friends', function (Blueprint $table) {
            // もし外部キーが存在すれば削除
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                                       WHERE TABLE_NAME = 'friends' AND TABLE_SCHEMA = DATABASE();");

            $foreignKeyNames = collect($foreignKeys)->pluck('CONSTRAINT_NAME')->toArray();

            if (in_array('friends_user_id_foreign', $foreignKeyNames)) {
                $table->dropForeign(['user_id']);
            }
            if (in_array('friends_friend_id_foreign', $foreignKeyNames)) {
                $table->dropForeign(['friend_id']);
            }

            // 外部キーを新たに設定
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('friends', function (Blueprint $table) {
            // もし外部キーが存在すれば削除
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                                       WHERE TABLE_NAME = 'friends' AND TABLE_SCHEMA = DATABASE();");

            $foreignKeyNames = collect($foreignKeys)->pluck('CONSTRAINT_NAME')->toArray();

            if (in_array('friends_user_id_foreign', $foreignKeyNames)) {
                $table->dropForeign(['user_id']);
            }
            if (in_array('friends_friend_id_foreign', $foreignKeyNames)) {
                $table->dropForeign(['friend_id']);
            }

            // 以前の状態に戻す
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('friend_id')->references('id')->on('users');
        });
    }
};
