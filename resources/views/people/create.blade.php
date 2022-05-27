@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
  $("#property").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  });
});

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
                    
                    <form id="form" action="{{url('people/insert')}}" method="post">
                      @csrf
                        <div class="form-group">
                          <label for="inputPropery">Property Name</label>
                          {{-- <input type="text" class="form-control" id="inputPropery" aria-describedby="propertyHelp" placeholder="Enter email"> --}}
                          <select style="width:100%;"   id="property" name="property" >
                            <option></option>
                          </select>
                          {{-- <small id="propertyHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                        </div>
                        <div class="form-group">
                          <label for="value">Value</label>
                          <input type="text" class="form-control" id="value" name="value" placeholder="Value">
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

  $.getJSON("/people/listproperties",function(response){
    let data = response.data;
    data = JSON.parse(data); //convert to javascript array
    values = '<option selected disabled>Select a property</option>';
    $.each(data,function(key,value){
      
      values+="<option value='"+value.id+"'>"+value.name+"</option>";
    });
    $("#property").html(values); 
  });


  $("#form").submit(function(e){
    
    e.preventDefault();

    let property = $('#property').val();
    let value = $('#value').val();
    
    $.ajax({
      url: "/people/insert",
      type:"POST",
      data:{
        "_token": "{{ csrf_token() }}",
        property:property,
        value:value
      },
      success:function(response){

        toastr.success(response.message);
      },
      error: function(response) {
        toastr.error(response.message);
      },
    });
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
