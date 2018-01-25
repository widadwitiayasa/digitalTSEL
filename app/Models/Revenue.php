<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $table = 'REVENUE';
    public function cluster()
    {
    	return $this->belongsTo('App\Models\Cluster','ID_CLUSTER','ID');
    }
    
    public function fromService()
    {
    	return $this->belongsTo('App\Models\Service','ID_SERVICE','ID');
    }
}
