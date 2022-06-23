<?php

namespace App\Http\Controllers;

use App\Http\Components\Message;
use App\Models\HumanRelation;
use App\Models\InteractionStatus;
use App\Models\InteractionTimeline;
use App\Models\People;
use App\Models\Property;
use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PeopleController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($message=null,$redirect=false) {
        $columns=[];
        $query = "SHOW COLUMNS FROM people";
        $results = DB::select($query);
        foreach($results as $result)
            array_push($columns,$result->Field);
        $peoples = People::latest()->paginate(10);
        
        if($message){
            return view('people.index',compact('peoples','columns'))->with('message','New People added ...');
        }else{
            return view('people.index',compact('peoples','columns'));
        }
        if($redirect)
            return Redirect::route('people.index',['message' => 'Successfully deleted ...']); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        if($request->input('reference')){
            $reference = People::find($request->input('reference'));
            return view('people.create',compact('reference'));
        }else{
            return view('people.create');
        }
    }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if($request->hasFile('photo')){
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            ]);
            $imageName = time().'.'.$request->photo->extension();  
            // $request->photo->move(public_path('photo'), $imageName);
            Storage::disk('photos')->put( $imageName, File::get($request->photo));
            
        }else{
            $request->request->remove('photo');
        }

        if( isset($request->reference_type) and ! is_numeric($request->reference_type)  ){ // and ! Property::where('name',$request->property)->first()
            $human_relation = new HumanRelation();
            $human_relation->name = $request->reference_type;
            $human_relation->causer_id = Auth::user()->id;
            $human_relation->save();
            $text = "New relation '$human_relation->name' added";
            $request->merge(['reference_type' => $human_relation->id]);
        }

        $new_request = $request->except(['_token','photo']);
        if(isset($imageName)) $new_request['photo'] = $imageName;
        
        $request_result = false;
        foreach ($new_request as $value)
            $request_result = $request_result || ($value != null);

        if($request_result ){
            
            if( $request->connected_from == 'facebook'){
                
                $timeline = new InteractionTimeline();
                $timeline->causer_id = Auth::user()->id;
                $timeline->interaction_status_id = InteractionStatus::$STATUS_DISCOVERED_VIA_SOCIAL_ID;
                $timeline->occurance_type = 'random';
                $timeline->is_active = false;
                $timeline->save();
            }

            $timeline = new InteractionTimeline();
            $timeline->causer_id = Auth::user()->id;
            $timeline->interaction_status_id = InteractionStatus::$STATUS_INSERTED_IN_SYSTEM;
            $timeline->occurance_type = 'planned';
            $timeline->is_active = true;
            $timeline->save();

            

            $people = People::create($new_request);
            $timeline->target_id = $people->id;
            $timeline->save();
            
            // $this->apiSuccess();
            // return $this->apiOutput(Response::HTTP_OK, "New People added ...");  
            return $this->index($message='New People added ...');
        }else{
            return $this->apiOutput(Response::HTTP_OK, "Minimum one field is required ...");
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$message='') {
        $people = People::findOrFail($id);
        // $properties = Property::
        // $people->values
        $values = People::join('values', 'values.people_id', '=', 'people.id')
                        ->join('properties', 'properties.id', '=', 'values.property_id')
                        // ->select('property_id')
                        ->select('*','people.name as name','properties.name as property_name','values.id as value_id')
                        ->get()
                        ->toArray();
        // dd($values);
        // $property_ids = array_column($values, 'property_id');
        
        return view('people.show',compact('people','values'))->with('message',$message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request, $message=''){
        $value = Value::findOrFail($request->value ?? null);
        $people = People::findOrFail($id);
        return view('people.edit',compact('people','value'))->with('message',$message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $people = People::findOrFail($id);
        $people->delete();
        return Redirect::route('people.index',['message' => 'Successfully deleted ...']);
    }

    public function listHumanRelations(){
        $human_relations = HumanRelation::get();
        $this->data = $human_relations->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All relations listed ..."); 
    }

    public function listProperties(){
        $properties = Property::get();
        $this->data = $properties->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All properties listed ...");
    }

    public function deletePeoplePropertyValue(Request $request,People $people,Value $value){
        $value->delete();
        $values = People::join('values', 'values.people_id', '=', 'people.id')
                        ->join('properties', 'properties.id', '=', 'values.property_id')
                        // ->select('property_id')
                        ->select('*','people.name as name','properties.name as property_name','values.id as value_id')
                        ->get()
                        ->toArray();
        return Redirect::route('people.show',[$people->id, 'message' => 'Successfully deleted ...']);
    }

    public function updatePeoplePropertyValue(Request $request,People $people,Value $value){
        $new_request = $request->except(['_token']);
        $value->update($new_request);
        $values = People::join('values', 'values.people_id', '=', 'people.id')
                        ->join('properties', 'properties.id', '=', 'values.property_id')
                        // ->select('property_id')
                        ->select('*','people.name as name','properties.name as property_name','values.id as value_id')
                        ->get()
                        ->toArray();
        return Redirect::route('people.show',[$people->id, 'message' => 'Successfully updated ...']);
    }

    
    
    

    

    

    public function addInfo(Request $request){
        // people_id
        // dd($request->all());
        $text = null;
        $property_id = null;
        if( ! is_numeric($request->property)  ){ // and ! Property::where('name',$request->property)->first()
            
            $property = new Property();
            $property->name = $request->property;
            $property->causer_id = Auth::user()->id;
            $property->save();
            $property_id = $property->id;
            $text = "New Property '$request->property' added";
        }

         
        $value = new Value();
        $value->people_id = $request->people_id;
        $value->property_id = $property_id ?? $request->property;
        $value->value = $request->value;
        $value->save();

        

        $text = $text ? $text." and data added ..." : "Data added ...";
        
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, $text);
        // $people = People::find($request->people_id)->first();
        // return $this->showAddPeopleInformationForm($people);
        
    }
}
