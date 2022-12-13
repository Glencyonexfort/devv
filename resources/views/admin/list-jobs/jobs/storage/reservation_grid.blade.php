<article>   
    <h3>Reservation</h3>    
    <form id="storage_reservation_form" class="custom-form" action="#">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Storage Type</label>
                    <select id="storage_type_search" name="storage_unit_id" class="form-control">
                        @foreach($storage_type_list as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">     
                    <label>Start Date</label>       
                    <div class="input-group">
                        <span class="input-group-prepend">
                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                        </span>
                        <input id="storage_unit_start_date" name="from_date" type="text" class="form-control daterange-single daterange_field" placeholder="Start Date" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">       
                    <label>End Date</label>       
                    <div class="input-group">
                        <span class="input-group-prepend">
                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                        </span>
                        <input id="storage_unit_end_date" name="to_date" type="text" class="form-control daterange-single daterange_field" placeholder="End Date" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row row mt-3 mb-1">
            <div class="col-md-7">
                <p class="muted">
                    *Use the search when you want to add a new reservation
                    <br/> Enter the parameters for the search and click on the Search 
                </p>
            </div>
            <div class="form-group col-md-5">
                <label class="control-label">&nbsp;</label>
                <button type="button" id="search-reservation-filters" class="btn btn-success wide-btn"><i class="fa fa-check"></i> Search</button>
                <button type="button" id="search-reservation-reset-filters" class="btn bg-slate-700 wide-btn"><i class="fa fa-refresh"></i> Reset</button>
            </div>
        </div>
    </form>
    <div class="card">
    <table class="storage_reservation">
        <thead>
            <tr>
                <th><span >Storage Unit</span></th>
                <th><span >From</span></th>
                <th><span >To</span></th>
                <th style="text-align: right;"><span style="margin-right: 22px;"></span></th>
            </tr>
        </thead>
        <tbody>
            @if(isset($storage_reservation))
            @foreach($storage_reservation as $item)
            <?php
                $from_date = date(isset($global->date_format)?$global->date_format:'d/m/Y',strtotime($item->from_date));
                $to_date = date(isset($global->date_format)?$global->date_format:'d/m/Y',strtotime($item->to_date));
            ?>
                <tr id="storage_line_div_view_" class="storage_line_div">
                    <td>
                        <span>
                            {{ $item->serial_number.' - '.$item->type_name }}
                        </span>
                    </td>
                    <td>
                        <span>
                            {{ $from_date }}
                        </span>
                    </td>
                    <td>
                        <span>
                            {{ $to_date }}
                        </span>
                    </td>
                    <td>
                        <span data-prefix style="margin-right: 22px;">
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="delete_storage_reservation_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                        </div>
                                </div>
                            </div> 
                        </span>
                    </td>
                </tr>
            @endforeach
            @else
            <tr id="storage_line_div_norecord"><td colspan="4">No Reservation found!</td></tr>
            @endif
            <tr id="storage_line_div_new" class="bgblu storage_line_div hidden" data-row="0">
                <td>
                    <span>
                        <div class="form-group">
                            <select id="storage_unit_new" class="form-control">
                            </select>
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                            </span>
                            <input id="storage_unit_start_date_new" name="to_date" type="text" class="storage_from_date_new form-control daterange-single" placeholder="End Date" value="">
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                            </span>
                            <input id="storage_unit_end_date_new" name="to_date" type="text" class="storage_to_date_new form-control daterange-single" placeholder="End Date" value="">
                        </div>
                    </span>
                </td>
                <td>
                    <div id="storage_btn_div_new" class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="button" class="btn btn-light cancel_storage_btn"> Cancel</button>
                        <button type="button" class="btn btn-success ml-2 save_storage_btn"> Reserve</button>
                    </div>
                </td>
            </tr>

        </tbody>                
    </table>
    </div>
{{-- <div class="float-left">
    <button id="add_storage_line" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
</div> --}}
</article> 