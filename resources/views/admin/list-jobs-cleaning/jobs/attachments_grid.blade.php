
<style>
    .att_thumb:hover{
        opacity:0.8;
    }
</style>
<div class="card-body">
    <div class="row">    
            @if($attachments)
            @foreach($attachments as $attachment)
            <?php
                $path_info = pathinfo($attachment->attachment_content);
                $f_type = $path_info['extension'];
                
                $image_url = substr($attachment->attachment_content, strrpos($attachment->attachment_content, '/public' )+1)."\n";
            ?>
            <div class="col-2 mb-4">
                @if($f_type=="pdf")
                <div class="card att_thumb" style="height: 100px;">
											<h6 class="d-flex font-weight-normal flex-nowrap mb-0" style="font-size: 12px;">
												<a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" title="{{ $attachment->attachment_type }}" class="text-default mr-2">
												    <img src="/public/img/pdf-bg.png" style="max-width:100px"/>
												    {!! \Illuminate\Support\Str::limit($attachment->attachment_type, 30,'....') !!}
												    </a>
											</h6>

									
				</div>
				@else
				<div class="card att_thumb" style="height: 100px;">

											<h6 class="d-flex font-weight-normal flex-nowrap mb-0" style="font-size: 12px;">
												<a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" title="{{ $attachment->attachment_type }}" class="text-default mr-2">
												    <img src="/{{$image_url}}" style="max-width:100px"/>
												    {!! \Illuminate\Support\Str::limit($attachment->attachment_type, 30,'....') !!}
												    </a>
											</h6>

									
				</div>
				@endif
            </div>
            @endforeach
            @endif   
    </div>
</div>