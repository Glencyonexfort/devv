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
  
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
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
  
        <div class="collapse navbar-collapse" id="navbarNavDropdown2">
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
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="new-col-child-header-title"> {{ $status->pipeline_status }}</span>
                <p class="new-col-child-header-subtitle m-0"> 3 Opportunities</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0"> Annualized value</p>
            <p class="price pull-right m-0"> $8,500</p>
        </div>
        <!-- Column 1 Card Content Starts Here -->
        <div class="py-1 new-col-child-container">
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Holloway Removals</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $5,000.46</span>
                                <h6 class="child-card-date-percent m-0">100%</h6> 
                            </span>
                    </div>
                </div>
            </div>
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Bluth Company (Example Lead)</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $3,000</span>
                                <h6 class="child-card-date-percent m-0">100% on 4/26/2020</h6> 
                            </span>
                    </div>
                </div>
            </div>
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Wayne Enterprises (Example Lead)</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $500</span>
                                <h6 class="child-card-date-percent m-0">75% on 4/25/2020</h6> 
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column 1 Card Content Ends Here -->
    </div>
    @endforeach
    @endif
    <!-- Column 1 Starts Here-->
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="new-col-child-header-title"> Demo Completed</span>
                <p class="new-col-child-header-subtitle m-0"> 3 Opportunities</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0"> Annualized value</p>
            <p class="price pull-right m-0"> $8,500</p>
        </div>
        <!-- Column 1 Card Content Starts Here -->
        <div class="py-1 new-col-child-container">
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Holloway Removals</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $5,000.46</span>
                                <h6 class="child-card-date-percent m-0">100%</h6> 
                            </span>
                    </div>
                </div>
            </div>
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Bluth Company (Example Lead)</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $3,000</span>
                                <h6 class="child-card-date-percent m-0">100% on 4/26/2020</h6> 
                            </span>
                    </div>
                </div>
            </div>
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Wayne Enterprises (Example Lead)</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $500</span>
                                <h6 class="child-card-date-percent m-0">75% on 4/25/2020</h6> 
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column 1 Card Content Ends Here -->
    </div>
    <!-- Column 1 Ends Here -->
    
        
    <!-- Column 2 Starts Here-->    
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="new-col-child-header-title"> Proposal Sent</span>
                <p class="new-col-child-header-subtitle m-0"> 1 Opportunity</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0"> Annualized value</p>
            <p class="price pull-right m-0"> $2,000</p>
        </div>
        <!-- Column 2 Card Content Starts Here -->
        <div class="py-1 new-col-child-container">
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Metro Movers</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $2,000.46</span>
                                <h6 class="child-card-date-percent m-0">50% on 4/30/2020</h6> 
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column 2 Card Content Ends Here -->
    </div>
    <!-- Column 2 Ends Here-->


    <!-- Column 3 Starts Here-->
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="new-col-child-header-title"> Contract Sent</span>
                <p class="new-col-child-header-subtitle m-0"> 0 Opportunities</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0"> Annualized value</p>
            <p class="price pull-right m-0"> $0</p>
        </div>
        <!-- Column 3 Card Content Starts Here -->
        <div class="py-1 new-col-child-container">
            <div class="no-matching-opprtunities">
                No matching opportunities 
            </div>
        </div>
        <!-- Column 3 Card Content Ends Here -->
    </div>
    <!-- Column 3 Ends Here-->


    <!-- Column 4 Starts Here-->
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="won new-col-child-header-title"> Won</span>
                <p class="new-col-child-header-subtitle m-0"> 1 Closed</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0"> Annualized value</p>
            <p class="price pull-right m-0"> $15,000</p>
        </div>
        <!-- Column 4 Card Content Starts Here -->
        <div class="py-1 new-col-child-container">
            <div class="new-col-child-card-container py-1 px-2">
                <div class="new-col-child-card p-2">
                    <h6 class="new-col-child-card-heading">Metro Movers</h6>
                    <div class="child-card-content">
                            <span class="pull-left child-card-icon">TM</span>
                            <span class="pull-left ml-2">
                                <span class="child-card-price m-0"> $15,000</span>
                                <h6 class="child-card-date-percent m-0">Closed on 4/24/2020</h6> 
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column 4 Card Content Ends Here -->
    </div>
    <!-- Column 4 Ends Here-->


    <!-- Column 5 Starts Here-->
    <div class="new-col-child">
        <div class="new-col-child-header py-0 p-2">
                <span class="lost new-col-child-header-title"> Lost</span>
                <p class="new-col-child-header-subtitle m-0"> 0 CLOSED</p>
        </div>
        <div class="new-col-child-subheader py-0 p-2">
            <p class="text-spacing pull-left m-0"> Annualized value</p>
            <p class="price pull-right m-0"> $0</p>
        </div>
        <!-- Column 5 Card Content Starts Here -->
        <div class="py-1 new-col-child-container">
            <div class="no-matching-opprtunities">
                No matching opportunities 
            </div>
        </div>
        <!-- Column 5 Card Content Ends Here -->
    </div>
    <!-- Column 5 Ends Here-->


</div>


@endsection
