{{-- @section('chooseListType_grid') --}} 
 <select name="choose_list_type" id="choose_list_type" class="form-control">
   <option value="">Select List Type</option>
   @foreach($listTypes as $type) 
      <option value="{{$type->id}}">{{$type->list_name}}</option>
   @endforeach
   </select>  
 {{-- @endsection --}}