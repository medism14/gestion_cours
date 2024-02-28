<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Annonce;
use App\Models\Level;
use App\Models\User;

class AnnoncesRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'annonce_id',
        'level_id',
        'user_id',
        'notif'
    ];  

    public function annonce () {
        return $this->belongsTo(Annonce::class);
    }

    public function level () {
        return $this->belongsTo(Level::class);
    }

    public function user () {
        return $this->belongsTo(User::class);
    }
}
