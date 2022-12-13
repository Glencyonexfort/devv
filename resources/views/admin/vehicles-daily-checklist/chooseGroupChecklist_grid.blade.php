{{-- @section('chooseGroupChecklist_grid') --}} 
<select name="choose_list_type" id="choose_group_checklist" class="form-control">
    <option value="">Select Group Checklist</option>
    @foreach($group as $type) 
       <option value="{{ $type->id }}">{{ $type->checklist_group }}</option>
    @endforeach
</select>  
{{-- @endsection --}}