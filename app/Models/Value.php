<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    use HasFactory;
    /**
     * @var array
     */
    protected $guarded = ["id"];

    public function properties(){
        return $this->hasMany(Property::class);
    }

    public function property(){
        return $this->belongsTo(Property::class);
    }
}
