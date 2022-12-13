{{-- @section('listtype_grid') --}} 
@foreach($listTypes as $type) 
<tr id="display_listType_form_grid_{{$type->id}}">
   <td>{{$type->list_name}}</td>
   <!-- <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> <a data-localmovesid="{{$type->id}}" class="listType-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> <a data-localmovesid="{{$type->id}}" class="listType-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> </div>
         </div>
      </div>
   </td> -->
</tr>
<tr id="update_listType_form_grid_{{$type->id}}" class="card-body light-blue-bg p10 hidden">
   <td><input type="text" name="list_name" value="{{$type->list_name}}" id="list_name_{{$type->id}}" class="form-control"></td>
   <td><button class="btn btn-light btn-sm listType-cancelUpdate-btn" style="padding:6px 6px;" data-localmovesid="{{$type->id}}">Cancel</button> <button type="button" class="btn btn-success btn-sm update_listType_btn" style="padding:6px 6px;" data-localmovesid="{{$type->id}}">Update</button></td>
</tr>
 @endforeach
 {{-- @endsection --}}