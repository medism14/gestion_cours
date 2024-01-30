<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use App\Models\File;
use App\Models\Notif;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'description',
        'section',
        'fileType',
        'module_id'
    ];

    public function module () {
        return $this->belongsTo(Module::class);
    }

    public function file () {
        return $this->hasOne(File::Class);
    }

    public function notif () {
        return $this->hasMany(Notif::class);
    }

}
