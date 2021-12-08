<?php

namespace Petrik\Loginapp;

use Illuminate\Database\Eloquent\Model;

class User extends Model{
    //ha created_at / updated_at nélkül hoztuk létre:
    public $timestamps = false;

    protected $visible= ['id', 'email']; 
}