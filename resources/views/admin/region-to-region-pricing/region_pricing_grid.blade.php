{{-- @section('region_pricing_grid') --}}
<?php $i = 1; ?>
@foreach($regionToRegionPricings as $priceRs)
<tr id="display_regionToRegionPricings_form_grid_{{$priceRs->id}}">
    <td>{{$priceRs->from_region->region_name}}</td>
    <td>{{$priceRs->to_region->region_name}}</td>
    <td>{{$priceRs->cbm_min}}</td>
    <td>{{$priceRs->cbm_max}}</td>
    <td>{{$global->currency_symbol}}{{$priceRs->price_flat}}</td>
    <td>{{$global->currency_symbol}}{{$priceRs->price_per_cbm}}</td>
    <td>{{$global->currency_symbol}}{{$priceRs->min_price}}</td>
    <td class="text-center">
        <div class="list-icons">
            <div class="dropdown">
                <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a data-pricingregionid="{{$priceRs->id}}" class="regionToRegionPricings-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a>
                    <a data-pricingregionid="{{$priceRs->id}}" class="regionToRegionPricings-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr id="update_regionToRegionPricings_form_grid_{{$priceRs->id}}" class="card-body light-blue-bg p10 hidden">
    <td>
        <select name="from_region_id" id="from_region_id_{{$priceRs->id}}" class="form-control">
            @foreach($jobs_pricing_regions as $data)
            <option value="{{$data->id}}" @if($data->id==$priceRs->from_region_id) selected="" @endif >{{$data->region_name}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="to_region_id" id="to_region_id_{{$priceRs->id}}" class="form-control">
            @foreach($jobs_pricing_regions as $data)
            <option value="{{$data->id}}" @if($data->id==$priceRs->to_region_id) selected="" @endif >{{$data->region_name}}</option>
            @endforeach
        </select>
    </td>
    <td><input type="text" name="cbm_min" value="{{$priceRs->cbm_min}}" id="cbm_min_{{$priceRs->id}}" class="form-control" /></td>
    <td><input type="text" name="cbm_max" value="{{$priceRs->cbm_max}}" id="cbm_max_{{$priceRs->id}}" class="form-control" /></td>
    <td><input type="text" name="price_flat" value="{{$priceRs->price_flat}}" id="price_flat_{{$priceRs->id}}" class="form-control" /></td>
    <td><input type="text" name="price_per_cbm" value="{{$priceRs->price_per_cbm}}" id="price_per_cbm_{{$priceRs->id}}" class="form-control" /></td>
    <td><input type="text" name="min_price" value="{{$priceRs->min_price}}" id="min_price_{{$priceRs->id}}" class="form-control" /></td>
    <td>
        <button class="btn btn-light btn-sm regionToRegionPricings-cancelUpdate-btn" style="padding: 6px 6px;" data-pricingregionid="{{$priceRs->id}}">Cancel</button>
        <button type="button" class="btn btn-success btn-sm update_regionToRegionPricings_btn" style="padding: 6px 6px;" data-pricingregionid="{{$priceRs->id}}">Update</button>
    </td>
</tr>
<?php $i++; ?>
@endforeach{{-- @endsection --}}