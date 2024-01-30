<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Level;
use App\Models\User;
use App\Models\Resource;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level_id',
        'user_id'
    ];

    public function level () {
        return $this->belongsTo(Level::class);
    }

    public function resources () {
        return $this->hasMany(Resource::class);
    }

    public function user () {
        return $this->belongsTo(User::class);
    }
}
