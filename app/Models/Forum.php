<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Level;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'user_id',
        'level_id'
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function level () {
        return $this->belongsTo(Level::class);
    }
}
