{{-- @section('regionaldepot_grid') --}}@foreach($depotLocations as $location) <tr id="display_depotLocation_form_grid_{{$location->id}}"> <td>{{$location->region_name}}</td><td>{{$location->depot_suburb}}</td><td> <div class="list-icons"> <div class="dropdown"> <a href="#" class="list-icons-item" data-toggle="dropdown"><span class="icon-menu"></span></a> <div class="dropdown-menu dropdown-menu-right"> <a data-depotlocationid="{{$location->id}}" class="depotLocation-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a> <a data-depotlocationid="{{$location->id}}" class="depotLocation-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a> </div></div></div></td></tr><tr id="update_depotLocation_form_grid_{{$location->id}}" class="card-body light-blue-bg p10 hidden"> <td> <select name="region_id" id="region_id_{{$location->id}}" class="form-control"> @foreach($pricingRegions as $data) <option value="{{$data->id}}" @if($data->id==$location->region_id) selected="" @endif >{{$data->region_name}}</option> @endforeach </select> </td><td><input type="text" name="depot_suburb" onchange="removeCountryNameEditCase({{$location->id}})" value="{{$location->depot_suburb}}" id="depot_suburb_{{$location->id}}" class="form-control depot_suburb"></td><td><button class="btn btn-light btn-sm depotLocation-cancelUpdate-btn" style="padding:6px 6px;" data-depotlocationid="{{$location->id}}">Cancel</button> <button type="button" class="btn btn-success btn-sm update_depotLocation_btn" style="padding:6px 6px;" data-depotlocationid="{{$location->id}}">Update</button></td></tr>@endforeach{{-- @endsection --}}
<script type="text/javascript">
   function initialize() {
        var options = {
            types: ['(cities)'],
            componentRestrictions: {
                country: "au"
            }
        };
        var allDepotInputs = document.getElementsByClassName('depot_suburb');

        for (var i = 0; i < allDepotInputs.length; i++) {
            //console.log(allDepotInputs[i]);
            var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
            autocomplete.inputId = allDepotInputs[i].id;
        }
    
        //var autocomplete = new google.maps.places.Autocomplete(input, options);

    }

    document.addEventListener('DOMNodeInserted', function(event) {
        // console.log(event);
        var target = $(event.target);
        if (target.hasClass('pac-item')) {
            // console.log(target.html());
            target.html(target.html().replace(/, Australia<\/span>/, "</span>"));
        }
    });

    google.maps.event.addDomListener(window, 'load', initialize);

    function removeCountryNameEditCase(row_id)
    {
      setTimeout(function() {
            var newval = $('#depot_suburb_'+row_id).val().replace(', Australia', '');
            $('#depot_suburb_'+row_id).val(newval);
        }, 5);
    }
    /*$(document).on('change', '.depot_suburb', function() {
      console.log($('.depot_suburb').val());
        setTimeout(function() {
            var newval = $('.depot_suburb').val().replace(', Australia', '');
            $('.depot_suburb').val(newval);
        }, 10);
    });*/
</script>