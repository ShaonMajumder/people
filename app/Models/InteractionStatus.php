<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteractionStatus extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $guarded = ["id"];
    
    public static $STATUS_INSERTED_IN_SYSTEM    = 1;
    public static $STATUS_DISCOVERED_VIA_SOCIAL_ID   = 2;
    public static $STATUS_RETURN_TO_MERCHANT 	= 3;
    public static $STATUS_CANCEL                = 4;
    public static $STATUS_HOLD                  = 5;
    public static $STATUS_WAY_TO_DELIVERY       = 6;
    public static $STATUS_DELIVERED             = 7;
    public static $STATUS_RECEIVED_IN_HUB       = 8;
    public static $STATUS_WAY_TO_HUB            = 9;
    public static $STATUS_REQUEST_FOR_PICKUP    = 10;
    public static $STATUS_REQUEST_FOR_DELIVERY  = 11;
    public static $STATUS_WAITING_FOR_DELIVERY  = 12;
}
