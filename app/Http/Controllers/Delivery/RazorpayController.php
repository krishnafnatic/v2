<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RazorpayController extends Controller
{
    //
     public function pay() {
        return view('pay');
    }

    public function dopayment(Request $request) {
        //Input items of form
        $input = $request->all();

        // Please check browser console.
        print_r($input);
        exit;
    }
}
