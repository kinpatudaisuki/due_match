<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'area',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function rooms() {
        return $this->belongsToMany(Room::class)->withTimestamps();
    }

    // ブロックしたユーザーリスト
    public function blockedUsers() {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    // 自分がブロックされたユーザーリスト
    public function blockers() {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    // ブロック済みかどうかをチェック
    public function hasBlocked($userId) {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    // 自分がブロックされたかをチェック
    public function isBlockedBy($userId) {
        return $this->blockers()->where('blocker_id', $userId)->exists();
    }

    public function formats() {
        return $this->belongsToMany(Format::class)->withTimestamps();
    }

    // 実際にフレンドになったユーザー
    public function acceptedFriends() {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
                    ->wherePivot('status', 'accept')
                    ->withTimestamps();
    }

    // 自分が送ったフレンド申請
    public function pendingRequests() {
        return $this->hasMany(Friend::class, 'user_id')
                    ->where('status', 'pending')
                    ->with('friend');
    }

    // 送られてきたフレンド申請
    public function approvalWaiting() {
        return $this->hasMany(Friend::class, 'friend_id')
                    ->where('status', 'pending')
                    ->with('user');
    }

}
