<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subadmin extends Model
{
    protected $fillable = [

        'superadminid',
        'societyid',
        'subadminid',
        'firstname',
        'lastname',
        'cnic',
        'password',
        'roleid',
        'rolename',

    ];
    use HasFactory;
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    public function societydata()
    {
        return $this->hasMany('App\Models\Subadmin', 'subadminid', 'subadminid');
    }

}
