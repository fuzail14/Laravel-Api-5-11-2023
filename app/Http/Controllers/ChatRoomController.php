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
        try {

        $isValidate = Validator::make($request->all(), [

            'loginuserid' => 'required|exists:users,id',
            // 'chatuserid' => 'required|exists:gatekeepers,gatekeeperid',
            'chatuserid' => 'required|exists:users,id'

        ]);



        if ($isValidate->fails()) {


            $errors=$isValidate->errors()->all();

            for ($i=0; $i < count($errors); $i++) {


                if (strcmp($errors[$i],"The loginuserid has already been taken.")==0)
                {

                    $cov=Chatroom::where('loginuserid',$request->loginuserid)->first();
                    return response()->json([
                        "data" => $cov,
                        "success" => true

                    ], 200);


                }

                else {
                    return response()->json([
                        "errors" => $isValidate->errors()->all(),
                        "success" => false

                    ], 403);

                }

            }

        }



        $chatroom = new Chatroom();
        $chatroom->loginuserid=$request->loginuserid;
        $chatroom->chatuserid=$request->chatuserid;
        $chatroom->save();


        $chatroomusers = new Chatroomuser();
        $chatroomusers ->userid=$chatroom->loginuserid;
        $chatroomusers ->chatuserid=$request->chatuserid;
        $chatroomusers->chatroomid=$chatroom->id;
        $chatroomusers->save();

     


        return response()->json(["data" => $chatroom]);


    }
catch (\Exception $e)

{

  $b=  Str::contains($e->getMessage(),"Integrity constraint violation");

  if($b)
{
    $cov=Chatroom::where('loginuserid',$request->loginuserid)->where('chatuserid',$request->chatuserid)->first()??Chatroom::where('chatuserid',$request->chatuserid)->where('loginuserid',$request->loginuserid)->first();
    return  response()->json(["data" =>$cov],200);

}


    return  response()->json(["error" =>[$e->getMessage()] ],500);

}
}
}
