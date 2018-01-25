<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'BRANCH';
    public function regional()
    {
    	return $this->belongsTo('App\Models\Regional','ID_REGIONAL','ID');
    }
    public function cluster()
    {
    	return $this->hasMany('App\Models\Cluster','ID','ID_BRANCH');
    }
}
