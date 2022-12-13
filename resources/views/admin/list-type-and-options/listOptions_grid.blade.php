{{-- @section('listOptions_grid') --}} 
@foreach($listOptions as $type) 
<tr id="display_listOption_form_grid_{{$type->id}}">
   <td>{{$type->list_name}}</td>
   <td>{{$type->list_option}}</td>
   <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> <a data-listOptionId="{{$type->id}}" class="listOption-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> <a data-listoptionid="{{$type->id}}" class="listOption-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> </div>
         </div>
      </div>
   </td>
</tr>
<tr id="update_listOption_form_grid_{{$type->id}}" class="card-body light-blue-bg p10 hidden">
   <td><select name="list_id" class="form-control" id="list_id_{{$type->id}}">@foreach($listTypes as $opt)<option value="{{$opt->id}}" @if($opt->id==$type->list_type_id) selected="" @endif>{{ $opt->list_name }}</option>@endforeach</select></td>
   <td><input type="text" name="list_option" value="{{$type->list_option}}" id="list_option_{{$type->id}}" class="form-control"></td>
   <td><button class="btn btn-light btn-sm listOption-cancelUpdate-btn" style="padding:6px 6px;" data-listOptionId="{{$type->id}}">Cancel</button> <button type="button" class="btn btn-success btn-sm update_listOption_btn" style="padding:6px 6px;" data-listoptionid="{{$type->id}}">Update</button></td>
</tr>
 @endforeach
 {{-- @endsection --}}