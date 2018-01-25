<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    protected $table = 'REGIONAL';
    public function area()
    {
    	return $this->belongsTo('App\Models\Area','ID_AREA','ID');
    }
    public function branch()
    {
    	return $this->hasmany('App\Models\Branch','ID','ID_REGIONAL');
    }

}
