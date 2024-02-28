<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Forum;
use App\Models\Level;
use App\Models\LevelsUser;
use App\Models\Module;
use App\Models\Notif;
use App\Models\UsersMessage;
use App\Models\AnnoncesRelation;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'role',
        'sexe',
        'first_connection',
        'notifs',
        'notif_viewed',
        'annonces',
        'annonce_viewed',
        'password',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function forums () {
        return $this->hasMany(Forum::class);
    }

    public function levels_users () {
        return $this->hasMany(LevelsUser::class);
    }

    public function notif () {
        return $this->hasMany(Notif::class);
    }

    public function modules () {
        return $this->hasMany(Module::class);
    }

    public function annonces () {
        return $this->hasMany(Annonce::class);
    }

    public function annonces_relations () {
        return $this->hasMany(AnnoncesRelation::class);
    }

    public function users_messages () {
        return $this->hasMany(UsersMessage::class);
    }
}
