<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Collection;
use App\Models\Total;
use Illuminate\Http\Response;
use Auth;

class CollectionController extends Controller
{
    //
    public $SuccessStatus = true;
    public $FailedStatus = false;

    public function collect(Request $request)
    {
        

        $collect = new Collection();
        $collect->item_id = $request->input('item');
        $collect->item_weight = $request->input('item_weight');
        $collect->price_per_kg = $request->input('price_per_kg');
        $collect->transport = $request->input('transport');
        $collect->loader = $request->input('loader');
        $collect->others = $request->input('others');
        $collect->location_id = Auth::user()->location_id;
        $collect->amount = $request->input('amount');
        $collect->user_id = Auth::id();
        $collect->save();

        $collected = (int)$request->input('item_weight');
            $locationId = Auth::user()->location_id;
            
            $t = Total::where('location_id',$locationId)->first();
            $t->update(['collected' => ($t->collected + $collected)]);
        

        return response()->json([
            "status" => $this->SuccessStatus,
            "message" => "Successfull",
            "data" => $collect,
            "total" => $t->collected
        ],200);
    }

    

    public function getCollection(Request $request)
    {
        try{
            $collect = Collection::with('location','item')->where('location_id', Auth::user()->location_id)->get();
        return response()->json([
            "status" => $this->SuccessStatus,
            "message" => "Successfull",
            "data" => $collect
        ],200);
        }catch (Exception $e) {
            return response()->json([
                'status' => $this->failedStatus,
                'msg'    => 'Error',
                'errors' => $e->getMessage(),
            ], 401);
        }
        
    }

}
