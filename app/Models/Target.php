<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table = 'target';
    public function cluster()
    {
    	return $this->belongsTo('App\Models\Cluster','ID_CLUSTER','ID');
    }
}
