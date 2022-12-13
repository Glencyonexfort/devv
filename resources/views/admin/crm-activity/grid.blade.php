<table class="table datatable-responsive" id="" style="width:100%;">
    <thead>
        <tr>
            <th style="width: 20px!important;"><input id="toggleAllActivity" type="checkbox" style="cursor: pointer;"/></th>
            <th style="width: 16%;">Type</th>
            <th style="width: 8%">Job#</th>
            <th style="width: 11%;">Lead</th>
            <th style="width: 10%;">From</th>
            <th style="width: 25%;">Subject</th>
            <th style="width: 50px">Date</th>
            @if($filter=="task")
            <th style="width: 50px">Assigned To</th>
            @endif
            <th class="text-right" style="width: 55px;">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($activities)>1)
        @foreach($activities as $data)
        {{-- $activities = $data --}}
        <?php
        if($data->opportunity=='N'){            
            $href = ($data->op_type=="Cleaning")?route("admin.list-jobs-cleaning.view-job", $data->job_id):route("admin.list-jobs.view-job", $data->job_id);
        }else{
            $href = route("admin.crm-leads.view", [$data->lead_id, $data->crm_opportunity_id]);
        }
            $tcolor=($data->log_type==11 || $data->log_type==14)?"#eee":"#000";
        ?>
        <tr>
            <td style="width: 3%!important;">
                <input class="activities-check" name="activities" type="checkbox" value="{{ $data->id }}" style="cursor: pointer;" />
            </td>
            <td>
                @if($data->log_type=="Task")
                    <span style="display:block;padding:2px 6px;color:#fff;background-color:#999">{{ $data->log_type }}</span>
                @else
                    @foreach($sys_log_types as $type)
                        @if($type->id==$data->log_type)
                        {{-- <span class="type-icon" style="background-color: {{ $type->colour }}"></span> --}}
                        <span style="display:block;padding:2px 6px;color:{{ $tcolor }};background-color:{{ $type->colour }}">{{ $type->log_type }}</span>
                        @endif
                    @endforeach
                @endif
            </td>
            <td>
                {{-- @if($data->op_type=='Moving') --}}
                    <span class="w400">{{ $data->job_number }}</span>
                {{-- @endif --}}
            </td>
            <td>
                <a href="{{ $href }}" class="link">{{ $data->name }}</a>
            </td>
            <td>
                {{ $data->log_from }}
            </td>
            <td>
                {{ $data->log_subject }} <br/> <span class="muted">{{ strip_tags(str_limit($data->log_message,55)) }}</span>
            </td>
            <td>
                <span class="w500">{{ date('M d', strtotime($data->log_date)) }}</span>
            </td>
            @if($filter=="task")
            <td>
                {{ $data->user_name }}
            </td>
            @endif
            <td class="text-right">
                <div class="list-icons">
                    <div class="dropdown">
                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                            <i class="icon-menu"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item updateActivity" data-id="{{ $data->id }}" data-type="{{ $data->log_type }}"><i class="fa fa-check-square"></i> 
                                @if($data->log_type=='Task')
                                    {{ "Mark as Done" }}
                                @else 
                                    {{ "Mark as Read" }}
                                @endif
                            </a>												
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="8">No record found!</td>
        </tr>
        @endif
    </tbody>
</table>