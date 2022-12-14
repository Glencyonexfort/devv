@forelse($project->files as $file)
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-9">
                {{ $file->filename }}
            </div>
            <div class="col-md-3">
                <a target="_blank"
                   @if(!is_null($file->external_link))
                   href="{{ $file->external_link }}"
                   @elseif(!is_null($file->dropbox_link))
                   href="{{ $file->dropbox_link }}"
                   @else
                   href="{{ asset('user-uploads/project-files/'.$project->id.'/'.$file->hashname) }}"
                   @endif
                   data-toggle="tooltip" data-original-title="View"
                   class="btn btn-info btn-circle"><i
                            class="fa fa-search"></i></a>
                
                @if(is_null($file->external_link))
                &nbsp;&nbsp;
                <a
                        @if(!is_null($file->dropbox_link))
                        href="{{ str_replace('dl=0', 'dl=1', $file->dropbox_link) }}"
                        @else
                        href="{{ route('client.files.download', $file->id) }}"
                        @endif

                        data-toggle="tooltip" data-original-title="Download" class="btn btn-inverse btn-circle"><i class="fa fa-download"></i></a>
                @if($file->user_id == $user->id)
                    &nbsp;&nbsp;
                    <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}" class="btn btn-danger btn-circle sa-params" data-pk="list"><i class="fa fa-times"></i></a>
                @endif
                @endif

                <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </li>
@empty
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-10">
                @lang('messages.newFileUploadedToTheProject')
            </div>
        </div>
    </li>
@endforelse