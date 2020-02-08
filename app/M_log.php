<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_log extends Model
{
    //
    //
    protected $table 		= "logs";
    protected $fillable 	= ['id', 'url', 'parameter', 'response'];
}
