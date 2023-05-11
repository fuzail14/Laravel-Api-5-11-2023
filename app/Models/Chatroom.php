<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    use HasFactory;
    protected $fillable = [
        'loginuserid',

     ];


     protected $casts = [
        "loginuserid"=> 'integer',
        "chatuserid"=> 'integer',


    ];
}
