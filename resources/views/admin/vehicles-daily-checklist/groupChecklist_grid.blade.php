{{-- @section('listOptions_grid') --}} 
@foreach($group_checklist as $type) 
<tr id="display_group_checklist_form_grid_{{ $type->id }}">
   <td>{{ $type->checklist_group }}</td>
   <td>{{ $type->checklist }}</td>
   <td>
      <div class="list-icons">
         <div class="dropdown">
            <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
            <div class="dropdown-menu dropdown-menu-right"> 
                <a data-groupchecklistId="{{ $type->id }}" class="group-checklist-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> 
                <a data-groupchecklistid="{{ $type->id }}" class="group-checklist-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> 
            </div>
         </div>
      </div>
   </td>
</tr>
<tr id="update_group_checklist_form_grid_{{ $type->id }}" class="card-body light-blue-bg p10 hidden">
   <td>
       <select name="list_id" class="form-control" id="group_id_{{ $type->id }}">
            @foreach($group as $opt)
                <option value="{{ $opt->id }}" @if($opt->id==$type->group_id) selected="" @endif>{{ $opt->checklist_group }}</option>
            @endforeach
        </select>
    </td>
   <td>
       <input type="text" name="checklist" value="{{ $type->checklist }}" id="checklist_{{$type->id}}" class="form-control">
    </td>
   <td>
       <button class="btn btn-light btn-sm groupChecklist-cancelUpdate-btn" style="padding:6px 6px;" data-groupChecklistId="{{ $type->id }}">Cancel</button> 
       <button type="button" class="btn btn-success btn-sm update_groupChecklist_btn" style="padding:6px 6px;" data-groupChecklistId="{{ $type->id }}">Update</button>
    </td>
</tr>
 @endforeach
 {{-- @endsection --}}