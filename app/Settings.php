<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['classes', 'grades', 'attendance'];

    public function users() {
        return $this->belongsTo(User::class);
    }
}
