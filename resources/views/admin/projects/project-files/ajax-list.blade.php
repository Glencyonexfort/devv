@forelse($project->files as $file)
<li class="list-group-item">
    <div class="row">
        <div class="col-md-9">
            {{ $file->filename }}
        </div>
        <div class="col-md-3">
            @if($file->external_link != '')
            <a target="_blank" href="{{ $file->external_link }}"
               data-toggle="tooltip" data-original-title="View"
               class="btn btn-info btn-circle"><i
                    class="fa fa-search"></i></a>

            @elseif(config('filesystems.default') == 'local')
            <a target="_blank" href="{{ asset('user-uploads/project-files/'.$project->id.'/'.$file->hashname) }}"
               data-toggle="tooltip" data-original-title="View"
               class="btn btn-info btn-circle"><i
                    class="fa fa-search"></i></a>

            @elseif(config('filesystems.default') == 's3')
            <a target="_blank" href="{{ $url.'project-files/'.$project->id.'/'.$file->filename }}"
               data-toggle="tooltip" data-original-title="View"
               class="btn btn-info btn-circle"><i
                    class="fa fa-search"></i></a>
            @elseif(config('filesystems.default') == 'google')
            <a target="_blank" href="{{ $file->google_url }}"
               data-toggle="tooltip" data-original-title="View"
               class="btn btn-info btn-circle"><i
                    class="fa fa-search"></i></a>
            @elseif(config('filesystems.default') == 'dropbox')
            <a target="_blank" href="{{ $file->dropbox_link }}"
               data-toggle="tooltip" data-original-title="View"
               class="btn btn-info btn-circle"><i
                    class="fa fa-search"></i></a>
            @endif

            @if(is_null($file->external_link))
            <a href="{{ route('admin.files.download', $file->id) }}"
               data-toggle="tooltip" data-original-title="Download"
               class="btn btn-inverse btn-circle"><i
                    class="fa fa-download"></i></a>

            <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}"
               data-pk="list" class="btn btn-danger btn-circle sa-params"><i class="fa fa-times"></i></a>
            @endif
            <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
        </div>
    </div>
</li>
@empty
<li class="list-group-item">
    <div class="row">
        <div class="col-md-10">
            @lang('messages.noFileUploaded')
        </div>
    </div>
</li>
@endforelse