{{-- @section('group_grid') --}} 
@foreach($group as $type) 
<tr id="display_group_form_grid_{{$type->id}}">
   <td>{{$type->checklist_group}}</td>
   <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> 
               <a data-localmovesid="{{$type->id}}" class="group-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> 
               <a data-localmovesid="{{$type->id}}" class="group-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> 
            </div>
         </div>
      </div>
   </td>
</tr>
<tr id="update_group_form_grid_{{$type->id}}" class="card-body light-blue-bg p10 hidden">
   <td><input type="text" name="checklist_group" value="{{ $type->checklist_group }}" id="checklist_group_{{ $type->id }}" class="form-control"></td>
   <td>
      <button class="btn btn-light btn-sm group-cancelUpdate-btn" style="padding:6px 6px;" data-localmovesid="{{$type->id}}">Cancel</button> 
      <button type="button" class="btn btn-success btn-sm update_group_btn" style="padding:6px 6px;" data-localmovesid="{{$type->id}}">Update</button>
   </td>
</tr>
 @endforeach
 {{-- @endsection --}}