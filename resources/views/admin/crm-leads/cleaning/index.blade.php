@section('cleaning_booking_detail_grid')
@include('admin.crm-leads.cleaning.booking_detail_grid')
@endsection

@section('end_of_lease_detail_grid')
@include('admin.crm-leads.cleaning.end_of_lease_detail_grid')
@endsection

@section('questions_grid')
@include('admin.crm-leads.cleaning.questions_grid')
@endsection
<input id="op_job_id" value="{{ $jobs_cleaning->job_id }}" type="hidden"/>
<div class="card card-removaltab">
    <div class="card-body padding-0">
        <div class="row row-margin-tp">
            <div class="col-md-6">
                <div id="cleaning_booking_detail_grid">
                    @yield('cleaning_booking_detail_grid')
                </div>
            </div>
            <div class="col-md-6">
                <div id="end_of_lease_detail_grid">
                    @yield('end_of_lease_detail_grid')
                </div>
            </div>
        </div>
        <div class="row mt-3 row-margin-bt">
            <div class="col-md-6">
                <div id="questions_grid">
                    @yield('questions_grid')
                </div>
            </div>
        </div>
    </div>
</div>