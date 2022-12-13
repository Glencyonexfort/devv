{{-- @section('trucksize_grid') --}}
@foreach($vehicleGroups as $group) 
<tr id="display_vehicleGroup_form_grid_{{$group->id}}">
   <td>{{$group->group_name}}</td>
   <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> <a data-vehiclegroupid="{{$group->id}}" class="vehicleGroup-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> <a data-vehiclegroupid="{{$group->id}}" class="vehicleGroup-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> </div>
         </div>
      </div>
   </td>
</tr>
<tr id="update_vehicleGroup_form_grid_{{$group->id}}" class="card-body light-blue-bg p10 hidden">
   <td><input type="text" name="group_name" value="{{$group->group_name}}" id="group_name_{{$group->id}}" class="form-control"></td>
   <td><button class="btn btn-light btn-sm vehicleGroup-cancelUpdate-btn" style="padding:6px 6px;" data-vehiclegroupid="{{$group->id}}">Cancel</button> <button type="button" class="btn btn-success btn-sm update_truckSize_btn" style="padding:6px 6px;" data-vehiclegroupid="{{$group->id}}">Update</button></td>
</tr>
@endforeach
{{-- @endsection --}}