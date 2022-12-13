@if($attachment)
    @foreach($attachment as $key=>$data)
        <li class="badge-light badge-striped badge-striped-left border-left-info d-flex justify-content-between mb-2">
            <a href="{{ $data['path'] }}">{{ $data['name'] }}</a>
            <a href="" class="remove_attachment_btn" style="margin-left: 4rem;" data-key="{{ $key }}" data-type="{{ $data['type'] }}" data-noteid=""><i class="fa fa-times"></i></a>
            <input type="hidden" name="template_attachment" id="template_attachment" value="{{ $data['name'] }}">
        </li>
    @endforeach
@endif