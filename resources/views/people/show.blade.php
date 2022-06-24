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
                    
                    <h2>{{ $people->name }}</h2>
                    <img height="100" src="{{asset('photos/'.$people->photo) }}" alt="" title="">
                    </br>
                    <a href="{{ route('people.create', ['reference' => $people->id ]) }}">Add Relative</a> 
                    </br>
                    @foreach( $values as $value)
                      <a href="{{ route('people.edit', [$people->id, 'value'=> $value['value_id'] ]) }}"><i class="fas fa-edit"></i> </a>
                      <form id="form-{{ $value['value_id'] }}" style="display: inline-block;" method="POST" action="{{ route('people.destroy', [$people->id] ) }}">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="value" value="{{ $value['value_id'] }}">
                        <i class="fas fa-trash" onclick="getDeletePermission({{ $value['value_id'] }})"></i>
                      </form>
                      {{ $value['property_name'] . ' - ' . $value['value']  }} <br>
                    @endforeach
                    
                    <form id="form" action="{{url('people/insert')}}" method="post">
                      @csrf
                      <input type="hidden" name="people_id" id="people_id" value="{{$people->id}}">

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
  function getDeletePermission(value_id){
    swal({
      title: "Are you sure?",
      text: "You are going to delete "+value_id+"!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        $('#form-'+value_id).submit();
      } else {
        swal("Your value is safe!");
      }
    });
  }
  
  $(document).ready( function() {
    toastr.options =
    {
      "closeButton" : true,
      "progressBar" : true
    };
    
    @if( request()->get('message') )
      toastr.success("{{ request()->get('message') }}");
      // {{ request()->fullUrlWithQuery(['message' => null]) }}
    @endif

    function listProperties(){
      $.getJSON("/people/list-properties",function(response){
        let data = response.data;
        data = JSON.parse(data);
        values = '<option selected disabled>Select a property</option>';
        $.each(data,function(key,value){
          values+="<option value='"+value.id+"'>"+value.name+"</option>";
        });
        $("#property").html(values); 
      });
    }
    listProperties();
    
    $("#form").submit(function(e){  
      e.preventDefault();

      let people_id = $('#people_id').val();
      let property = $('#property').val();
      let value = $('#value').val();
      
      $.ajax({
        url: "/people/addinfo",
        type:"POST",
        data:{
          "_token": "{{ csrf_token() }}",
          people_id:people_id,
          property:property,
          value:value
        },
        success:function(response){
          // toastr.success(response.message);
          // listProperties();
          location.reload();
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