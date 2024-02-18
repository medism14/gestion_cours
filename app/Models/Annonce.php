<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\AnnoncesRelation;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'choix_filieres',
        'choix_personnes',
        'date_expiration',
        'user_id'
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function annonces_relations () {
        return $this->hasMany(AnnoncesRelation::class);
    }
}
