<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitortype extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "gatekeeperid",
        "userid",
        "visitortype",
        "name",
        "description",
        "cnic",
        "mobileno",
        "vechileno",
        "arrivaldate",
        "arrivaltime",
        "status",
        "statusdescription"
    ];
}
