<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kitchen;
use App\CategoryItem;
use Auth;
use App\Events\Notify;
use DB;
use Session;
use App\SessionValue;
use Illuminate\Support\Str;
use Bitfumes\Multiauth\Model\Admin;
use App\Orders;

class KitchenController extends Controller
{
    
    public function getItems()
    {   
    	$category_items = CategoryItem::all();
    	$kitchen = null;
    	//serial number counter
    	$count = 1;
        $total_price = 0;

    	if(Auth::guest())
    	{

            $id =  Session::get('uni_id');

            $kitchen = SessionValue::where('session_id',$id)->get();
            if(SessionValue::where('session_id',$id)->sum('item_quantity') == 0)
            {
                $kitchen = null;
            }
            else
            {
                $total_price = 0;

            foreach ($kitchen as $key) {
                foreach ($category_items as $value) {
                    if($key['item_id'] == $value['item_id'])
                    {
                        $total_price += $key['item_quantity']*$value['item_price']; 
                    }
                }
            }

            $total_price*= 100;
            }
    	}
    	else
    	{
            if(isset($_COOKIE['uni_id']))
            {   //get cookie
                $val = $_COOKIE['uni_id'];

                Kitchen::where('user_id',Auth::user()->id)->where('confirm_status',null)->delete();

                $session_value = SessionValue::where('session_id',$val)->get();

                foreach ($session_value as $key) {
                    # code...
                    $new = new Kitchen;
                    $new->user_id = Auth::user()->id;
                    $new->item_id = $key['item_id'];
                    $new->item_quantity = $key['item_quantity'];
                    $new->save();

                    $key->delete();
                }

                //delete cookie
                setcookie('uni_id', '', time() - 3600);
            }


    		$kitchen = Kitchen::where('user_id',Auth::user()->id)->where('confirm_status',null)->get();
    		if(Kitchen::where('user_id',Auth::user()->id)->where('confirm_status',null)->sum('item_quantity') == 0)
    		{
    			$kitchen = null;
    		}
            else
            {
                $total_price = 0;

            foreach ($kitchen as $key) {
                foreach ($category_items as $value) {
                    if($key['item_id'] == $value['item_id'])
                    {
                        $total_price += $key['item_quantity']*$value['item_price']; 
                    }
                }
            }

            $total_price*= 100;
            }
    		
    	}

    	return view('kitchen',['category_items' => $category_items,'kitchen' => $kitchen,'count' => $count,'total_price' => $total_price]);
    }

    public function updateItems(Request $request)
    {
    	if($request->action == 'plus')
    	{   

            if(Auth::guest())
            {
                $id = Session::get('uni_id');

                SessionValue::where('session_id',$id)->where('item_id',$request->item_id)->increment('item_quantity');

                $item_qty = SessionValue::where('session_id',$id)->where('item_id',$request->item_id)->value('item_quantity');

            $item_price = CategoryItem::where('item_id',$request->item_id)->value('item_price');
            $item_price = $item_price * $item_qty;
            $total_price = 0;
            $category_items = CategoryItem::all();
            $kitchen = SessionValue::where('session_id',$id)->get();
            foreach ($kitchen as $key) {
                foreach ($category_items as $value) {
                    if($key['item_id'] == $value['item_id'])
                    {
                        $total_price += $key['item_quantity']*$value['item_price']; 
                    }
                }
            }

            $total_price*= 100;


                $response = array(
                    'status' => 'unauthorized',
                    'item_price' => $item_price,
                    'total' => $total_price,
                );

                return response()->json($response);

            }
            else{
                Kitchen::where('user_id',Auth::user()->id)->where('item_id',$request->item_id)->where('confirm_status',null)->increment('item_quantity');

            $item_qty = Kitchen::where('user_id',Auth::user()->id)->where('item_id',$request->item_id)->where('confirm_status',null)->value('item_quantity');

            $item_price = CategoryItem::where('item_id',$request->item_id)->value('item_price');
            $item_price = $item_price * $item_qty;

            $category_items = CategoryItem::all();
            $kitchen = Kitchen::where('user_id',Auth::user()->id)->where('confirm_status',null)->get();
            $total_price = 0;

            foreach ($kitchen as $key) {
                foreach ($category_items as $value) {
                    if($key['item_id'] == $value['item_id'])
                    {
                        $total_price += $key['item_quantity']*$value['item_price']; 
                    }
                }
            }

            $total_price*= 100;


                $response = array(
                    'status' => 'success',
                    'item_price' => $item_price,
                    'total' => $total_price,
                );

                return response()->json($response);
            }

    		
    	}

    	if($request->action == 'minus')
    	{
            if(Auth::guest())
            {
                $id = Session::get('uni_id');

                SessionValue::where('session_id',$id)->where('item_id',$request->item_id)->decrement('item_quantity');

                if(SessionValue::where('session_id',$id)->where('item_id',$request->item_id)->value('item_quantity') == 0)
                {
                    SessionValue::where('session_id',$id)->where('item_id',$request->item_id)->delete();

                    $response = array(
                    'status' => 'delete',
                );

                return response()->json($response);
                }

            $item_qty = SessionValue::where('session_id',$id)->where('item_id',$request->item_id)->value('item_quantity');

            $item_price = CategoryItem::where('item_id',$request->item_id)->value('item_price');
            $item_price = $item_price * $item_qty;
            $total_price = 0;
            $category_items = CategoryItem::all();
            $kitchen = SessionValue::where('session_id',$id)->get();
            foreach ($kitchen as $key) {
                foreach ($category_items as $value) {
                    if($key['item_id'] == $value['item_id'])
                    {
                        $total_price += $key['item_quantity']*$value['item_price']; 
                    }
                }
            }

            $total_price*= 100;


                $response = array(
                    'status' => 'unauthorized',
                    'item_price' => $item_price,
                    'total' => $total_price,
                );

                return response()->json($response);

            }
            else
            {
                Kitchen::where('user_id',Auth::user()->id)->where('item_id',$request->item_id)->where('confirm_status',null)->decrement('item_quantity');

                $to_delete = Kitchen::where('user_id',Auth::user()->id)->where('item_id',$request->item_id)->where('confirm_status',null)->pluck('item_quantity');

                if($to_delete[0] == 0)
                {
                    Kitchen::where('user_id',Auth::user()->id)->where('item_id',$request->item_id)->where('confirm_status',null)->delete();
                    $response = array(
                    'status' => 'delete',
                );

                return response()->json($response); 
                }

                $category_items = CategoryItem::all();
                $kitchen = Kitchen::where('user_id',Auth::user()->id)->where('confirm_status',null)->get();
                $total_price = 0;

                foreach ($kitchen as $key) {
                    foreach ($category_items as $value) {
                        if($key['item_id'] == $value['item_id'])
                        {
                            $total_price += $key['item_quantity']*$value['item_price']; 
                        }
                    }
                }

                $total_price*= 100;


                $item_qty = Kitchen::where('user_id',Auth::user()->id)->where('item_id',$request->item_id)->where('confirm_status',null)->value('item_quantity');

                $item_price = CategoryItem::where('item_id',$request->item_id)->value('item_price');
                $item_price = $item_price * $item_qty;

                $response = array(
                    'status' => 'success',
                    'item_price' => $item_price,
                    'total' => $total_price,
                );

                return response()->json($response);
            }


    			
    	}

    }

    public function confirm(Request $request)
    {  
        $order = new Orders;
        $order->razorpay_payment_id = $request->id;
        $order->amount = $request->amount;
        $order->time_slot = $request->timeid;
        $order->date = $request->date;
        $order->user_id = Auth::user()->id;
        $order->address_id = $request->address;
        $order->save();
        $id = $order->id;
        event(new Notify('12'));
        setcookie('orderid',$id);
        Kitchen::where('user_id',Auth::user()->id)->where('confirm_status',null)->update(['confirm_status' => 1,'order_id' => $id]);


        $response = array(
                    'status' => 'success',
                );

        return response()->json($response); 
    }


    public function check_status(Request $request)
    {
        if(Orders::where('id',$_COOKIE['orderid'])->value('order_status') == 'Accepted')
        {
            $response = array(
                    'status' => 'accept',
                );
        }
        else if(Orders::where('id',$_COOKIE['orderid'])->value('order_status') == 'Preparing')
        {
            $response = array(
                    'status' => 'preparing',
                );
        }
        else if(Orders::where('id',$_COOKIE['orderid'])->value('order_status') == 'Out For Delivery')
        {
            $response = array(
                    'status' => 'outfor',
                );
        }
        else if(Orders::where('id',$_COOKIE['orderid'])->value('order_status') == 'Delivered')
        {
            $response = array(
                    'status' => 'delivered',
                );
        }
        $response = array(
                    'status' => 'unknown',
                );
        return response()->json($response); 

    }


    public function ordersentkitchen()
    {
        $cookie = $_COOKIE['orderid'];

        return view('order_sent_kitchen',['cookie'=>$cookie]);
    }


    public function checkminimumprice(Request $request)
    {   
        //fix minimum price
        $minimum_price = 999;

        if(($request->price/100) >= $minimum_price)
        {
            $response = array(
                    'status' => 'okay',
                );
        }
        else{
            $response = array(
                    'status' => 'more',
                );
        }
        
        return response()->json($response); 
    }
}
