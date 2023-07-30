<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndividualBill;
use App\Models\IndividualBillItem;
use Illuminate\Support\Facades\Validator;


class IndividualBillController extends Controller
{
    public function createIndividualBill(Request $request)
    {
        $isValidate = Validator::make($request->all(), [

            'subadminid' => 'required|exists:users,id',
            'financemanagerid' => 'required|exists:users,id',
            'residentid' => 'required|exists:residents,residentid',
            //'propertyid' => 'required|exists:properties,id',
            'billstartdate' => 'required|date',
            'billenddate' => 'required|date',
            'duedate' => 'required|date',
            'billtype' => 'required|string',
            'paymenttype' => 'required|string',
            'status' => 'required|in:paid,unpaid,partiallypaid',
            //'charges' => 'required|numeric',

            'latecharges' => 'required|numeric',
            'tax' => 'required|numeric',
            //'balance' => 'required|numeric',
            //'payableamount' => 'required|numeric',
            //'totalpaidamount' => 'required|numeric',
            'isbilllate' => 'required|numeric',

            // Validation rules for individualbillitems table
            'bill_items' => 'required|array',
            'bill_items.*.billname' => 'required|string',
            'bill_items.*.billprice' => 'required|numeric',
        ]);
        if ($isValidate->fails()) {
            return response()->json([
                "errors" => $isValidate->errors()->all(),
                "success" => false
            ], 403);
        }

        // Create individualbills record






        $charges = 0;
        foreach ($request['bill_items'] as $item) {
            $charges += $item['billprice'];
        }

        $individualBill = new IndividualBill;
        $individualBill->subadminid = $request->subadminid;
        $individualBill->financemanagerid = $request->financemanagerid;
        $individualBill->residentid = $request->residentid;
        //$individualBill->propertyid = $request->propertyid;
        $individualBill->billstartdate = $request->billstartdate;
        $individualBill->billenddate = $request->billenddate;
        $individualBill->duedate = $request->duedate;

        $individualBill->billtype = $request->billtype;
        $individualBill->paymenttype = $request->paymenttype ?? 'NA';
        $individualBill->status = $request->status;

        $individualBill->charges = $charges;
        $individualBill->latecharges = $request->latecharges;

        $individualBill->tax = $request->tax;

        $individualBill->payableamount =  $request->tax + $charges;

        $individualBill->balance = $request->payableamount;

        $individualBill->totalpaidamount = $request->totalpaidamount;
        $individualBill->isbilllate = $request->isbilllate;

        $individualBill->save();


        // Create individualbillitems records

        foreach ($request['bill_items'] as $item) {
            IndividualBillItem::create([
                'individualbillid' => $individualBill->id,
                'billname' => $item['billname'],
                'billprice' => $item['billprice'],
            ]);
        }

        return response()->json([
            'message' => 'Bill created successfully',
            'data' => $individualBill
        ]);
    }


    public function getIndividualBillsForFinance($subadminid)
    {
        $individualBills = IndividualBill::where('subadminid', $subadminid)->with('billItems')->get();

        return response()->json([
            'message' => 'Individual bills fetched successfully',
            'individualBills' => $individualBills
        ]);
    }
    public function getIndividualBillsByResident($residentid)
    {
        $individualBills = IndividualBill::where('residentid', $residentid)->with('billItems')->get();

        return response()->json([
            'message' => 'Individual bills fetched successfully',
            'individualBills' => $individualBills
        ]);
    }
}
