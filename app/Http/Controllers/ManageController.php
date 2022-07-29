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

class ManageController extends Controller
{
    //
    public function createRole(Request $request)
    {
        
            $role = new UserRole();
            $role->name = $request->input('name');
            $role->user_id = Auth::id();
            $role->save();

            return back()->with('message', 'Role Created Successfully'); 
    }
    public function roleList()
    {
        $role = UserRole::all();
        return view('manage/role',compact('role'));
    }
    public function roleEdit($id)
    {
        $role = UserRole::find($id);
        return view('manage/role_edit',compact('role'));
    }
    public function roleEditUpdate(Request $request, $id)
    {
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        return redirect('manage/role')->with('message', 'Role Updated Successfully');
    }
    public function roleDelete($id){
        $role = UserRole::find($id);
        $role->delete();
        return redirect('manage/role')->with('message', 'Role Deleted Successfully');
    }






    public function deleteCollection($id)
    {
        $items = Collection::find($id);
        $items->delete();
        return back()->with('message', 'Deleted Successfully');
    }

    public function deleteSorting($id)
    {
        $items = Sorting::find($id);
        $dat = BailingItem::whereIn('id', $request->sort_item)->pluck('item');
        $sorting_details = SortingDetails::where('location_id',$item->location_id)
                        ->where('location_id',$item->location_id)
                        ->first();
        $items->delete();
        return back()->with('message', 'Deleted Successfully');
    }


}
