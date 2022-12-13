@foreach($roles as $role) 
<tr id="display_roles_form_grid_{{$role->id}}">
   <td>{{$role->display_name}}</td>
   <td>{{$role->description}}</td>
   <td>
      @if($role->tenant_id>0)
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right">                
               <a data-id="{{$role->id}}" class="roles-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> 
               <a data-id="{{$role->id}}" class="roles-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a>
               <a href="{{ route('admin.role-permissions',['id'=>$role->id]) }}"  class="dropdown-item" title="Permissions"><i class="icon-list"></i> Permissions</a>
               </div>

         </div>
      </div>
      @endif
   </td>
</tr>
<tr id="update_roles_form_grid_{{$role->id}}" class="card-body light-blue-bg p10 hidden">
   <td><input type="text" name="display_name" value="{{$role->display_name}}" id="edit_display_name_{{$role->id}}" class="form-control"></td>
   <td><input type="text" name="description" value="{{$role->description}}" id="edit_description_{{$role->id}}" class="form-control"></td>
   <td>      
      <button type="button" class="btn btn-success btn-sm update_role_btn" style="padding:6px 6px;" data-id="{{$role->id}}">Update</button>
      <button class="btn btn-light btn-sm roles-cancelUpdate-btn" style="padding:6px 6px;" data-id="{{$role->id}}">Cancel</button>       
   </td>
</tr>
@endforeach
{{-- @endsection --}}