<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(){
        return view('people.create');
    }

    public function insert(Request $request){
        if( ! Property::where('name',$request->property)->first() )
            dd( Property::where('name',$request->property)->first() );
    }
}
