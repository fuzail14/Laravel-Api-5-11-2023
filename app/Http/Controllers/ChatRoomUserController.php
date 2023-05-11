<?php

namespace App\Http\Controllers;
use App\Models\Chat;
use App\Models\Chatroomuser;

use Illuminate\Http\Request;

class ChatRoomUserController extends Controller
{
    public function fetchchatroomusers($userid,$chatuserid)
    {



        $cov=Chatroomuser::where('userid',$userid)->where('chatuserid',$chatuserid)->first()??Chatroomuser::where('userid',$chatuserid)->where('chatuserid',$userid)->first();




        return response()->json([
            "success"=>true,
            "data" => $cov]);




    }

}
