<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    protected $table = 'CLUSTER';
    public function branch()
    {
    	return $this->hasOne('App\Models\Branch','ID','ID_BRANCH');
    }
    public function revenue()
    {
    	return $this->hasMany('App\Models\Revenue','ID','ID_CLUSTER');
    }
    public function target()
    {
    	return $this->hasMany('App\Models\Target','ID','ID_CLUSTER');
    }

    public function myBranch()
    {
        return $this->belongsTo('App\Models\Branch', 'ID_BRANCH', 'ID');
    }
}