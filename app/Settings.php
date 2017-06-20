<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    //
    private $fillable = ['classes', 'attendance', ''];

    public function users() {
        return $this->belongsTo(App\User::class);
    }
}
