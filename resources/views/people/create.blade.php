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
                          <select  id="property" name="property" >
                            <option value="AL">Alabama</option>
                            <option value="WY">Wyoming</option>
                          </select>
                          <small id="propertyHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group">
                          <label for="value">Value</label>
                          <input type="password" class="form-control" id="value" name="value" placeholder="Password">
                        </div>
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" id="exampleCheck1">
                          <label class="form-check-label" for="exampleCheck1">Check me out</label>
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
        console.log(response);
      },
      error: function(response) {

      },
    });
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
