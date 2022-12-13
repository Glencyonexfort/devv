
<?php $i = 1; ?> 
@foreach($taxes as $tax)
<tr id="display_tax_form_grid_{{$tax->id}}">
    <td>
        {{$tax->tax_name}}
    </td>
    <td>
        {{$tax->rate_percent.'%'}}
    </td>
    <td class="text-center">
        <div class="list-icons">
            <div class="dropdown"> 
                <a href="#" class="list-icons-item" data-toggle="dropdown">
                    <span class="icon-menu"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right"> 
                    <a data-row_id="{{$tax->id}}" class="edit_tax_row dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> 
                    <a data-row_id="{{$tax->id}}" class="remove_tax_btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr id="update_tax_form_grid_{{$tax->id}}" class="card-body light-blue-bg p10 hidden">
    <td>
        <input type="text" name="tax_name" value="{{$tax->tax_name}}" id="tax_name_{{$tax->id}}" class="form-control" />
    </td>
    <td>
        <input type="number" name="rate_percent" value="{{$tax->rate_percent}}" id="rate_percent_{{$tax->id}}" class="form-control" />
    </td>
    <td> 
        <button class="btn btn-light btn-sm cancel_update_tax_row" style="padding: 6px 6px;" data-row_id="{{$tax->id}}">Cancel</button> 
        <button type="button" class="btn btn-success btn-sm update_tax_btn" style="padding: 6px 6px;" data-row_id="{{$tax->id}}">Update</button>
    </td>
</tr> <?php $i++; ?> 
@endforeach

<tr id="new_tax_form_grid" class="card-body light-blue-bg p10 hidden">
    <td>
        <input type="text" name="tax_name" value="" id="tax_name_new" class="form-control" />
    </td>
    <td>
        <input type="number" name="rate_percent" value="" id="rate_percent_new" class="form-control" />
    </td>
    <td> 
        <button id="cancel_new_tax_row" class="btn btn-light btn-sm" style="padding: 6px 6px;">Cancel</button> 
        <button id="save_new_tax" type="button" class="btn btn-success btn-sm save_tax_btn" style="padding: 6px 6px;">Save</button>
    </td>
</tr>