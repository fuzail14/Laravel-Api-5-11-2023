<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\Bill;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Familymember;
use App\Models\Houseresidentaddress;


class BillController extends Controller
{



    
    public function generatebill(Request $request)
    {

      

     $isValidate = Validator::make($request->all(), [

        'subadminid' => 'required|exists:users,id',
        'duedate' => 'required',
        'billstartdate' => 'required',
        'billenddate' => 'required',
        'status'=>'required'
        ]);

        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false
            ], 403);
        }

      $res= Resident::where('subadminid', $request->subadminid)->where('status', 1)->where('propertytype','house')
      ->join('users', 'users.id', '=', 'residents.residentid')->get();

      $residentlist=[];
      $noOfAppUsers=1;

      foreach($res as $li)
      {

    
        array_push($residentlist, $li->residentid);
        $noOfusers= Familymember::where('subadminid',$request->subadminid)->where('residentid',$li->residentid)->count();
        $noOfAppUsers=$noOfAppUsers+ $noOfusers;

       
      }

    
      
      $charges=0.0;
      $chargesafterduedate=0.0;
      $appcharges=0.0;
      $tax=0.0;
      $balance=0.0;	
      $subadminid=0;
      $residnentid=0;
      $propertyid=0;
      $measurementid=0;
      $duedate=null;
      $billstartdate=null;
     $billenddate=null;
     $getmonth=null;
     $month=null;
     $status=0;
     $payableamount=0.0;
    




  

    

        foreach ($residentlist as $li)

{

    $residnents = Houseresidentaddress::where('houseresidentaddresses.residentid', $li)->join('residents', 'houseresidentaddresses.residentid', '=', 'residents.residentid')->with('property')->with('measurement')->first();
    $measurement =$residnents['measurement'];
    $property =$residnents['property'];
    $subadminid=$request->subadminid;
    $residnentid=$residnents->residentid;
    $status= $request->status;
    $duedate=$request->duedate;
    $billstartdate=$request->billstartdate;
    $billenddate=$request->billenddate;
    $getmonth = Carbon::parse( $duedate)->format('F Y');
    $month=$getmonth;
  

  
    foreach ($measurement as $measurement)

    {
        

$measurementid=$measurement->id;
$charges=$measurement->charges;
$appcharge=$measurement->appcharges;
$tax=$measurement->tax;
$payableamount=($appcharge*$noOfAppUsers)+($tax+$charges);
$chargesafterduedate=($measurement->chargesafterduedate+($appcharge*$noOfAppUsers)+$tax);
$balance=$payableamount;



    }

    foreach ($property as $property)

    { 
        $propertyid= $property->id;


    }

    $firstDate = Carbon::parse($duedate);

    
    $existingBill = Bill::where('residentid', $residnentid)
                        ->whereMonth('duedate',  $firstDate->month)
                        ->whereYear('duedate',  $firstDate->year)
                        ->first();

                  
       if($existingBill!=null)

{
                $firstDate = Carbon::parse($duedate);
                $secondDate = Carbon::parse( $existingBill->duedate);
              
              
    if ($firstDate->year === $secondDate->year&&$firstDate->month === $secondDate->month ) {
        return response()->json(['message' => 'A bill has already been generated for this user in this month.'], 400);
    }
   
    
   

}



$bill = new Bill();  



$billstatus =  $bill->insert(
[

 [
      'charges'=>$charges,
      'chargesafterduedate'=>$chargesafterduedate,
      'appcharges'=>$appcharge,
      'tax'=>$tax,
      'payableamount'=>$payableamount,
      'balance'=>$balance,
     'subadminid' => $subadminid,
     'residentid'=>$residnentid,
     'propertyid'=>$propertyid,
     'measurementid'=>$measurementid,
     'duedate'=>$duedate,
     'billstartdate'=>$billstartdate,
     'billenddate'=>$billenddate,
     'month'=>$month,
     'status'=>$status,
     'created_at' => date('Y-m-d H:i:s'),
     'updated_at' => date('Y-m-d H:i:s'),
     'noofappusers'=> $noOfAppUsers
 ],

]
);



}

return response()->json([
    "success" => true,
    
    "message"=> "Monthly Bill Generated for Residents Successfully !"
]);



       
   
    }


    public function generatedbill($subadminid)
    {

        $bills =  Bill::where('subadminid', $subadminid) ->join('users', 'users.id', '=', 'bills.residentid')
        ->select(
            
            'users.rolename',
            'bills.*',
            'users.firstname', 
            'users.lastname',
             'users.image',
            'users.cnic',
            'users.roleid',
          
            
            
            )->get();



            $bills =  Bill::where('subadminid', $subadminid)
            ->with('user')
            ->with('resident')
            ->with('measurement')
            ->with('property')
        
            ->get();





        return response()->json([
            "success" => true,
            "data" => $bills,
        ]);
    }



    public function monthlybills($residnentid,)
    {



        $currentDate = date('Y-m-d');
        $currentYear = date('Y', strtotime($currentDate));
        $currentMonth = date('m', strtotime($currentDate));

        $bills =  Bill::where('residentid',$residnentid)
        ->whereMonth('billenddate', $currentMonth)->whereYear('billenddate',
        $currentYear)
        ->where('status', 0) ->get()->first();





        return response()->json([
            "success" => true,
            "data" => $bills,
        ]);
    }




    
 

}