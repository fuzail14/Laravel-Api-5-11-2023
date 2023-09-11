<?php

namespace App\Http\Controllers;
use App\Models\Eventimage;
use App\Models\Gatekeeper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;
use App\Models\User;
use App\Models\Resident;
use Carbon\Carbon;


class EventController extends Controller
{

    public function addevent(Request $request)
    {

        $isValidate = Validator::make($request->all(), [

            'userid' => 'required|exists:users,id',
             'title' => 'required',
            'description' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'active'=> 'required'



        ]);


        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false

            ], 403);
        }

        $event = new Event();
        $event->userid=$request->userid;
        $event->title=$request->title;
        $event->description=$request->description;
        $event->startdate=  Carbon::parse($request->startdate)->format('Y-m-d');
        $event->enddate= Carbon::parse($request->enddate)->format('Y-m-d');
        $event->active=$request->active;
        $event->save();
        $fcm=[];
        $residents= Resident::where('subadminid',$request->userid)
        ->join('users','users.id','=','residents.residentid')->get();


        $gatekeepers= Gatekeeper::where('subadminid',$request->userid)
        ->join('users','users.id','=','gatekeepers.gatekeeperid')->get();

        foreach ($gatekeepers as $datavals) {

            array_push($fcm, $datavals['fcmtoken']);

        }

      

        foreach ($residents as $datavals) {

            array_push($fcm, $datavals['fcmtoken']);

        }

       
           
        $serverkey='AAAAcuxXPmA:APA91bEz-6ptcGS8KzmgmSLjb-6K_bva-so3i6Eyji_ihfncqXttVXjdBQoU6V8sKilzLb9MvSHFId-KK7idDwbGo8aXHpa_zjGpZuDpM67ICKM7QMCGUO_JFULTuZ_ApIOxdF3TXeDR';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $mydata=['registration_ids'=>$fcm,
 
        "data"=>["type"=>'Event'],
        "android"=> [
            "priority"=> "high",
            "ttl"=> 60 * 60 * 1,
            "android_channel_id"=>"high_importance_channel"

        ],
        "notification"=>['title'=>'Event','body'=>$event->description,
        
        ]

    ];
    $finaldata=json_encode($mydata);
        $headers = array (
            'Authorization: key=' . $serverkey,
            'Content-Type: application/json'
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $finaldata );
        $result = curl_exec ( $ch );
        // var_dump($result);
        curl_close ( $ch );








        return response()->json(["data" => $event]);




    }


   


    
    public function addeventimages(Request $request)
    {
        if ($request->hasFile('images')) {

            $images = $request->file('images');
            
            dd($images);

            foreach ($images as $image) {
                dd("sj");
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads'), $imageName);
                // You can also store the image path in your database if needed.
            }

            return response()->json([
                "success"=>true,
              "message"=>"event upload image successfully"]);
        }
    }











        






        public function events($userid)

    {


        $event = Event::where('userid', $userid)->orderBy('created_at','desc')->get();


        return response()->json(["data" => $event]);
    }



    public function deleteevent($id)

    {



        $event = Event::where('id', $id)->delete();

        if($event==1)
        {        return response()->json([
            "success"=>true,
          "message"=>"event deleted successfully"]);}

        return response()->json([
            "success"=>false,
            "message"=>"failed to delete event"],403);




        }


    public  function updateevent (Request $request)


    {


        $isValidate = Validator::make($request->all(), [

            'title' => 'required',
            'description' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'active'=> 'required',
            'id'=> 'required|exists:events,id',


        ]);


        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false

            ], 403);
        }


        $event = Event::find($request->id);

        $event->title=$request->title;
        $event->description=$request->description;
        $event->startdate=  Carbon::parse($request->startdate)->format('Y-m-d');
        $event->enddate= Carbon::parse($request->enddate)->format('Y-m-d');
        $event->active=$request->active;
        $event->update();



        return response()->json([
            "success"=>true,
            "data" => $event,
            "message"=> "event details update successfully"
    ]);



    }

    public  function searchevent ($userid,$q)


    {


        if($q=='Newest')
        {
$event = Event::where('userid', $userid)->orderBy('created_at','desc')->get();

        }
      else  if($q=='Oldest')
        {

            $event = Event::where('userid', $userid)->orderBy('created_at','asc')->get();

        }
        else if($q=='Default'){

 $event = Event::where('userid', $userid)->orderBy('created_at','desc')->get();;


        }

        return response()->json([
            "success"=>true,
            "data" => $event,

    ]);





    }

}
