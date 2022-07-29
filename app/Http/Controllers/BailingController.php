<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bailing;
use App\Models\Total;
use Illuminate\Http\Response;
use Auth;
use App\Models\BailingItem;
use App\Models\BailedDetails;
use App\Models\SortDetails;
use Carbon\Carbon;
use DB;
use App\Http\Traits\HistoryTrait;
use App\Models\Item;

class BailingController extends Controller
{
    //
    use HistoryTrait;
    public $successStatus = true;
    public $failedStatus = false;
    
    public function getBailing(Request $request)
    {
        $t = Total::where('location_id', Auth::user()->location_id)->first();
        $bailing_items = BailingItem::all();
        $getBailing = Bailing::where('location_id', Auth::user()->location_id)->get();
        $sorted = SortDetails::where('location_id', Auth::user()->location_id)->first();
        $items = Item::all();
        return response()->json([
            "status" => $this->successStatus,
            "message" => "Successfull",
            "items" => $items,
            "sorted_breakdown" => $sorted,
            "bailing_item" => $bailing_items,
            "total_sorted" => $t->sorted
        ],200);
    }

    public function bailing(Request $request)
    {
        try {
            

            $w = json_encode($request->item_weight);
            $result = 0;
            foreach(json_decode($w) as $value){
                $result += $value;
            }
            $t = Total::where('location_id', Auth::user()->location_id)->first();
            if(empty($t)){
                return response()->json([
                    'status' => $this->failedStatus,
                    'message'    => 'No Collection Record Found',
                ],500 );
            }else{

                if($result > $t->sorted){
                    return response()->json([
                        'status' => $this->failedStatus,
                        'message'    => 'Insufficent Sorted Record',
                    ], 500);
                }
            }

                $bailing = new Bailing();
                $bailing->bailingItem_id = json_encode($request->sort_item);
                $bailing->item_weight = json_encode($request->item_weight);
                $bailing->location_id = Auth::user()->location_id;
                $bailing->user_id = Auth::id();
                //dd($bailing);
                $bailing->save();


                $bailed = (int)array_sum($request->item_weight);
                $total = Total::where('location_id',Auth::user()->location_id)->first();
                $old_total_bailed = $total->bailed;
                $total->update(['bailed' => ($total->bailed + $bailed)]);
                $total->update(['sorted' => ($total->sorted - $bailed)]);


                $dat = BailingItem::whereIn('id', $request->sort_item)->pluck('item');
                
                $data = str_replace(" ","_",json_decode($dat));
                //dd($data);
                $dataset = array();
                $listitem = $request->sort_item;

                $listitemweight = $request->item_weight;

                for ($i=0; $i<count($listitem); $i++) { 
                $dataset[$data[$i]] = $listitemweight[$i];
                }
                $tweight = array_sum($request->item_weight);
                //dd($tweight);
                
                $other_value_history = [
                    'Unsorted' => ($old_total_bailed - $bailed),
                    'location_id'=> Auth::user()->location_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $other_value = [
                    'location_id'=> Auth::user()->location_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                //dd($dataset);
                DB::table('bailed_details_histories')->insert([
                    array_merge($dataset, $other_value_history)
                ]);
                $old_sorting = DB::table('bailed_details')->where('location_id', Auth::user()->location_id)->first();
                //dd(empty($old_sorting));
                if(empty($old_sorting)){
                    
                    DB::table('bailed_details')->insert([
                        array_merge($dataset, $other_value)
                    ]);
                }else{
                    $new_dataset = array();
                    foreach($dataset as $key => $data) {
                        $new_dataset[$key] = $data + $old_sorting->$key;
                    }
                    //dd($new_dataset);
                    $updated = BailedDetails::where('location_id', Auth::user()->location_id)
                               ->update($new_dataset);
                }

                // $sorted = (int)json_encode($request->sort_item);
                $locationId = $request->location;
                $userId = Auth::id();

                //log history
                $history = $this->allHistory($locationId,$userId);
                $bailing_items = BailingItem::all();
                return response()->json([
                    "status" => $this->successStatus,
                    // "data" => $bailing,
                    // "total" => $t->bailed,
                    "message" => "Successfull",
                    "bailing_item" => $bailing_items,
                    // "total_sorted" => $t->sorted
                ],200);

        } catch (Exception $e) {
            return response()->json([
                'status' => $this->failedStatus,
                'message'    => 'Error',
                'errors' => $e,
            ], 401);
        }
    }
}
