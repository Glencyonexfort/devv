{{-- @section('contact_grid') --}}
@foreach($contacts as $contact)
<ul class="media-list">
    <li class="media broderline1 contact_grid_1" style="border-bottom: none!important;">
        <div class="media-body">
            <span>{{ $contact->name }}</span>
            <div class="text-muted">{{ str_limit($contact->description,25) }}</div>
        </div>

        <div class="header-elements broderline">
            <div class="list-icons">
                {{-- <a href="#" class="list-icons-item mr-2" title="Mark Completed"><i class="icon-checkmark3"></i></a> --}}
                <div class="dropdown">
                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown">
                    <img class="contact_grid_2" src="{{ asset('newassets/img/icon-edit-1.png') }}">            
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                    {{-- <a data-contactid="{{ $contact->id }}" data-leadid="{{ $lead_id }}" class="contact-expand-btn dropdown-item mr-2 cursor-pointer" title="Expand"><i class="icon-profile"></i>Expand</a> --}}
                <a data-contactid="{{ $contact->id }}" data-leadid="{{ $lead_id }}" class="contact-update-btn dropdown-item cursor-pointer" title="Edit"><i class="icon-pencil5"></i>Edit</a>
                <a data-contactid="{{ $contact->id }}" data-leadid="{{ $lead_id }}" class="contact-remove-btn dropdown-item mr-2 cursor-pointer txt-red" title="Delete"><i class="icon-bin"></i>Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
<?php
    $contact_detail_data = App\CRMContactDetail::where(['tenant_id'=> auth()->user()->tenant_id,'contact_id'=>$contact->id ])->get();
?>
<div id="update_contact_form_grid_{{ $contact->id }}" class="card-body light-blue-bg p10 hidden">
    <form id="update_contact_form_{{ $contact->id }}" class="custom-form update_contact_form" action="#">
        @csrf
        {{ Form::hidden('lead_id', $lead_id) }}
        {{ Form::hidden('contact_id', $contact->id) }}
        <div class="form-group">
            <label>Name</label>
            <input name="name" type="text" class="form-control" value="{{ $contact->name }}">
        </div>

        <div class="form-group">
            <label>Title</label>
            <input name="description" type="text" class="form-control" value="{{ $contact->description }}">
        </div>

        <div class="form-group">
            <label>Contact Detail</label>
            @foreach($contact_detail_data as $data)
            <div class="input-group mgb-10">
                <input name="contact_detail[]" type="text" class="form-control contact_detail" placeholder="Phone, email or URL" autocomplete="false" value="{{ $data->detail }}">
                <div class="input-group-append">                    
                    <select name="contact_detail_type[]" class="form-control form-control-uniform">
                        @foreach($contact_types as $d)
                            <option value="{{ $d->list_option }}"
                                @if($d->list_option == $data->detail_type)
                                selected=""
                                @endif
                            >{{ $d->list_option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endforeach
            <div class="input-group mgb-10 contact_detail_div_edit" id="contact_detail_div_edit_{{ $contact->id }}">
                <input name="contact_detail[]" type="text" class="form-control contact_detail" placeholder="Phone, email or URL" autocomplete="false" value="" data-id="{{ $contact->id }}">
                <div class="input-group-append">                    
                    <select name="contact_detail_type[]" class="form-control form-control-uniform">
                        @foreach($contact_types as $d)
                            <option value="{{ $d->list_option }}">{{ $d->list_option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-start align-items-center m-t-10">
            <button type="reset" class="btn btn-light contact-update-btn" data-contactid="{{ $contact->id }}">Cancel</button>
            <button type="button" class="update_contact_btn btn bg-blue ml-3" data-contactid="{{ $contact->id }}">Update</button>
        </div>

    </form>
</div>
<div id="expand_contact_form_grid_{{ $contact->id }}" class="card-body light-blue-bg p10" style="border: 1px solid #d2d2d2;border-top: none;">
        <form class="custom-form" action="#">
            <div class="form-group">
                <label>Name</label>
                <p>{{ $contact->name }}</p>
            </div>

            <div class="form-group">
                <label>Decription</label>
                <p>{{ $contact->description }}</p>
            </div>
            @foreach($contact_detail_data as $data)
            <div class="form-group">
                <label>{{ $data->detail_type }}</label>
                <p>{{ $data->detail }}</p>
            </div>
            @endforeach
    
            {{-- <div class="d-flex justify-content-start align-items-center m-t-10">
                <button class="btn btn-light contact-expand-btn" data-contactid="{{ $contact->id }}">Close</button>
            </div> --}}
        </form>
        </div>
    @endforeach
{{-- @endsection --}}