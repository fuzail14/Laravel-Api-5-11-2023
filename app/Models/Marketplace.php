<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;
    protected $fillable = [
        "residentid",
        "societyid",
        "subadminid",
        "productname",
        "description",
        "productprice",
        "images"

    ];

    // public function setFilenamesAttribute($value)
    // {
    //     $this->attributes['images'] = json_encode($value);
    // }
    public function resident()
    {
        return $this->hasMany('App\Models\User', 'id', 'residentid');
    }
    public function residentdata()
    {
        return $this->hasMany('App\Models\Resident', 'residentid', 'residentid');
    }
}
