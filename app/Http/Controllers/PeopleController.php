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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PeopleController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listHumanRelations(){
        $human_relations = HumanRelation::get();
        $this->data = $human_relations->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All relations listed ..."); 
    }
    public function create(Request $request){
        if($request->input('reference')){
            $reference = People::find($request->input('reference'));
            return view('people.create',compact('reference'));
        }else{
            return view('people.create');
        }
    }

    public function deletePeoplePropertyValue(Request $request,People $people,Value $value){
        $value->delete();
        $values = People::join('values', 'values.people_id', '=', 'people.id')
                        ->join('properties', 'properties.id', '=', 'values.property_id')
                        // ->select('property_id')
                        ->select('*','people.name as name','properties.name as property_name','values.id as value_id')
                        ->get()
                        ->toArray();
        return Redirect::route('people.info',array('people' => $people,'values' => $values))->with('message','Successfully deleted ...');
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

    public function listPeople($message=null){
        $columns=[];
        $query = "SHOW COLUMNS FROM people";
        $results = DB::select($query);
        foreach($results as $result)
            array_push($columns,$result->Field);
        $peoples = People::latest()->paginate(10);
        
        if($message){
            return view('people.list',compact('peoples','columns'))->with('message','New People added ...');
        }else{
            return view('people.list',compact('peoples','columns'));
        }
    }
    
    public function showEditPeopleInformationForm(People $people, Value $value, $message=''){
        return view('people.info.edit',compact('people','value'))->with('message',$message);
    }

    public function showAddPeopleInformationForm(People $people,$message=''){
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
        
        return view('people.info.add',compact('people','values'))->with('message',$message);
    }

    public function insert(Request $request){
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
            return $this->listPeople('New People added ...');
        }else{
            return $this->apiOutput(Response::HTTP_OK, "Minimum one field is required ...");
        }
        
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

    public function listProperties(){
        $properties = Property::get();
        $this->data = $properties->toJson();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "All properties listed ...");
    }
}
