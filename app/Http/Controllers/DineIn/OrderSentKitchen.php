<?php

namespace App\Http\Controllers\DineIn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DineInModels\HelperTableManager;
use Session;
use App\DineInModels\Kitchen;
use App\DineInModels\BusinessTobeRegistered;
use Illuminate\Support\Str;

class OrderSentKitchen extends Controller
{   
    //
    public function checkpin(Request $request)
    {   
        $data = $request->totaldata;
    	$pin = $data['pin1'].$data['pin2'].$data['pin3'].$data['pin4'];
    	$table_number = Session::get('table');

        $ifexist = HelperTableManager::where('helper_code',$pin)->where('table_number',$table_number)->first();
        if($ifexist == null)
            {
                // return redirect()->back()->with('error','wrong pin');
                return response()->json(['status'=>'wrong pin','message'=>'You have entered wrong pin']);
            }
            else
                { 
                Kitchen::where('table_number',$table_number)->update(['confirm_status' => 'yes']);
            // return view('ordersentkitchen');
                return response()->json(['status'=>'success','url'=>'ordersentkitchen']);
        }
    }
}
