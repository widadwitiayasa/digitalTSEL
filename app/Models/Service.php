<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'SERVICE';
    public function revenue()
    {
    	return $this->hasMany('App\Models\Revenue','ID','ID_SERVICE');
    }
}
