<li class="top-notifications">
    <div class="message-center">
        <a href="javascript:;" class="show-all-notifications">
            <div class="user-img">
                <span class="btn btn-circle btn-info"><i class="icon-list"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">{{ __('email.newTask.subject') }} - {{ ucfirst($notification->data['heading']) }}</span> <span class="time">@if($notification->data['created_at']){{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->data['created_at'])->diffForHumans() }}@endif</span>
            </div>
        </a>
    </div>
</li>
