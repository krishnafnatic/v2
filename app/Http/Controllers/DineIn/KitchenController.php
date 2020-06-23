<?php

namespace App\Http\Controllers\DineIn;

use Illuminate\Http\Request;
use App\DineIn\Kitchen;
use App\DineIn\KitchenItemAddon;
use App\DineIn\ItemAddon;
use App\DineIn\KitchenCustomize;
use App\DineIn\CategoryItem;
use DB;
use Session;
use App\DineIn\BusinessTobeRegistered;
use Illuminate\Support\Str;
use App\DineIn\AlertNotification;

class KitchenController extends Controller
{
    //

    public function display()
    { 
      $table_number = Session::get('table');

      // check if kitchen empty
      $total_items = DB::table("dikitchen")->where("table_number","=",$table_number)->where('confirm_status',null)->get()->sum("item_quantity");
      $kitchen = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->get();
      $category_items = CategoryItem::all();
      
      $addons = DB::table('dikitchen_customize')->join('dikitchen_item_addons','dikitchen_customize.id','=','dikitchen_item_addons.order_id')->get();
      $kitchen_customize = KitchenCustomize::all();
      $original_addons = ItemAddon::all();
      $count = '1';
    	return view('dinein.kitchen',['kitchen' => $kitchen,'category_items' => $category_items, 'addons' => $addons,'original_addons' => $original_addons,'count' => $count,'kitchen_customize' => $kitchen_customize,'total_items' => $total_items]);
    }

    public function update(Request $request)
    {	
      $table_number = Session::get('table');
      $ifexist = Kitchen::where('item_id',$request->item_id)->where('table_number',$table_number)->where('confirm_status',null)->first();
      if($request->action == 'kitchenadd')
      {
        KitchenCustomize::where('id',$request->id)->increment('quantity');
        $select_id = KitchenCustomize::where('id',$request->id)->pluck('order_id');
        Kitchen::where('id',$select_id[0])->where('confirm_status',null)->increment('item_quantity');
        $response = array(
            'status' => 'success',
          );
        return response()->json($response); 
      }
      if($request->action == 'kitchenminus')
      {
        KitchenCustomize::where('id',$request->id)->decrement('quantity');
        $select_id = KitchenCustomize::where('id',$request->id)->pluck('order_id');
        Kitchen::where('id',$select_id[0])->where('confirm_status',null)->decrement('item_quantity');
        $delete_status = 'false';
        $check_main = Kitchen::where('id',$select_id[0])->where('confirm_status',null)->pluck('item_quantity');
        if($check_main[0] == 0){
          Kitchen::where('id',$select_id[0])->where('confirm_status',null)->delete();
        }
        $check_delete = KitchenCustomize::where('id',$request->id)->pluck('quantity');
        if($check_delete[0] == 0){
          KitchenCustomize::where('id',$request->id)->delete();
          KitchenItemAddon::where('order_id',$request->id)->delete();
          $delete_status = 'true';
        }
        $response = array(
            'status' => 'success',
            'delete_status' => $delete_status,
          );
        return response()->json($response); 
      }

      if($request->action == 'kitchencustomize')
      {
        $item_id = KitchenItemAddon::where('order_id',$request->id)->distinct('item_id')->pluck('item_id');
        KitchenItemAddon::where('order_id',$request->id)->delete();
        foreach ($request->totaldata as $key => $value) {
          $new_addon = new KitchenItemAddon;
          $new_addon->item_id = $item_id[0];
          $new_addon->addon_name = $key;
          $new_addon->addon_value = $value;
          $new_addon->table_number = $table_number;
          $new_addon->order_id = $request->id;
          $new_addon->business_id = null;
          $new_addon->save();
        }

      }

      if($request->action == 'addon')
      {   
        if($ifexist == null)
        {
          $new_entry = new Kitchen;
          $new_entry->table_number = $table_number;
          $new_entry->item_id = $request->item_id;
          $new_entry->item_quantity = '1';
          $new_entry->business_id = null;
          $new_entry->save();

          $id = DB::table('dikitchen')->where('table_number',$table_number)->where('item_id',$request->item_id)->where('confirm_status',null)->first();

          $new_customize = new KitchenCustomize;
          $new_customize->order_id = $id->id;
          $new_customize->quantity = '1';
          $new_customize->business_id = null;
          $new_customize->save();
        }
        else{
          $id = DB::table('dikitchen')->where('table_number',$table_number)->where('item_id',$request->item_id)->where('confirm_status',null)->first();
          Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('item_id',$request->item_id)->increment('item_quantity');
          $new_customize = new KitchenCustomize;
          $new_customize->order_id = $id->id;
          $new_customize->quantity = '1';
          $new_customize->business_id = null;
          $new_customize->save();
        }

        $id = DB::table('dikitchen_customize')->orderBy('id','DESC')->first();

        foreach ($request->totaldata as $key => $value) {
          $new_addon = new KitchenItemAddon;
          $new_addon->item_id = $request->item_id;
          $new_addon->addon_name = $key;
          $new_addon->addon_value = $value;
          $new_addon->table_number = $table_number;
          $new_addon->order_id = $id->id;
          $new_addon->business_id = null;
          $new_addon->save();
        }
      }

      $total_items = DB::table("dikitchen")->where("table_number","=",$table_number)->where('confirm_status',null)->get()->sum("item_quantity");
      $response = array(
        'status' => 'success',
        'msg' => $total_items,
      );
      return response()->json($response); 
    }

    public function customize(Request $request){
      $table_number = Session::get('table');

      if($request->action == 'add'){
        $update = KitchenCustomize::where('id',$request->item_id)->first();
        $update->increment('quantity');
        $get_id = KitchenCustomize::where('id',$request->item_id)->pluck('order_id');
        $sum = DB::table('dikitchen_customize')->where('order_id',$get_id[0])->get()->sum('quantity');
        DB::table('dikitchen')->where('id',$get_id[0])->where('confirm_status',null)->update(['item_quantity' => $sum]);
        $total_items = DB::table("dikitchen")->where("table_number","=",$table_number)->where('confirm_status',null)->get()->sum("item_quantity");
        $check_main_item = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('id',$get_id[0])->pluck('item_quantity');
        $item_id_to_change = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('id',$get_id[0])->pluck('item_id');
        $response = array(
        'status' => 'success',
        'total_items' => $total_items,
        'item_id' => $item_id_to_change[0],
        'item_quantity' => $check_main_item[0],
      );
        return response()->json($response); 
      }

      elseif ($request->action == 'minus') {
        $update = KitchenCustomize::where('id',$request->item_id)->first();
        $update->decrement('quantity');
        $get_id = KitchenCustomize::where('id',$request->item_id)->pluck('order_id');
        $sum = DB::table('dikitchen_customize')->where('order_id',$get_id[0])->get()->sum('quantity');
        DB::table('dikitchen')->where('id',$get_id[0])->where('confirm_status',null)->update(['item_quantity' => $sum]);
        $update = KitchenCustomize::where('id',$request->item_id)->first();
        $total_items = DB::table("dikitchen")->where("table_number","=",$table_number)->where('confirm_status',null)->get()->sum("item_quantity");
        $check_main_item = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('id',$get_id[0])->pluck('item_quantity');
        $item_id_to_change = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('id',$get_id[0])->pluck('item_id');
        if($update->quantity == '0'){
          DB::table('dikitchen_customize')->where('order_id',$update->id)->delete();
          $update->delete();
          DB::table('dikitchen_item_addons')->where('order_id',$request->item_id)->delete();
          $check_main_item = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('id',$get_id[0])->pluck('item_quantity');
          $item_id_to_change = Kitchen::where('table_number',$table_number)->where('confirm_status',null)->where('id',$get_id[0])->pluck('item_id');
          if($check_main_item[0] == 0){
            DB::table('dikitchen')->where('confirm_status',null)->where('id',$get_id[0])->delete();
          }
          $response = array(
            'delete_status' => 'success',
            'total_items' => $total_items,
            'item_id' => $item_id_to_change[0],
            'item_quantity' => $check_main_item[0],
          );
        }
        else{
          $response = array(
            'delete_status' => 'no',
            'total_items' => $total_items,
            'item_id' => $item_id_to_change[0],
            'item_quantity' => $check_main_item[0],
          );
        }
        return response()->json($response); 
      }
      else{
        $table_number = Session::get('table');
      $item_details = CategoryItem::where('item_id',$request->item_id)->get();
      $kitchen_details = Kitchen::where(['item_id'=> $request->item_id,'table_number' => $table_number,'confirm_status' => null])->get();
      $kitchen_custom = KitchenCustomize::where('order_id',$kitchen_details[0]->id)->get();
      $kitchen_addon = DB::table('dikitchen_customize')->join('dikitchen_item_addons','dikitchen_customize.id','=','dikitchen_item_addons.order_id')->get();
      $total_items = DB::table("dikitchen")->where('confirm_status',null)->where("table_number","=",$table_number)->get()->sum("item_quantity");
      $response = array(
        'status' => 'success',
        'kitchen_custom' => $kitchen_custom,
        'kitchen_addon' => $kitchen_addon,
        'item_details' => $item_details,
        'total_items' => $total_items,
      );
      return response()->json($response); 
      }
    }

    public function notify()
    {
      $table_number = Session::get('table');

      $new_alert = new AlertNotification;
      $new_alert->table_number = $table_number;
      $new_alert->business_id = null;
      $new_alert->save();

      if(Session::has('qrc')){
        Kitchen::where('table_number',$table_number)->update(['confirm_status' => 'yes']);
        $response = array(
        'status' => 'no',
        'url' => 'ordersentkitchen'
      );
      return response()->json($response);
      }

      $response = array(
        'status' => 'success',
      );
      return response()->json($response);
    }

    public function getnotifications()
    {
      $notify_details = AlertNotification::all();

      return view('dinein.waiter_notifications',['notify_details' => $notify_details]);
    }

}
