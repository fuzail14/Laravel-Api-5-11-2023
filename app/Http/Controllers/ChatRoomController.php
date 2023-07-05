<?php

namespace App\Http\Controllers;
use App\Models\Chatroom;
use App\Models\Chatroomuser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Chat;
class ChatRoomController extends Controller
{
    //

    public function createchatroom (Request $request)
    {
        

        $isValidate = Validator::make($request->all(), [

            'loginuserid' => 'required|exists:users,id',
            'chatuserid' => 'required|exists:users,id'

        ]);



        if ($isValidate->fails()) {


            

            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false
            ], 403);

        }


        $chatroom=Chatroom::where('loginuserid',$request->loginuserid)->where(
            "chatuserid",$request->chatuserid)
        ->get();

        if($chatroom->isEmpty() )

        {
            
            $chatroom2=  Chatroom::where('loginuserid',$request->chatuserid)->where(
                "chatuserid",$request->loginuserid)
            ->get();

            
            if($chatroom2->isEmpty() )
            {

            $chatroom = new Chatroom();
            $chatroom->loginuserid=$request->loginuserid;
            $chatroom->chatuserid=$request->chatuserid;
            $chatroom->save();

            return response()->json(["data" => [$chatroom]]);
            }

            
    
            return response()->json(["data" => $chatroom2]);



        }
        
       
        
         
    

        return response()->json(["data" => $chatroom]);


   


 
}


public function fetchChatRoom($userid,$chatuserid)
    {



        $cov=Chatroom::where('loginuserid',$userid)
        ->where('chatuserid',$chatuserid)
        ->first()??Chatroom::where('loginuserid',$chatuserid)
        ->where('chatuserid',$userid)->first();




        return response()->json([
            "success"=>true,
            "data" => $cov]);




    }
}
