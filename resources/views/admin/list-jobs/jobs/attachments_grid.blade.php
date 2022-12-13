
<style>
    .att_thumb:hover{
        opacity:0.8;
    }
</style>
<?php
$c_signature=0;
?>
@if(count($job_legs))
<div class="form-group row mt-1">
    <h3 class="col-lg-6" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Customer Signature</h3>
</div>
<div class="card" style="border: none!important">
<div class="card-body">
    <div class="row">    
            @foreach($job_legs as $leg)
            <?php
                if($leg->customer_sign!=NULL && $leg->customer_sign!=""){
                        $customer_sign = substr($leg->customer_sign, strrpos($leg->customer_sign, '/public') + 1);
                        $customer_sign = url('/'.$customer_sign);  
                        $c_signature=1;
            ?>
            <div class="col-6 mb-4">
				<div class="card att_thumb" style="height: 150px;">

											<h6 class="d-flex font-weight-normal flex-nowrap mb-0" style="font-size: 12px;">
												{{-- <a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" title="{{ $attachment->attachment_type }}" class="text-default mr-2"> --}}
												    <img src="{{$customer_sign}}" style="max-height:150px;margin: 0 auto;"/>
												{{-- </a> --}}
											</h6>

									
				</div>
            </div>
            <?php
            }
                // $path_info = pathinfo($attachment->attachment_content);
                // $f_type = $path_info['extension'];
                
                // $image_url = substr($attachment->attachment_content, strrpos($attachment->attachment_content, '/public' )+1)."\n";
            ?>            
            @endforeach    
            <?php
                if($c_signature==0){
                    echo 'No signature found.';
                }
            ?>
    </div>
</div>
</div>
@endif
@if($attachments)
<div class="form-group row mt-1">
    <h3 class="col-lg-6" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Attachments</h3>
</div>
<div class="card" style="border: none!important">
<div class="card-body">
    <div class="row">    
            @foreach($attachments as $attachment)
            <?php
                $path_info = pathinfo($attachment->attachment_content);
                if(is_array($path_info)){
                    $f_type = isset($path_info['extension'])?$path_info['extension']:'';
                }else{
                    $f_type="";    
                }
                $image_url = substr($attachment->attachment_content, strrpos($attachment->attachment_content, '/public' )+1)."\n";
            ?>
            <div class="col-2 mb-4">
                @if($f_type=="pdf")
                <div class="card att_thumb" style="height: 100px;">
											<h6 class="d-flex font-weight-normal flex-nowrap mb-0" style="font-size: 12px;">
												<a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" title="{{ $attachment->attachment_type }}" class="text-default mr-2">
												    <img src="{{ url('/public/img/pdf-bg.png') }}" style="max-height:100px"/>
												    {!! \Illuminate\Support\Str::limit($attachment->attachment_type, 30,'....') !!}
												    </a>
											</h6>

									
				</div>
				@else
				<div class="card att_thumb" style="height: 100px;">

											<h6 class="d-flex font-weight-normal flex-nowrap mb-0" style="font-size: 12px;">
												<a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" title="{{ $attachment->attachment_type }}" class="text-default mr-2">
												    <img src="/{{$image_url}}" style="max-height:100px"/>
												    {!! \Illuminate\Support\Str::limit($attachment->attachment_type, 30,'....') !!}
												    </a>
											</h6>

									
				</div>
				@endif
            </div>
            @endforeach               
    </div>
</div>
</div>
@endif