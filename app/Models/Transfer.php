<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Transfer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "from",
        "to",
        "item",
        "item_weight",
        "status",
        "rej_reason",
        "user_id",
        ];
    
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }
    public function bailed(): string
    {
        $it = array();
        $items = DB::table('bailing_items')->whereIn('id',json_decode($this->transfer_item))->get();
       
        foreach($items as $t){
            $it[] = $t->item;
        }
        return implode(", ",$it);
    }
}
