<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Bailing extends Model
{
    use HasFactory;

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // public function bailed()
    // {
    //     $b = BailingItem::where('id',$this->bailingItem_id)->first();
    //     return $b->item;
    // }
    public function bailed(): string
    {
        $it = array();
        $items = DB::table('bailing_items')->whereIn('id',json_decode($this->bailingItem_id))->get();
       
        foreach($items as $t){
            $it[] = $t->item;
        }
        return implode(", ",$it);
    }
}
