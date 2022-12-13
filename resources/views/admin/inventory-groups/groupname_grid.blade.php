{{-- @section('trucksize_grid') --}}
@foreach($inventoryGroups as $group) 
<tr id="display_inventoryGroup_form_grid_{{$group->id}}">
   <td>{{$group->group_name}}</td>
   <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> <a data-localmovesid="{{$group->id}}" class="inventoryGroup-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> <a data-localmovesid="{{$group->id}}" class="inventoryGroup-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> </div>
         </div>
      </div>
   </td>
</tr>
<tr id="update_inventoryGroup_form_grid_{{$group->id}}" class="card-body light-blue-bg p10 hidden">
   <td><input type="text" name="group_name" value="{{$group->group_name}}" id="group_name_{{$group->id}}" class="form-control"></td>
   <td><button class="btn btn-light btn-sm inventoryGroup-cancelUpdate-btn" style="padding:6px 6px;" data-localmovesid="{{$group->id}}">Cancel</button> <button type="button" class="btn btn-success btn-sm update_truckSize_btn" style="padding:6px 6px;" data-localmovesid="{{$group->id}}">Update</button></td>
</tr>
@endforeach
{{-- @endsection --}}