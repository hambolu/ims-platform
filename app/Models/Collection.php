<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    // public function item()
    // {
    //     $it = Item::where('id',$this->item)->first();
    //     return $it->item;
    // }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
}
