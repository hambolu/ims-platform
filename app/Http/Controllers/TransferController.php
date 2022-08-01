<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\History;
use App\Models\BailedDetails;
use App\Models\Total;
use App\Models\Factory;
use App\Models\BailingItem;
use App\Models\User;
use App\Models\Item;
use Auth;
use Carbon\Carbon;
use DB;
use App\Http\Traits\HistoryTrait;
use Illuminate\Support\Facades\Http;
use App\Models\TransferDetailsHistory;

class TransferController extends Controller
{
    //
    use HistoryTrait;

    public $successStatus = true;
    public $failedStatus = false;

    public function getTransfer(Request $request)
    {
        $transfer = Transfer::with('factory','location')->where('location_id', Auth::user()->location_id)->get();
        $total = Total::where('location_id',Auth::user()->location_id)->first();
        $bailed_details = BailedDetails::where('location_id',Auth::user()->location_id)->first();
        $factory = Factory::all();
        $items = Item::all();
        $dateS = Carbon::now()->startOfMonth()->subMonth(3);
        $dateE = Carbon::now();
        $transfer_history = Transfer::with('factory','location')->where('location_id',Auth::user()->location_id)
                                ->orWhere('factory_id',Auth::user()->factory_id)
                                ->whereBetween('created_at',[$dateS,$dateE])
                                ->get();
        $transfer_item = BailingItem::all();
        return response()->json([
            "status" => $this->successStatus,
            "bailed" => $total->transfered,
            "bailed_breakdown" => $bailed_details,
            "factory" => $factory,
            "items" => $items,
            "transfer_item" => $transfer_item,
            "transfer_history"  => $transfer_history,

        ],200);
    }
    public function getTransferHistory(Request $request)
    {
        $transfer = Transfer::with('factory','location')->where('location_id', Auth::user()->location_id)->get();
        $bailed_details = BailedDetails::where('location_id', Auth::user()->location_id)->get();
        $factory = Factory::all();
        return response()->json([
            "status" => $this->successStatus,
            "data" => $transfer
            
        ],200);
    }

    public function transfer(Request $request){
        
        
        try{
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
                    ], 500);
                    
                }else{
    
                    if($result > $t->bailed){
                        return response()->json([
                            'status' => $this->failedStatus,
                            'message'    => 'Insufficent Bailed Record',
                        ], 500);
                    }
                }

                    $transfer = new Transfer();
                    $transfer->transfer_item = json_encode($request->transfer_item);
                    
                    $transfer->item_weight = json_encode($request->item_weight);
                    $transfer->location_id = Auth::user()->location_id;
                    $transfer->factory_id = $request->factory_id;
                    $transfer->collection_id = Auth::user()->location_id;
                    $transfer->user_id = Auth::id();
                    $transfer->status = 0;
                    //dd($transfer);
                    $transfer->save();
    
    
                    $transfered = array_sum($request->item_weight);
                    $total = Total::where('location_id',Auth::user()->location_id)->first();
                    $old_total_transfered = $total->transfered;
                    $total->update(['transfered' => ($total->transfered + $transfered)]);
                    $total->update(['bailed' => ($total->bailed - $transfered)]);
    
    
                    $dat = BailingItem::whereIn('id', $request->transfer_item)->pluck('item');
                    
                    $data = str_replace(" ","_",json_decode($dat));
                    //dd($data);
                    $dataset = array();
                    $listitem = $request->transfer_item;
    
                    $listitemweight = $request->item_weight;
    
                    for ($i=0; $i<count($listitem); $i++) { 
                    $dataset[$data[$i]] = $listitemweight[$i];
                    }
                    $tweight = array_sum($request->item_weight);
                    //dd($tweight);
                    
                    $other_value_history = [
                        'Unsorted' => ($old_total_transfered - $transfered),
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
                    DB::table('transfer_details_histories')->insert([
                        array_merge($dataset, $other_value_history)
                    ]);
                    $old_transfer = DB::table('transfer_details')->where('location_id', $request->collection_id)->first();
                    //dd(empty($old_sorting));
                    if(empty($old_sorting)){
                        
                        DB::table('transfer_details')->insert([
                            array_merge($dataset, $other_value)
                        ]);
                    }else{
                        $new_dataset = array();
                        foreach($dataset as $key => $data) {
                            $new_dataset[$key] = $data + $old_transfer->$key;
                        }
                        //dd($new_dataset);
                        $updated = BailedDetails::where('location_id', $request->collection_id)
                                   ->update($new_dataset);
                    }
    
                   
                    $locationId = $request->collection_id;
                    $userId = Auth::id();
                    $notification_id = User::where('factory_id',Auth::user()->factory_id)
                        ->whereNotNull('device_id')
                        ->pluck('device_id');
                    if (!empty($notification_id)) {
                        
                        $factory = Factory::where('id',Auth::user()->factory_id)->first();
                        $response = Http::withHeaders([
                            'Authorization' => ' key=AAAAva2Kaz0:APA91bHSiOJFPwd-9-2quGhhiyCU263oFWWrnYKtmuF1jGmDSMBHWiFkGy3tiaP3bLhJNMy9ki0YY061y5riGULckZtBkN9WkDZGX5X9HN60a2NvwHFR8Yevnat_zHzomC5O7AkdYwT8',
                            'Content-Type' => 'application/json'
                        ])->post('https://fcm.googleapis.com/fcm/send', [
                            "registration_ids" => $notification_id,
                                "message" => [
                                        "notification" => [
                                            "title" => "Transfer notification",
                                            "body" => "Incomming Transfer from ".$factory->name
                                        ]
                                ],
                        ]);
                        $notification = $response->json('results');
                    }
                    //log history
                    $history = $this->allHistory($locationId,$userId);
                   
                    return response()->json([
                        "status" => $this->successStatus,
                        "message" => "Successfull",
                        "data" => $transfer,
                        "total" => $t->transfered,
                        "total_bailed" => $t->bailed,
                        "notification" => $notification
                    ],200);
            
            } catch (Exception $e) {
                return response()->json([
                    'status' => $this->failedStatus,
                    'message'    => 'Error',
                    'errors' => $e->getMessage(),
                ], 500);
            }
        

    }
    public function updateTransfer(Request $request)
    {
        $transfer = Transfer::with('factory','location')
                    ->where('location_id', Auth::user()->location_id)
                    ->where('id', $request->id)
                    ->first();
        $transfer->update(['status' => $request->status]);
        $transfer->update(['comments' => $request->comments]);
        return response()->json([
            "status" => $this->successStatus,
            "message" => "Successfull",
            "data" => $transfer
        ],200);
    }
    public function history(Request $request)
    {
        $history = History::all();
        return [
            "status" => $this->successStatus,
            "data" => $history
        ];
    }

   
}
