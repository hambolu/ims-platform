<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;
use App\Models\User;
use App\Models\Total;
use App\Models\History;
use App\Models\Location;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Sales;
use App\Models\Factory;
use App\Models\Transfer;
use App\Models\BailingItem;
use App\Models\Sorting;
use App\Models\Bailing;
use App\Models\Recycle;
use App\Models\UserRole;
use App\Models\FactoryTotal;
use App\Models\SortDetailsHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Session;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Http\Traits\HistoryTrait;
use App\Models\SortDetails;
use App\Models\BailedDetails;
use App\Models\BailedDetailsHistory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MainController extends Controller
{
    //
    use HistoryTrait;
    
    public function signin(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::attempt($credentials)) {
            
 
            $user = User::where("id",Auth::id())->get();
            
            return redirect('dashboard')->with('status', 'Profile updated!');
        }else{
            return back()->with('error','Invalid Credentials');
        }
        
    }
    public function collect(Request $request)
    {
        

        $collect = new Collection();
        $collect->item_id = $request->input('item');
        $collect->item_weight = $request->input('item_weight');
        $collect->location_id = $request->input('location');
        $collect->amount = $request->input('amount');
        $collect->user_id = Auth::id();
        $collect->save();

        $collected = $request->input('item_weight');
            $locationId = $request->input('location');
            
            $total = Total::where('location_id',$locationId)->first();
            //dd($t->collected + $collected);
            if (!empty($total) ) {
                $total->update(['collected' => ($total['collected'] + $collected)]);
            }else{
                $create = new Total();
                $create->location_id = $request->input('location');
                $create->collected = $collected;
                $create->save();
            }


            return back()->with('message', 'Collection Created Successfully');
    }
    public function viewCollect()
    {
        $item = Item::all();
        $center = Location::all();
        $collections = Collection::all();
        return view('addCollection',compact('center', 'item','collections'));
    }




    public function logout() {
        Session::flush();
        
        Auth::logout();

        return redirect('/');
    }
    
    public function dashboard()
    {
        $users = User::select(\DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month_name"))
        ->whereYear('created_at', date('Y'))
        ->groupBy('month_name')
        ->orderBy('created_at', 'asc')
        ->pluck('count','month_name');

        $labels = $users->keys();
        $data = $users->values();
        $locations = Location::all()->count();
        $location = Location::all();
        $totals = Total::all();
        $items = Item::all()->count();
        $collections = Collection::all();
        $tcollect = Collection::all()->count();
        $staffs = User::where('role_id',2)->count();
        $sales = Sales::all()->sum('amount');
        $users = User::all()->count();
        $factory = Factory::all()->count();
        


        return view('dashboard',compact('location','factory','locations','labels','collections','totals','items','tcollect','staffs','users','sales'));
    }

    public function createUser(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string',
            'location_id' => 'required|string',
            'role_id' => 'required|string',
            'factory_id' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return back()->with('error', $validator->errors());
        }
        //dd($validator->validated());
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => Hash::make($request->password)]
                ));
                
        return back()->with('message', 'User Created Successfully');
    }
    
     public function user_edit($id)
    {
        $users = User::find($id);
        $collection = Location::all();
        $factory = Factory::all();
        $role = UserRole::all();
        return view('user_edit',compact('users','collection','factory','role'));
    }
    public function userDelete($id)
    {
        $users = User::find($id);
        $users->delete();
        return redirect('/users')->with('message', 'User Deleted Successfully');
    }
    public function userEdit(Request $request, $id)
    {
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->location_id = $request->location_id;
        $user->role_id = $request->role_id;
        $user->factory_id = $request->factory_id;
        $user->password = Hash::make($request->password);
        $user->save();
        
       
        return redirect('/users')->with('message', 'User Updated Successfully');
    }
    public function users()
    {
        $users = User::all();
        $collection = Location::all();
        $factory = Factory::all();
        $roles = UserRole::all();
        //dd($roles);
        return view('users',compact('users','collection','factory','roles'));
    }

    public function locations()
    {
        $location = Location::all();
        return view('locations',compact('location'));
    }
    public function factory()
    {
        $factory = Factory::all();
        return view('factory',compact('factory'));
    }

    public function createItem(Request $request)
    {
        
            $items = new Item();
            $items->item = $request->input('item');
            $items->user_id = Auth::id();
            $items->save();

            return back()->with('message', 'Item Created Successfully'); 
    }
    public function createBailingItem(Request $request)
    {
        
        //dd(SortDetails::all());
            $items = new BailingItem();
            $items->item = $request->input('bailing_item');
            $items->items_id = $request->input('item_id');
            $items->user_id = Auth::id();
            $items->save();

            
            $data = BailingItem::all();
            $col = $request->input('bailing_item');
              
                if (Schema::hasColumn('sort_details', str_replace(" ","_",$col))){
                    // do something
                }else{
                    Schema::table('sort_details', function(Blueprint $table) use ($col){
                        $table->string(str_replace(" ","_",$col))->default('0')->after('id');
                    });
                }
            
            
            if (Schema::hasColumn('sort_details_histories', str_replace(" ","_",$col))){
                // do something
            }else{
                    Schema::table('sort_details_histories', function(Blueprint $table) use ($col){
                        $table->string(str_replace(" ","_",$col))->default('0')->after('id');
                    });
                }
            
               
                if (Schema::hasColumn('bailed_details', str_replace(" ","_",$col))){
                    // do something
                }else{
                    Schema::table('bailed_details', function(Blueprint $table) use ($col){
                        $table->string(str_replace(" ","_",$col))->default('0')->after('id');
                    });
                }
            
              
                if (Schema::hasColumn('bailed_details_histories', str_replace(" ","_",$col))){
                    // do something
                }else{
                    Schema::table('bailed_details_histories', function(Blueprint $table) use ($col){
                        $table->string(str_replace(" ","_",$col))->default('0')->after('id');
                    });
                }
                if (Schema::hasColumn('transfer_details_histories', str_replace(" ","_",$col))){
                    // do something
                }else{
                    Schema::table('transfer_details_histories', function(Blueprint $table) use ($col){
                        $table->string(str_replace(" ","_",$col))->default('0')->after('id');
                    });
                }
                if (Schema::hasColumn('transfer_details', str_replace(" ","_",$col))){
                    // do something
                }else{
                    Schema::table('transfer_details', function(Blueprint $table) use ($col){
                        $table->string(str_replace(" ","_",$col))->default('0')->after('id');
                    });
                }
            
            return back()->with('message', 'Bailing Item Created Successfully');
    }
    public function bailingList(){
        $items = BailingItem::all();
        $mainItems = Item::all();
        return view('bailing_item',compact('items','mainItems'));
    }
    public function itemList()
    {
        $items = Item::all();
        return view('item',compact('items'));
    }

    public function itemEdit($id)
    {
        $item = Item::find($id);
        return view('item_edit',compact('item'));
    }
    public function itemEditUpdate(Request $request, $id)
    {
        $items = Item::find($id);
        $items->item = $request->input('item');
        $items->save();

        return redirect('/item')->with('message', 'Item Updated Successfully');
    }
    public function itemDelete($id){
        $items = Item::find($id);
        $items->delete();
        return redirect('/item')->with('message', 'Item Deleted Successfully');
    }

    public function sortedDelete($id){
        $item = Sorting::find($id);
        //dd($item);
        $item->delete();
        return back()->with('message', 'Sorting Deleted Successfully');
    }

    public function bailedEdit($id)
    {
        $item = BailingItem::find($id);
        return view('bailing_item_edit',compact('item'));
    }
    public function bailItemEditUpdate(Request $request, $id)
    {
        $items = BailingItem::find($id);
        $items->item = $request->input('bailing_item');
        $items->save();

        return redirect('/bailing_item')->with('message', 'Bailed Item Updated Successfully');
    }
    public function bailedDelete($id){
        $items = BailingItem::find($id);
        $items->delete();
        return redirect('/bailing_item')->with('message', 'Bailed Item Deleted Successfully');
    }

    public function sorted(Request $request){
        try {
            
            $w = json_encode($request->sort_item_weight);
            $result = 0;
            foreach(json_decode($w) as $value){
                $result += $value;
            }
            $t = Total::where('location_id', $request->input('location'))->first();
            if($t == null){
                return back()->with('error', 'No Collection Record Found'); 
            }else{

                if($result > $t->collected){
                    return back()->with('error', 'Insufficent Balance'); 
                }
            }

                $sort = new Sorting();
                $sort->item_id = $request->input('item');
                $sort->sorting_id = json_encode($request->sort_item);
                // if(array_unique($request->sort_item) === $request->sort_item){
                //     return back()->with('error', 'Duplicate Sorting Items');
                // }
                $sort->sort_item_weight = json_encode($request->sort_item_weight);
                $sort->location_id = $request->input('location');
                $sort->user_id = Auth::id();
                //dd($sort);
                $sort->save();


                $sorted = (int)array_sum($request->sort_item_weight);
                $total = Total::where('location_id',$request->input('location'))->first();
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
                    'location_id'=> $request->input('location'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $other_value = [
                    'location_id'=> $request->input('location'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                //dd($dataset);
                DB::table('sort_details_histories')->insert([
                    array_merge($dataset, $other_value_history)
                ]);
                $old_sorting = DB::table('sort_details')->where('location_id', $request->input('location'))->first();
                //dd(empty($old_sorting));
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
                    $updated = SortDetails::where('location_id', $request->input('location'))
                               ->update($new_dataset);
                }
                


                // $sorted = (int)json_encode($request->sort_item);
                $locationId = $request->location;
                $userId = Auth::id();

                //log history
                $history = $this->allHistory($locationId,$userId);

                return back()->with('message', 'Sorting Created Successfully'); 
        } catch (Exception $e) {
            return back()->with('error', 'Error'); 
        }
    }

    public function bailed(Request $request)
    {
        try{
        $w = json_encode($request->item_weight);
            $result = 0;
            foreach(json_decode($w) as $value){
                $result += $value;
            }
            $t = Total::where('location_id', $request->input('location'))->first();
            if($t == null){
                return back()->with('error', 'No Collection Record Found');
            }else{

                if($result > $t->sorted){
                    return back()->with('error', 'Insufficent Sorting Balance');
                }
            }

                $bailing = new Bailing();
                $bailing->bailingItem_id = json_encode($request->bail_item);
                if(array_unique($request->bail_item) === $request->bail_item){
                    return back()->with('error', 'Duplicate Bailing Items');
                }
                $bailing->item_weight = json_encode($request->item_weight);
                $bailing->location_id = Auth::user()->location_id;
                $bailing->user_id = Auth::id();
                //dd($bailing);
                $bailing->save();


                $bailed = (int)array_sum($request->item_weight);
                $total = Total::where('location_id',$request->input('location'))->first();
                $old_total_bailed = $total->bailed;
                $total->update(['bailed' => ($total->bailed + $bailed)]);
                $total->update(['sorted' => ($total->sorted - $bailed)]);


                $dat = BailingItem::whereIn('id', $request->bail_item)->pluck('item');
                
                $data = str_replace(" ","_",json_decode($dat));
                //dd($data);
                $dataset = array();
                $listitem = $request->bail_item;

                $listitemweight = $request->item_weight;

                for ($i=0; $i<count($listitem); $i++) { 
                $dataset[$data[$i]] = $listitemweight[$i];
                }
                $tweight = array_sum($request->item_weight);
                //dd($tweight);
                
                $other_value_history = [
                    'Unsorted' => ($old_total_bailed - $bailed),
                    'location_id'=> $request->input('location'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $other_value = [
                    'location_id'=> $request->input('location'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                //dd($dataset);
                DB::table('bailed_details_histories')->insert([
                    array_merge($dataset, $other_value_history)
                ]);
                $old_sorting = DB::table('bailed_details')->where('location_id', $request->input('location'))->first();
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
                    $updated = BailedDetails::where('location_id', $request->input('location'))
                               ->update($new_dataset);
                }

                // $sorted = (int)json_encode($request->sort_item);
                $locationId = $request->location;
                $userId = Auth::id();

                //log history
                $history = $this->allHistory($locationId,$userId);
                return back()->with('message', 'Sorting Created Successfully'); 
        } catch (Exception $e) {
            return back()->with('error', 'Error'); 
        }

    }
    public function transferd(Request $request)
    {
        try{
        $w = json_encode($request->item_weight);
            $result = 0;
            foreach(json_decode($w) as $value){
                $result += $value;
            }
            $t = Total::where('location_id', $request->input('collection_id'))->first();
            if($t == null){
                return back()->with('error', 'No Collection Record Found');
                
            }else{

                if($result > $t->bailed){
                    return back()->with('error', 'Insufficent Transferd Record');
                    
                }
            }
                //dd($request->transfer_item[0],$request->transfer_item[0]);
                $transfer = new Transfer();
                $transfer->transfer_item = json_encode($request->transfer_item);
                
                $transfer->item_weight = json_encode($request->item_weight);
                $transfer->location_id = $request->collection_id;
                $transfer->factory_id = $request->factory_id;
                $transfer->collection_id = $request->collection_id;
                $transfer->user_id = Auth::id();
                $transfer->status = 0;
                //dd($transfer);
                $transfer->save();


                $transfered = (int)array_sum($request->item_weight);
                $total = Total::where('location_id',$request->collection_id)->first();
                $old_total_transfered = $total->transfered;
                $total->update(['transfered' => ($total->transfered + $transfered)]);
                $total->update(['bailed' => ($total->sorted - $transfered)]);


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
                    'location_id'=> $request->location_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $other_value = [
                    'location_id'=> $request->collection_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                //dd($dataset);
                DB::table('bailed_details_histories')->insert([
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

                // $sorted = (int)json_encode($request->sort_item);
                $locationId = $request->collection_id;
                $userId = Auth::id();

                //log history
                $history = $this->allHistory($locationId,$userId);
                return back()->with('message', 'Transferd Created Successfully'); 
        } catch (Exception $e) {
            return back()->with('error', 'Error'); 
        }

    }
    public function sorting()
    {
        
        $item = Item::all();
        $bailingItems = BailingItem::all();
        //dd($bailingItems);
        $collection = Location::all();
        $sorting = Sorting::all();
        //dd($sorting);
        $bail = SortDetailsHistory::all();
        

        

        return view('sorting',compact('bailingItems','item','collection','sorting','bail'));
    }
    public function bailing()
    {
        
        $item = Item::all();
        $bailingItems = BailingItem::all();
        //dd($bailingItems);
        $collection = Location::all();
        $sorting = Bailing::all();
        //dd($sorting);
        $bail = SortDetailsHistory::all();
        

        

        return view('bailing',compact('bailingItems','item','collection','sorting','bail'));
    }
    public function transfering()
    {
        
        $item = Item::all();
        $bailingItems = BailingItem::all();
        //dd($bailingItems);
        $collection = Location::all();
        $factory = Factory::all();
        $transfer = Transfer::all();
        //dd($sorting);
        $bail = SortDetailsHistory::all();
        

        

        return view('transfer',compact('bailingItems','item','collection','transfer','bail','factory'));
    }
    public function viewsorting($id)
    {

        // $items = Sorting::find($id);
        // //dd(json_decode(explode(", ",$items->sort_item_weight)));
        // $itemData = json_decode($items->sort_item_weight);
        // $data = BailingItem::whereIn('id', json_decode($items->sorting_id))->pluck('item');
        // $col = str_replace(" ","_",$data);
        // //dd(array_values($itemData));

        // $itemData = DB::table('sort_details')->select(array_values(json_decode($col)))
        //                 ->where('location_id',$items->location_id)
        //                 ->first();
        // $itemData->update([$col - $itemData ]);
        
        // dd($itemData);
       
        
        // $arr1 = $itemData;
        // $arr2 = $col;
        
        // $subtracted = array_map(function ($x, $y) { return $y-$x; } , $arr1, $arr2);
        // $result     = array_combine(array_keys($arr1), $subtracted);

        // dd($result);




        $bailingItems = BailingItem::all();
        $sort = Sorting::find($id);
        $result = 0;
        foreach(json_decode($sort->sort_item_weight) as $value){
            $result += $value;
        }
         //dd($result);
         $bail = SortDetailsHistory::all();
         $bail = BailingItem::select('item')->pluck('item');

         $dd = array_values(json_decode(str_replace(" ","_",$bail)));
         $se = SortDetailsHistory::select($dd,'Unsorted')->where('location_id',$sort->location_id)->get();
         $se2 = SortDetailsHistory::select($dd,'Unsorted')->get();
        //  $table = DB::getSchemaBuilder()->getColumnListing('sort_details_histories');
        //     dd($table);
         
         $locations = Location::all()->count();
         $location = Location::all();
         $totals = Total::where('location_id',$sort->location_id)->first();
        
        return view('viewSortingDetails',compact('sort','bailingItems','result','bail','dd','se','totals'));
    }
    public function createFactory(Request $request)
    {
        $location = new Factory();
        $location->name = $request->input('name');
        $location->address = $request->input('address');
        $location->city = $request->input('city');
        $location->state = $request->input('state');
        $location->user_id = Auth::id();
        $location->save();



        return back()->with('message', 'Factory Created Successfully'); 
    }
    
    
    public function location(Request $request)
    {
        $location = new Location();
        $location->name = $request->input('name');
        $location->address = $request->input('address');
        $location->city = $request->input('city');
        $location->state = $request->input('state');
        $location->user_id = Auth::id();
        $location->save();

        return back()->with('message', 'Location Created Successfully'); 
    }
    public function report(){
        $report = History::all();
        return view('report',compact('report'));
    }

    public function recycle(Request $request){
        $recycle = Recycle::create([
            "item_weight_input" => $request->item_weight_input,
            "item_weight_output" => $request->item_weight_output,
            "factory_id"    => $request->factory_id,
            "user_id" => Auth::id(),
            
        ]);

        $recycled = $request->item_weight_output;

        $factory_id = $request->factory_id;

        if (FactoryTotal::where('factory_id',$factory_id)->exists()) {
            # code...
            $t = FactoryTotal::where('factory_id',$factory_id)->first();
            $t->update(['recycled' => ($t->recycled + $recycled)]);
        }else{
            $total = new  FactoryTotal();
            $total->recycled = $recycled;
            $total->factory_id = $request->factory_id;
            $total->save();
        }
            

        return back()->with('message', 'Recycle Created Successfully');

    }
    public function recycled(Request $request)
    {
        $recycled = Recycle::all();
        $factory = Factory::all();
        return view('recycle',compact('recycled','factory'));
    }

    public function sales(Request $request){
        try{
            $sales = new Sales();
            $sales->item_weight = $request->item_weight;
            $sales->amount = $request->amount;
            $sales->factory_id = $request->factory_id;
            $sales->user_id = Auth::id();
            $sales->save();

            $sales = $request->amount;
            $weight = $request->item_weight;
            $recycled = $request->item_weight_output;

            $factory_id = $request->factory_id;

            if (FactoryTotal::where('factory_id',$factory_id)->exists()) {
                # code...
                $t = FactoryTotal::where('factory_id',$factory_id)->first();
                $t->update(['sales' => ($t->sales + $sales)]);
                $t->update(['recycled' => ($t->recycled - $weight)]);
            }else{
                $total = new  FactoryTotal();
                $total->sales = $sales;
                $total->factory_id = $request->factory_id;
                $total->save();
            }
            return back()->with('message', 'Sales Created Successfully');

        }catch (Exception $e) {
            return response()->json([
                'status' => $this->failedStatus,
                'message'    => 'Error',
                'errors' => $e->getMessage(),
            ], 401);
        }
        

    }

    public function salesp()
    {
        $recycled = Recycle::all();
        $sales = Sales::all();
        $factory = Factory::all();
        return view('sales',compact('recycled','sales','factory'));
    }

    public function collectionFilter(Request $request)
    {
        
           $start_date = Carbon::parse($request->start_date)
                                 ->toDateTimeString();
    
           $end_date = Carbon::parse($request->end_date)
                                 ->toDateTimeString();
                                 
            $collection = Total::whereBetween('created_at', [
                $start_date, $end_date
              ])->orWhere('location_id', $request->location_id)
              ->paginate(50);
              $location = Location::all();
           return view('collection_report',compact('collection','location'));
    }

    public function collection_filter()
    {
        $collection = Total::paginate(50);
        $location = Location::all();
        return view('collection_report',compact('collection','location'));
    }

    public function sortedFilter(Request $request)
    {
           $start_date = Carbon::parse($request->start_date)
                                 ->toDateTimeString();
    
           $end_date = Carbon::parse($request->end_date)
                                 ->toDateTimeString();
    
            $sorting = Sorting::whereBetween('created_at', [
                $start_date, $end_date
              ])->orWhere('location_id', $request->location)->paginate(50);
              $collection = Location::all();
           return view('sorting_report',compact('sorting','collection'));
    }

    public function sorted_filter()
    {
        $sorting = Sorting::paginate(50);
        $collection = Location::all();
        return view('sorting_report',compact('sorting','collection'));
    }

    public function bailedFilter(Request $request)
    {
           $start_date = Carbon::parse($request->start_date)
                                 ->toDateTimeString();
    
           $end_date = Carbon::parse($request->end_date)
                                 ->toDateTimeString();
    
            $bailed = Bailing::whereBetween('created_at', [
                $start_date, $end_date
              ])->orWhere('location_id', $request->location)->paginate(50);
              $collection = Location::all();
           return view('bailed_report',compact('bailed','collection'));
    }

    public function bailed_filter()
    {
        $bailed = Bailing::paginate(50);
        $collection = Location::all();
        return view('bailed_report',compact('bailed','collection'));
    }

    public function transferFilter(Request $request)
    {
           $start_date = Carbon::parse($request->start_date)
                                 ->toDateTimeString();
    
           $end_date = Carbon::parse($request->end_date)
                                 ->toDateTimeString();
    
            $transfered = Transfer::whereBetween('created_at', [
                $start_date, $end_date
              ])->orWhere('location_id', $request->location)
              ->orWhere('factory_id', $request->factory)
                ->paginate(50);
              $collection = Location::all();
              $result = 0;
              $factory = Factory::all();
           return view('transfered_report',compact('transfered','collection','factory'));
    }

    public function transfer_filter()
    {
        $result = 0;
        $transfered = Transfer::paginate(50);
        $collection = Location::all();
        $factory = Factory::all();
        return view('transfered_report',compact('transfered','collection','factory'));
    }

    public function recycleFilter(Request $request)
    {
           $start_date = Carbon::parse($request->start_date)
                                 ->toDateTimeString();
    
           $end_date = Carbon::parse($request->end_date)
                                 ->toDateTimeString();
    
            $recycled = Recycle::whereBetween('created_at', [
                $start_date, $end_date
              ])->orWhere('factory_id', $request->factory)
                ->paginate(50);
              $collection = Location::all();
              $factory = Factory::all();
           return view('recycled_report',compact('recycled','collection','factory'));
    }

    public function recycle_filter()
    {
        $recycled = Recycle::paginate(50);
        $collection = Location::all();
        $factory = Factory::all();
        return view('recycled_report',compact('recycled','collection','factory'));
    }

    public function salesFilter(Request $request)
    {
           $start_date = Carbon::parse($request->start_date)
                                 ->toDateTimeString();
    
           $end_date = Carbon::parse($request->end_date)
                                 ->toDateTimeString();
    
            $sales = Sales::whereBetween('created_at', [
                $start_date, $end_date
              ])->orWhere('factory_id', $request->factory)
                ->paginate(50);
              $collection = Location::all();
              $factory = Factory::all();
           return view('sales_report',compact('sales','collection','factory'));
    }

    public function sales_filter()
    {
        $sales = Sales::paginate(50);
        $collection = Location::all();
        $factory = Factory::all();
        return view('sales_report',compact('sales','collection','factory'));
    }
}
