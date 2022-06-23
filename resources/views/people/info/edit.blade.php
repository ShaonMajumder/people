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
