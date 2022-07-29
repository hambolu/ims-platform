<?php

namespace App\Http\Controllers;

use App\Models\Sorting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use App\Models\Total;
use DB;
use App\Http\Traits\HistoryTrait;
use Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\SortDetails;
use App\Models\BailingItem;
use App\Models\Item;
use Carbon\Carbon;

class SortingController extends Controller
{
    public $successStatus = true;
    public $failedStatus = false;

    use HistoryTrait;

    public function sorted(Request $request){
        try {
            
            $w = json_encode($request->sort_item_weight);
            $result = 0;
            foreach(json_decode($w) as $value){
                $result += $value;
            }
            $t = Total::where('location_id', Auth::user()->location_id)->first();
            if($t == null){
                return response()->json([
                    'status' => $this->failedStatus,
                    'message'    => 'No Collection Record Found',
                ],500 );
            }else{

                if($result > $t->collected){
                    return response()->json([
                        'status' => $this->failedStatus,
                        'message'    => 'Insufficent Collection',
                    ], 500);
                }
            }

                $sort = new Sorting();
                $sort->item_id = $request->item_id;
                $sort->sorting_id = json_encode($request->sort_item);
                $sort->sort_item_weight = json_encode($request->sort_item_weight);
                $sort->location_id = Auth::user()->location_id;
                $sort->user_id = Auth::id();
                //dd($sort);
                $sort->save();


                $sorted = (int)array_sum($request->sort_item_weight);
                $total = Total::where('location_id',Auth::user()->location_id)->first();
                $old_total_collected = $total->collected;
                $total->update(['collected' => ($total->collected - $sorted)]);
                $total->update(['sorted' => ($total->sorted + $sorted)]);


                $dat = BailingItem::whereIn('id', $request->sort_item)->pluck('item');
                
                $data = str_replace(" ","_",json_decode($dat));
                //dd($data);
                $dataset = array();
                $listitem = $request->sort_item;

                $listitemweight = $request->sort_item_weight;

                for ($i=0; $i<count($listitem); $i++) { 
                $dataset[$data[$i]] = $listitemweight[$i];
                }
                $tweight = array_sum($request->sort_item_weight);
                //dd($tweight);
                
                $other_value_history = [
                    'Unsorted' => ($old_total_collected - $sorted),
                    'location_id'=> Auth::user()->location_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $other_value = [
                    'location_id'=> Auth::user()->location_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];


                DB::table('sort_details_histories')->insert([
                    array_merge($dataset, $other_value_history)
                ]);
                $old_sorting = DB::table('sort_details')->where('location_id', Auth::user()->location_id)->first();

                if(empty($old_sorting)){
                    
                    DB::table('sort_details')->insert([
                        array_merge($dataset, $other_value)
                    ]);
                }else{
                    $new_dataset = array();
                    foreach($dataset as $key => $data) {
                        $new_dataset[$key] = $data + $old_sorting->$key;
                    }
                    //dd($new_dataset);
                    $updated = SortDetails::where('location_id', Auth::user()->location_id)
                               ->update($new_dataset);
                }

                // $sorted = (int)json_encode($request->sort_item);
                $locationId = Auth::user()->location_id;
                $userId = Auth::id();

                //log history
                $history = $this->allHistory($locationId,$userId);

                return  response()->json([
                    "status" => $this->successStatus,
                    "message" => "Successfull",
                    "data" => $sort
                ],200);
            } catch (Exception $e) {
                return response()->json([
                    'status' => $this->failedStatus,
                    'message'    => 'Error',
                    'errors' => $e->getMessage(),
                ], 500);
            }
    }

    public function getSorted(Request $request)
    {
        //dd(Auth::user()->location_id);
        $getSorted = Sorting::with('item','location')->where('location_id', Auth::user()->location_id)->get();
        $sorting_items = BailingItem::all();
        $total = Total::where('location_id',Auth::user()->location_id)->first();
        $items = Item::all();
        if(empty($getSorted))
        {
            return response()->json([
                "status" => $this->failedStatus,
                "message" => "No Record Found",
            ], 500);

        }else{
            return response()->json([
                "status" => $this->successStatus,
                "message" => "Successfull",
                "items" => $items,
                "sorting_items" => $sorting_items,
                "total_collected" => $total->collected
            ], 200);
        }
    }

    



    public function filter(Request $request)
    {
        $table_name = $request->input('table_name');
        $item = $request->input('item');
        $item_weight = $request->input('item_weight');
        $location = $request->input('location');
        $created_at = $request->input('created_at');
        $status = $request->input('status');
        $amount = $request->input('amount');

        $filter = DB::table('table_name')
            ->where('item', 'like', '%'.$item.'%')
            ->orWhere('item_weight', 'like', '%'.$item_weight.'%')
            ->orWhere('created_at', 'like', '%'.$created_at.'%')
            ->orWhere('status', 'like', '%'.$status.'%')
            ->orWhere('amount', 'like', '%'.$amount.'%')
            ->orWhere('userId', 'like', '%'.$userId.'%')
            ->get();
            return [
                "status" => $this->successStatus,
                "data" => $filter
            ];

    }
}
