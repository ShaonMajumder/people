<?php

namespace App\Http\Controllers;

use App\Http\Components\Message;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PeopleController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(){
        return view('people.create');
    }

    public function insert(Request $request){
        if( ! Property::where('name',$request->property)->first() ){
            $property = new Property();
            $property->name = $request->property;
            $property->causer_id = Auth::user()->id;
            $property->save();
            $this->apiSuccess();
            return $this->apiOutput(Response::HTTP_OK, "New Property '$property->name' added ...");
        }
            
    }
}
