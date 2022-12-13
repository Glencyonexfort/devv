@if(count($attachments)>0)
<label style="font-size: 24px;font-weight: 300;">Attachments</label>
<ul class="list-group list-group-flush">
    @foreach($attachments as $data)
        <li class="list-group-item d-flex justify-content-between" style="padding: 0px 0px 10px 0px;">
            <a class="attachment-link" target="_blank" href="\admin\moving-settings\viewTemplateAttachment\{{ $data->id}}">
                <i class="fa fa-paperclip"></i>{{ $data->attachment_file_name }}
            </a>
                <a class="badge badge-danger badge-pill remove-attachment" href="" data-id="{{ $data->id }}"><i class="fa fa-times"></i></a>
    </li>
    @endforeach
</ul>
@endif

