<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'AREA';
    public function regional()
    {
    	return $this->hasmany('App\Models\Regional','ID','ID_AREA');
    }
}
