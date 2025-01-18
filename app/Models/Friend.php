<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'status'];

    // 送信者
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 受信者
    public function friend() {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public static function isPendingRequest($currentUserId, $userId) {
        return self::where('user_id', $currentUserId)
                   ->where('friend_id', $userId)
                   ->where('status', 'pending')
                   ->exists();

                }
    public static function hasReceivedFriendRequest($currentUserId, $userId) {
        return self::where('user_id', $userId)
                    ->where('friend_id', $currentUserId)
                    ->where('status', 'pending')
                    ->exists();
    }

}
