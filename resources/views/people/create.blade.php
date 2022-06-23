@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
  $("#connected_from").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  });
  $("#reference_type").select2({
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

                    <form id="form" action="{{ route('people.store') }}" method="post">
                      @csrf
                        <div class="form-group">
                          <label for="name">Full Name</label>
                          <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                        </div>

                        @isset($reference)
                          {{-- @dd($reference) --}}
                          <div class="form-group">
                            <label for="reference_type">Relative / Reference Type</label>
                            <select style="width:100%;" id="reference_type" name="reference_type" > </select>
                            of <label>Relative / Reference - {{ $reference->name }}</label>
                          </div>
                          <div class="form-group">
                            
                            <input type="hidden" id="reference_id" name="reference_id" value="{{ $reference->id }}">
                          </div>
                          <script>
                            $(document).ready(function() {
                              function listHumanRelations(){
                                $.getJSON("/people/list-human-relations",function(response){
                                  let data = response.data;
                                  data = JSON.parse(data); //convert to javascript array
                                  values = '<option selected disabled>Select a property</option>';
                                  $.each(data,function(key,value){
                                    values+="<option value='"+value.id+"'>"+value.name+"</option>";
                                  });
                                  $("#reference_type").html(values); 
                                });
                              }
                              listHumanRelations();
                            });
                          </script>
                        @endisset

                        <div class="form-group">
                          <label for="connected_form">Connected From</label>
                          <select style="width:100%;" id="connected_from" name="connected_from" >
                            <option selected disabled>Select</option>
                            <option value="facebook">Facebook</option>
                          </select>
                        </div>

                        <div class="form-group">
                          <label for="photo">Photo</label>
                          <input type="file" id="photo" name="photo" class="form-control" multiple>
                        </div>

                        <div class="form-group">
                          <label for="father_name">Father Name</label>
                          <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father Name">
                        </div>
                        <div class="form-group">
                          <label for="mother_name">Mother Name</label>
                          <input type="text" class="form-control" id="mother_name" name="mother_name" placeholder="Mother Name">
                        </div>
                        
                        <div class="form-group">
                          <label for="birth_certificate_number">Birth Certificate Number</label>
                          <input type="text" class="form-control" id="birth_certificate_number" name="birth_certificate_number" placeholder="Birth Certificate Number">
                        </div>
                        <div class="form-group">
                          <label for="nid">National identification number</label>
                          <input type="text" class="form-control" id="nid" name="nid" placeholder="NID">
                        </div>
                        <div class="form-group">
                          <label for="iris">IRIS</label>
                          <input type="text" class="form-control" id="iris" name="iris" placeholder="IRIS">
                        </div>
                        <div class="form-group">
                          <label for="dna">DNA</label>
                          <input type="text" class="form-control" id="dna" name="dna" placeholder="DNA">
                        </div>
                        <div class="form-group">
                          <label for="national_health_certificate_number">National Health certificate number</label>
                          <input type="text" class="form-control" id="national_health_certificate_number" name="national_health_certificate_number" placeholder="National Health certificate number">
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

    var formData = new FormData();
    formData.append("_token", "{{ csrf_token() }}");
    formData.append('name', $('#name').val());
    formData.append('connected_from', $('#connected_from').val());
    formData.append('photo', $('#photo')[0].files[0]);
    formData.append('father_name', $('#father_name').val());
    formData.append('mother_name', $('#mother_name').val());
    formData.append('nid', $('#nid').val());
    formData.append('birth_certificate_number', $('#birth_certificate_number').val());
    formData.append('iris', $('#iris').val());
    formData.append('dna', $('#dna').val());
    formData.append('national_health_certificate_number', $('#national_health_certificate_number').val());
    
    if ($('#reference_type').length > 0) {
      formData.append('reference_id', $('#reference_id').val());
      formData.append('reference_type', $('#reference_type').val());
    }

    $.ajax({
      url: "{{ route('people.store') }}",
      type:"POST",
      data: formData,
      processData: false,  // tell jQuery not to process the data
      contentType: false,  // tell jQuery not to set contentType
      success:function(response){
        // toastr.success(response.message);
        window.location.href = "{{ route('people.index','message=New People added ...') }}";
        if(response.status)
          $('#form')[0].reset();
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
