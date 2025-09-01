<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Custodian extends Model
{
     protected $table = 'custodians';

    protected $fillable = [
        'contractor_id',
        'department_id',
        'status'
    ];
}
