<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Forum;
use App\Models\Resource;
use App\Models\Sector;
use App\Models\LevelsUser;
use App\Models\AnnoncesRelation;
use App\Models\UsersMessage;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'degree',
        'sector_id'
    ];
    
    public function modules () {
        return $this->hasMany(Module::class);
    }
    
    public function levels_users () {
        return $this->hasMany(LevelsUser::class);
    }

    public function forum () {
        return $this->hasOne(Forum::class);
    }

    public function sector () {
        return $this->belongsTo(Sector::class);
    }

    public function annonces_relations () {
        return $this->hasMany(AnnoncesRelation::class);
    }

    public function users_messages () {
        return $this->hasMany(UsersMessage::class);
    }
}
