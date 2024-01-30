<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Level;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function levels () {
        return $this->hasMany(Level::class);
    }

}
