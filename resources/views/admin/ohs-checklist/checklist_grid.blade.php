{{-- @section('trucksize_grid') --}}
@foreach($checklist as $item) 
<tr id="display_checklist_form_grid_{{$item->id}}">
   <td>{{$item->checklist}}</td>
   <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> 
                <a data-localmovesid="{{$item->id}}" class="checklist-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> 
                <a data-localmovesid="{{$item->id}}" class="checklist-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> 
            </div>
         </div>
      </div>
   </td>
</tr>
<tr id="update_checklist_form_grid_{{$item->id}}" class="card-body light-blue-bg p10 hidden">
   <td><input type="text" name="checklist" value="{{$item->checklist}}" id="checklist_name_{{$item->id}}" class="form-control"></td>
   <td>
       <button class="btn btn-light btn-sm checklist-cancelUpdate-btn" style="padding:6px 6px;" data-localmovesid="{{$item->id}}">Cancel</button> 
       <button type="button" class="btn btn-success btn-sm update_checklist_btn" style="padding:6px 6px;" data-localmovesid="{{$item->id}}">Update</button>
    </td>
</tr>
@endforeach
{{-- @endsection --}}