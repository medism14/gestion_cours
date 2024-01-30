<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Level;

class LevelsUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'user_id'
    ];

    function level () {
        return $this->belongsTo(Level::class);
    }

    function user () {
        return $this->belongsTo(User::class);
    }
}
