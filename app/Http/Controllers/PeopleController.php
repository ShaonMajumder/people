<?php

namespace App\Http\Controllers;

use App\Http\Components\DBTrait;
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
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Schema;
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PeopleController extends Controller
{
    use Message,DBTrait;

<<<<<<< HEAD
=======
    public function __construct()
    {
        $this->middleware('auth');
    }
    
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
<<<<<<< HEAD
    public function index($message=null,$redirect=false){
        $columns=[];
        $query = "SHOW COLUMNS FROM people";
        $results = DB::select($query);
        foreach($results as $result)
            array_push($columns,$result->Field);
        $peoples = People::latest()->paginate(10);
=======
    public function index($message=null,$redirect=false) {
        list($columns,$peoples) = $this->getDBListing(new People());
        
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
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
        
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
<<<<<<< HEAD
        if($redirect)
            return Redirect::route('people.index',array('peoples' => $peoples,'columns' => $columns))->with('message','Successfully deleted ...');
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
    public function store(Request $request){
        
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
            $request->merge(['reference_id' => (int) $request->reference_id]);
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
            
            
            // return $this->listPeople('New People added ...');
            return Redirect::route('people.index',array('message' => 'New People added ...'));
        }else{
            return $this->apiOutput(Response::HTTP_OK, "Minimum one field is required ...");
        }
        
    }

    /**
=======
        
    }

    /**
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
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
        
<<<<<<< HEAD
        return view('people.info.add',compact('people','values'))->with('message',$message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $message=''){ //People $people, Value $value,
        $people = People::findOrFail($id);
        return view('people.info.edit',compact('people'))->with('message',$message);
=======
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

    public function updatePeoplePropertyValue(Request $request,People $people,Value $value){
        $new_request = $request->except(['_token']);
        $value->update($new_request);
        return Redirect::route('people.show',[$people->id, 'message' => 'Successfully updated ...']);
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
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
<<<<<<< HEAD
        //
=======
        $value = Value::findOrFail($request->value_id);
        $new_request = $request->except(['_token']);
        $value->update($new_request);
        $people = People::findOrFail($id);
        return Redirect::route('people.show',[$people->id, 'message' => 'Successfully updated ...']);
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
<<<<<<< HEAD
    public function destroy($id)
    {
        $people = People::findOrFail($id);
        $people->delete();
        return Redirect::route('people.index',array('message' => 'Successfully deleted ...'));
    }

    public function listProperties(){
        $properties = Property::get();
        $this->data = $properties->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All properties listed ...");
    }

    public function listHumanRelations(){
        $human_relations = HumanRelation::get();
        $this->data = $human_relations->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All relations listed ..."); 
=======
    public function destroy($id,Request $request) {
        if($request->value){
            $value = Value::findOrFail($request->value);
            $value->delete();
            return Redirect::route('people.show',[$id,'message' => 'Successfully deleted ...']);
        }else{
            $people = People::findOrFail($id);
            $people->delete();
            return Redirect::route('people.index',['message' => 'Successfully deleted ...']);
        }
        
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
    }

    /**
     * Add a new value for a people
     *
     * @param Request $request
     * @return void
     */
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

<<<<<<< HEAD
        
=======
>>>>>>> b641b4d45132921e61037422708299f38aeae07a

        $text = $text ? $text." and data added ..." : "Data added ...";
        
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, $text);
<<<<<<< HEAD
        // $people = People::find($request->people_id)->first();
        // return $this->showAddPeopleInformationForm($people);
        
=======
    }

    public function listHumanRelations(){
        $human_relations = HumanRelation::get();
        $this->data = $human_relations->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All relations listed ..."); 
>>>>>>> b641b4d45132921e61037422708299f38aeae07a
    }

    public function deletePeoplePropertyValue(Request $request,People $people,Value $value){
        $value->delete();
        $values = People::join('values', 'values.people_id', '=', 'people.id')
                        ->join('properties', 'properties.id', '=', 'values.property_id')
                        // ->select('property_id')
                        ->select('*','people.name as name','properties.name as property_name','values.id as value_id')
                        ->get()
                        ->toArray();
        return Redirect::route('people.show', [$people->id])->with('message','Successfully deleted ...');
    }

    public function showEditPeopleInformationForm(People $people, Value $value, $message=''){
        return view('people.info.edit',compact('people','value'))->with('message',$message);
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
        return Redirect::route('people.info',array('people' => $people,'values' => $values))->with('message','Successfully updated ...');
    }

    

    
    
    

    

    

    
}
