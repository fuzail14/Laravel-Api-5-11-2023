<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Superadminfinancemanagers;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SuperAdminFinanceManagerController extends Controller
{


    public function superAdminFinanceMangerRegister(Request $request)
    {
        $isValidate = Validator::make($request->all(), [
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'cnic' => 'required|unique:users|max:191',
            'address' => 'required',
            'mobileno' => 'required',
            'roleid' => 'required',
            'rolename' => 'required',
            'password' => 'required',
            'image' => 'nullable|image',
            "superadminid" => 'required|exists:users,id',


        ]);
        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false
            ], 403);
        }


        $user = new User;

        $image = $request->file('image');

        if ($image != null) {
            $imageName = time() . "." . $image->extension();
            $image->move(public_path('/storage/'), $imageName);
            $user->image = $imageName;
        }
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->cnic = $request->cnic;
        $user->address = $request->address;
        $user->mobileno = $request->mobileno;
        $user->roleid = $request->roleid;
        $user->rolename = $request->rolename;
        $user->image = $imageName ?? 'images/user.png';
        $user->password = Hash::make($request->password);
        // $user->password = $request->password;


        $user->save();
        $tk =   $user->createToken('token')->plainTextToken;
        $financeManager = new Superadminfinancemanagers;
        $financeManager->financemanagerid = $user->id;
        $financeManager->superadminid = $request->superadminid;
        $financeManager->status = $request->status ?? 'active';

        $financeManager->save();

        return response()->json(
            [
                "token" => $tk,
                "success" => true,
                "message" => "Register Successfully",
                "data" => $user,
            ]
        );
    }
    public function view($id)
    {

        $data = Superadminfinancemanagers::where('subadminid', $id)
            ->join('users', 'users.id', '=', 'financemanagers.financemanagerid')->get();
        return response()->json(
            [
                "success" => true,
                "data" => $data
            ]
        );
    }
    public function delete($id)


    {
        $user = User::where('id', $id)->first();



        if ($user == null) {
            return response()->json([
                "errors" => "Id Not Exist",
                "success" => false
            ], 403);
        }



        $destination = public_path('storage\\') . $user->image;





        if (File::exists(public_path('storage\\') . 'images/user.png')) {
        }
        if (File::exists($destination)) {


            unlink($destination);
        }

        $financeManager =   User::where('id', $id)->delete();

        if ($financeManager == 0) {
            return response()->json([
                "errors" => "Id Not Exist",
                "success" => false
            ], 403);
        }




        return response()->json([
            "success" => true,
            "message" => "Deleted successfully"
        ]);
    }
    public function update(Request $request)
    {
        $isValidate = Validator::make($request->all(), [
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'address' => 'required',
            'mobileno' => 'required',
            'image' => 'nullable|image',
            "id" => 'required|exists:users,id',
        ]);
        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false
            ], 403);
        }
        $user = User::Find($request->id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = $request->address;
        $user->mobileno = $request->mobileno;
        $user->password = Hash::make($request->password);


        if ($request->hasFile('image')) {

            $destination = public_path('storage\\') . $user->image;




            if (File::exists(public_path('storage\\') . 'images/user.png')) {
            } else  if (File::exists($destination)) {

                unlink($destination);
            }

            $image = $request->file('image');
            $imageName = time() . "." . $image->extension();
            $image->move(public_path('/storage/'), $imageName);

            $user->image = $imageName;
        }




        $user->update();

        return response()->json([
            "success" => true,
            "message" => "Record Updated Successfully"
        ]);
    }


    public function currentMonthBills($subadminid)
    {

        //User
        //Resident





        $currentDate = date('Y-m-d');
        $currentYear = date('Y', strtotime($currentDate));

        $currentMonth = date('m', strtotime($currentDate));

        // $bills = Bill::where('subadminid', $subadminid)
        //  ->whereMonth('billenddate', $currentMonth)
        //  ->whereYear(
        //     'billenddate',
        //     $currentYear
        // )
        // ->whereIn('status',[0,1])
        //     ->with('user')
        //     ->with('resident')
        //     ->get();


        $bills = Bill::where('subadminid', $subadminid)
            ->whereMonth('billenddate', $currentMonth)
            ->whereYear(
                'billenddate',
                $currentYear
            )->with('resident')
            ->with('user')
            ->with('measurement')
            ->get();






        return response()->json([
            "success" => true,
            "data" => $bills,
        ]);
    }
}
