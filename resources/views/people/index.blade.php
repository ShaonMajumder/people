@extends('layouts.app')

@section('content')


      

<script>
$(document).ready(function() {
  $("#property").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  });
});

  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  };

</script>

<script>
  $(document).ready(function() {
    @if( request()->get('message') )
      toastr.success("{{ request()->get('message') }}");
      // {{ request()->fullUrlWithQuery(['message' => null]) }}
    @endif
  });
  
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                  <div class="container mt-5" style="overflow-x: auto;">
                    <a href="{{ route("people.create") }}">Add New People</a>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                          <th scope="col" width="1%"></th>
                          @foreach($columns as $column)
                            <th scope="col" width="1%">{{$column}}</th>
                          @endforeach
                            {{-- <th scope="col" width="1%">#</th>
                            <th scope="col" width="15%">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col" width="10%">Username</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($peoples as $people)
                                <tr>
                                  <td>
                                    <a href="{{ route('people.show', [$people->id]) }}"><i class="fa-solid fa-plus"></i></a>
                                    <form method="POST" action="{{ route('people.destroy', [$people->id]) }}">
                                      @csrf
                                      @method('DELETE')
                                      <i class="fa-solid fa-trash" onclick="$(this).parent().submit()"></i>
                                    </form>
                                  </td>
                                  @foreach ($people->toArray() as $key => $item)
                                    
                                    @if($item and $key == 'photo')
                                      <td><img height="40" src="{{asset('photos/'.$item) }}" alt="" title=""></td>
                                    @elseif($people->reference and $key == 'reference_type')
                                      <td><a href="{{ route('people.show',[$people->reference->id]) }}">{{ $people->reference->name }}</a></td>
                                    @else
                                      <td>{{ $item }}</td>
                                    @endif
                                  @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
        
                    <div class="d-flex">
                        {!! $peoples->links() !!}
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready( function() {
  

  $.getJSON("/people/list-properties",function(response){
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

    let name = $('#name').val();
    let father_name = $('#father_name').val();
    let mother_name = $('#mother_name').val();
    let photo = $('#photo').val();
    let nid = $('#nid').val();
    let birth_certificate_number = $('#birth_certificate_number').val();
    let iris = $('#iris').val();
    let dna = $('#dna').val();
    let national_health_certificate_number = $('#national_health_certificate_number').val();

    $.ajax({
      url: "/people/insert",
      type:"POST",
      data:{
        "_token": "{{ csrf_token() }}",
        name:name,
        father_name:father_name,
        mother_name:mother_name,
        photo:photo,
        nid:nid,
        birth_certificate_number:birth_certificate_number,
        iris:iris,
        dna:dna,
        national_health_certificate_number:national_health_certificate_number    
      },
      success:function(response){
        toastr.success(response.message);
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
