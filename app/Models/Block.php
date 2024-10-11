<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = ['blocker_id', 'blocked_id'];

    // ブロックしたユーザー
    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    // ブロックされたユーザー
    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }
}
