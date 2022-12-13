@extends('layouts.app')

@section('page-title')

 <div class="page-header page-header-light">

 <nav class="navbar-max-width navbar navbar-expand-lg py-1 pr-md-0">
    <ul class="navbar-nav navbar-topp-margin">
        <li class="nav-item-centre nav-item  m-0">
            <div class="searchbar">
                <i class="icon fa fa-search ml-1"></i>
                <input type="text" placeholder="Search" class="input-searchbar-newdesign">
            </div>  
        </li>

    </ul>
    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarNavDropdown" style="margin-right: 0rem !important;">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item-centre nav-item ">
                <span class="mr-3 icon-span"> 
                    <i class="fa fa-phone"></i>
                    <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                    <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                    </div>
                </span>
            </li>
            <li class="nav-item-centre nav-item ">
                <span class="mr-3 icon-span"> 
                    <i class="fa fa-question-circle"></i>
                    <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                    <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                    </div>
                </span>
            </li>
        </ul>
    </div>
</nav>

    <div class="page-header-content header-elements-md-inline" style="border-top:1px solid #ccc; border-bottom: 1px solid #ccc;">
        <div class="page-pipelines d-flex">
            <h4><span class="font-weight-semibold" style="font-size:23.6px;margin-left:-11px !important;font-family: 'Poppins',sans-serif;">Sales Pipeline</span></h4>
        </div>
    </div>

    <nav class="navbar-max-width navbar navbar-expand-lg py-2 navbar-bottom pr-md-0">
        <ul class="navbar-nav mr-auto">
                <li class="nav-item list-item-border ">
                    <span class="mr-2 icon-span"> 
                        <i class="fa fa-calendar-check-o mr-1"></i> 
                        <b>Excpected:</b> All Time  
                        <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                        <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                        </div>
                    </span>
                </li>
                <li class="nav-item ">
                    <span class="mr-2 icon-span"> 
                        <img class="icons-margin-top mr-1" src="{{ asset('newassets/img/growth.png') }}">
                        Sales
                        <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                        <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                        </div>
                    </span>
                </li>
        </ul>
    
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown2" aria-controls="navbarNavDropdown2" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </button>
  
        <div class="collapse navbar-collapse" id="navbarNavDropdown2" style="margin-right: 0rem !important;">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item-centre nav-item list-item-border ">
                    <span class="mr-3 icon-span"> 
                        <i class="fa fa-building mr-1"></i>
                        All Leads
                        <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                        <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                        </div>
                    </span>
                </li>
                <li class="nav-item-centre nav-item list-item-border ">
                    <span class="mr-3 icon-span"> 
                        <i class="fa fa-user-circle mr-1"></i>    
                        All Users
                        <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                        <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                        </div>
                    </span>
                </li>
                <li class="nav-item-centre nav-item list-item-border ">
                    <span class="mr-3 icon-span"> 
                        <i class="fa fa-sort-amount-desc mr-1"></i>    
                        Actual Value (Annualized)
                        <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                        <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                        </div>
                    </span>
                </li>
                <li class="nav-item-centre nav-item ">
                    <span class="mr-3 icon-span"> 
                        <img class="icons-margin-top mr-1" src="{{ asset('newassets/img/equalizer.png') }}">
                        Options
                        <img class="ml-1 dropdown-toggle" data-toggle="dropdown" src="{{ asset('newassets/img/Icon awesome-caret-down.png') }}">
                        <div style="padding:0 !important" class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"><img src="{{ asset('newassets/img/icon-edit-1.png') }}"> Action</a>
                        </div>
                    </span>
                </li>
            </ul>
        </div>
    </nav>
</div>
<div class="new-parent">
    @if(count($statuses)>0)
    @foreach($statuses as $status)
    <?php
        $bg_color="";
        if($status->pipeline_id==2){
            $bg_color="color2";
        }elseif ($status->pipeline_id==3) {
            $bg_color="color3";
        }else{
            $bg_color="";
        }
        $op_count = \App\CRMOpportunities::where(['op_status'=>$status->pipeline_status, 'tenant_id'=>auth()->user()->tenant_id])->count();
        $op_sum = \App\CRMOpportunities::where(['op_status'=>$status->pipeline_status, 'tenant_id'=>auth()->user()->tenant_id])->sum('value');
        $opportunity = \App\CRMOpportunities::select(
                        'crm_opportunities.id',
                        'crm_opportunities.lead_id',
                        'crm_leads.name',
                        'crm_opportunities.value',
                        'crm_opportunities.confidence',
                        'crm_opportunities.updated_at',
                        'crm_opportunities.created_at'
                        )
                        ->join('crm_leads', 'crm_leads.id', '=', 'crm_opportunities.lead_id')
                        ->where(['crm_opportunities.op_status'=>$status->pipeline_status, 'crm_opportunities.tenant_id'=>auth()->user()->tenant_id])
                        ->get();
    ?>        
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="{{ $bg_color }} new-col-child-header-title"> {{ $status->pipeline_status }}</span>
                <p class="new-col-child-header-subtitle m-0" id="total_{{ $status->id }}"> {{ $op_count }} Opportunities</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0" > Annualized value</p>
            <p class="price pull-right m-0" id="total_value_{{ $status->id }}"> {{$global->currency_symbol.$op_sum }}</p>
        </div>
        <!-- Column 1 Card Content Starts Here -->
        <div id="dragable_div_{{ $status->id }}" class="py-1 new-col-child-container ondragdiv" ondrop="drop(event, this,{{ $status->id }},'{{ $status->pipeline_status }}')" ondragover="allowDrop(event)">
            <div id="dropable_empty_{{ $status->id }}" class="dropable_empty" style="display:none"></div>             
            @if(count($opportunity)>0)
            @foreach ($opportunity as $opp)   
            <?php
                $name = explode(" ",$opp->name);
                $icon = '';
                if(count($name)>1){
                    for ($i=0;$i<2;$i++) {
                        $icon .= strtoupper(substr($name[$i],0,1));
                    }
                }else{
                    $icon = strtoupper(substr($name[0],0,1));
                }
            ?>
            <div id="dragable_col_{{ $opp->id }}" class="new-col-child-card-container py-1 px-2 grabbable" draggable="true" ondragstart="drag(event,{{ $opp->id }},{{ $status->id }},'{{ $status->pipeline_status }}')" ondrop="return false" ondragover="return false">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading"><a href="{{ route("admin.crm-leads.view", $opp->lead_id) }}" target="_blank" style="color: #6d91ba;">{{ $opp->name }}</a></h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">{{ $icon }}</span>
                            <span class="child-card-inner-content pull-left ml-2">
                                <span class="child-card-price m-0"> {{ $global->currency_symbol.$opp->value }}</span>
                                <h6 class="child-card-date-percent m-0">
                                    {{ $opp->confidence.'% on' }} 
                                    {{ ((strtotime($opp->updated_at) > 0)?$opp->updated_at:$opp->created_at)->format($global->date_format) }}
                                </h6> 
                            </span>
                    </div>
                </div>
            </div>
            {{-- <div id="dropable_empty_{{ $opp->id }}" style="border: 2px dotted blue;border-radius: 4px">

            </div> --}}
            @endforeach
            @else
            @endif
        </div>
        <!-- Column 1 Card Content Ends Here -->
    </div>
    @endforeach
    @endif
</div>
@endsection
@push('footer-script')
<script>
    // $(document).ready(function() {
    //     //$('.grabbable').draggable();
    //     $('body').on('drag', '.grabbable', function(e) {
    //         alert('asdf');
    //     });
    // });


    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev,id,from_status_id,from_status) {
        ev.dataTransfer.setData("text", ev.target.id);
        ev.dataTransfer.setData("id", id);
        ev.dataTransfer.setData("from_status_id", from_status_id);
        ev.dataTransfer.setData("from_status", from_status);
        $(".ondragdiv").addClass("dropable-div");
        // $(".dropable_empty").show(); 
        // $('#dropable_empty_'+div).hide();
        // $('.grabbable_'+div).show(); 
               
    }

    function drop(ev,el,to_status_id,to_status) {
        ev.preventDefault();
        var id = ev.dataTransfer.getData("id");
        var from_status = ev.dataTransfer.getData("from_status");
        var from_status_id = ev.dataTransfer.getData("from_status_id");
        var data = ev.dataTransfer.getData("text");
        el.appendChild(document.getElementById(data));
        $(".ondragdiv").removeClass("dropable-div");
        console.log(id);
        console.log(status);
        console.log($(this).data('status'));
        var token = "{{ csrf_token() }}";

        $.ajax({
            url: "/admin/opportunity/movestatus",
            method: 'post',
            data: {'_token': token, 'id': id,'from_status':from_status,'to_status':to_status},
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                //console.log(result.message);
                if (result.error == 0) {
                    $("#total_"+to_status_id).html(result.to_total + " Opportunities");
                    $("#total_value_"+to_status_id).html(result.to_total_value);

                    $("#total_"+from_status_id).html(result.from_total + " Opportunities");
                    $("#total_value_"+from_status_id).html(result.from_total_value);
                    $.toast({
                        heading: 'Success',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
        // $(".dropable_empty").hide();
        // $(".grabbable").show();         
    }
    
    
</script>
@endpush    
