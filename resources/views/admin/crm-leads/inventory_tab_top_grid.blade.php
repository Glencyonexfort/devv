<?php
    $truck_list = \App\JobsMovingLocalMoves::where(['tenant_id'=>auth()->user()->tenant_id])->get();
    // For Insurance value calculation        
    $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
?>
<div class="row">
    <div class="col-md-7">
        <div id="cbm_edit_view" class="row">
            <div class="col-md-4">
                <strong>Cubic Volume: <span id="total_cbm_view">{{ ($job->total_cbm>0)?$job->total_cbm:0 }}</span> m3</strong>
            </div>
            <div class="col-md-4">
                <strong>Goods Value: <span id="total_goods_value_view">{{$global->currency_symbol}}{{ number_format((float)$job->goods_value, 2, '.', ',') }}</span></strong>
            </div>
            <div class="col-md-4" style="padding: 0;">
                <strong>Insurance based on: <span>{{ strtoupper($job->insurance_based_on) }}</span></strong>
                <strong id="cbm_edit_btn" class="" style="margin-left: 5px;cursor: pointer;"><i class="icon-pencil"></i></strong>
            </div>
        </div>
        <div id="cbm_edit_form" class="row hidden">
            <input type="hidden" id="goods_value_per_cbm" value="{{ $goods_value_per_cbm }}"/>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Truck size to default value</label>
                    <select class="form-control" id="local_move_truck">
                        <option value="" disabled selected>Choose a truck..</option>
                        @foreach($truck_list as $truck)
                            <option value="{{ $truck->max_cbm }}">
                                {{ $truck->truck_size_in_ton.' T'}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Or enter CBM to default value</label>
                    <input type="number" id="total_cbm_field" name="total_cbm" class="form-control total_cbm" value="{{ $job->total_cbm }}" placeholder="0m3">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Or directly enter a value</label>
                    <input type="number" id="goods_value_field" name="goods_value" class="form-control" value="{{ $job->goods_value }}" placeholder="0.00">
                </div>
            </div>
            {{-- <div class="col-md-4">
                <select id="insurance_based_on_field" name="insurance_based_on" class="form-control">
                        <option value="cbm" @if($job->insurance_based_on=="cbm")selected=""@endif>CBM</option>
                        <option value="value" @if($job->insurance_based_on=="value")selected=""@endif>VALUE</option>
                </select>
            </div> --}}
            <div class="m-t-10 m-b-10 m-l-10">
                <button id="cancel_cbm_btn" class="btn btn-light">Cancel</button>
                <button id="update_cbm_btn" type="button" class="btn btn-success ml-2">Update</button>
            </div>
        </div>
        {{-- <div class="bottom_box">
            <p class="muted">Click on Calculate or manually edit the Cubic volume</p>
        </div> --}}
    </div>
    <div class="col-md-4" style="text-align: right;">
        <span>
            <button type="button" class="btn btn-sm btn-light inventoryPdfGenerate" data-jobid="{{$job_id}}" data-type="Moving" style="padding: 6px;"><i class="icon-clipboard3"></i> Generate Inventory PDF</button>
            <button type="button" id="inventoryPdfDownload" class="btn btn-sm btn-light ml-2 inventoryPdfDownload" style="padding: 6px;" data-jobid="{{$job_id}}" @if (empty($job) || $job->inventory_file_name == null) disabled @endif><i class="icon-file-pdf"></i> Download</button>
        </span>
    </div>
    <div class="col-md-1" style="text-align: right;">
        <div class="btn_calculate">
            <input class="btn-calculate btn bg-teal-400" type="button" value="@lang('modules.listJobs.calculate')" style="padding: 6px 12px;">
        </div>
    </div>
</div>