@extends('layouts.app')

@section('content')
<script>
// $(document).ready(function() {
//   $("#property").select2({
//     tags: true,
//     tokenSeparators: [',', ' ']
//   });
// });

</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <h2>{{ $people->name }}</h2>
                    <form id="form" action="{!! route('people.update', $people->id) !!}" method="post">
                      @csrf
                      @method('PATCH')
                        <input type="hidden" name="value_id" value="{{ $value->id }}">
                        <div class="form-group">
                          <label for="inputPropery">Property Name</label>
                          {{-- <input type="text" class="form-control" id="inputPropery" aria-describedby="propertyHelp" placeholder="Enter email"> --}}
                          {{-- <select style="width:100%;"   id="property" name="property" >
                            <option></option>
                          </select> --}}
                          <input type="text" class="form-control" id="property" name="property" placeholder="Value" value="{{ $value->property->name }}" disabled>
                          {{-- <small id="propertyHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                        </div>
                        <div class="form-group">
                          <label for="value">Value</label>
                          <input type="text" class="form-control" id="value" name="value" placeholder="Value" value="{{ $value->value }}">
                        </div>
                        <div class="form-group">
                          <label for="question_asked">Question asked ?</label>
                          <input type="text" class="form-control" id="question_asked" name="question_asked" placeholder="question asked?" value="{{ $value->question_asked }}">
                        </div>
                        <div class="form-group">
                          <label for="value">Information source</label>
                          <input type="text" class="form-control" id="information_source" name="information_source" placeholder="information source" value="{{ $value->information_source }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready( function() {
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  };

  @if(Session::has('message'))
    toastr.success("{{ session('message') }}");
  @endif

  function listProperties(){
    $.getJSON("/people/listproperties",function(response){
      let data = response.data;
      data = JSON.parse(data); //convert to javascript array
      values = '<option selected disabled>Select a property</option>';
      $.each(data,function(key,value){
        
        values+="<option value='"+value.id+"'>"+value.name+"</option>";
      });
      $("#property").html(values); 
    });
  }
  listProperties();
  


  
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
