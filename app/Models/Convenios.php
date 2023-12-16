<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; //línea necesaria

class Convenios extends Model
{
   use SoftDeletes; //Implementamos 
   // protected $table = 'coach_users';
}