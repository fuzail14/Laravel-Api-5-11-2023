<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Marketplace;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use PhpParser\Parser\Multiple;

class MarketPlaceController extends Controller
{

    public function addProduct(Request $request)
    {

        $isValidate = Validator::make($request->all(), [

            'residentid' => 'required|exists:residents,residentid',
            'societyid' => 'required|exists:societies,id',
            'subadminid' => 'required|exists:subadmins,subadminid',
            'productname' => 'required',
            'description' => 'required',
            'productprice' => 'required',
            'images' => 'required',
            //'images.*' => 'image'


        ]);


        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false

            ], 403);
        }


        // $files = [];
        // if ($request->hasfile('images')) {
        //     foreach ($request->file('images') as $file) {
        //         $name = time() . rand(1, 50) . '.' . $file->extension();
        //         $file->move(public_path('files'), $name);
        //         $files[] = $name;
        //     }
        // }


        $product = new Marketplace();


        $product->residentid = $request->residentid;
        $product->societyid = $request->societyid;
        $product->subadminid = $request->subadminid;
        $product->productname = $request->productname;
        $product->description = $request->description;
        $product->productprice = $request->productprice;
        $product->description = $request->description;
        $images = $request->file('images');
        $imageName = time() . "." . $images->extension();
        $images->move(public_path('/storage/'), $imageName);
        $product->images = $imageName;
        $product->save();

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }


    public function viewProducts($societyid)
    {
        $products = Marketplace::where('societyid', $societyid)
            ->join('users', 'marketplaces.residentid', '=', 'users.id')->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function viewSellProductsResidnet($residentid)
    {
        $products = Marketplace::where('residentid', $residentid)->with('resident')->with('residentdata')->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
