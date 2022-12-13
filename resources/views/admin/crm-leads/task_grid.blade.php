{{-- @section('task_grid') --}}
@foreach($tasks as $task)
<?php
$task_date = date(isset($global->date_format)?$global->date_format:'d/m/Y',strtotime($task->task_date));
?>
    <div class=" header-elements-inline">
        <div class="page-title">
            <h5 class="d-flex">
                <button class="btn bg-dark btn-icon btn-sm task_grid_1_button">FM</button>
                <span class="font-weight-semibold lh16 task_grid_1_span">{{ str_limit($task->description,20) }}<br/>
                    <small class="d-block text-muted mg-0">{{ $task_date }}</small>
                </span>
            </h5>
        </div>

        <div class="header-elements broderline">
            <div class="list-icons">
                <div class="dropdown">
                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown">
                    <img class="task_grid_2_img" src="{{ asset('newassets/img/icon-edit-1.png') }}">
                        <!-- <i class="icon-menu9"></i> -->
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a data-taskid="{{ $task->id }}" data-leadid="{{ $lead_id }}"  class="task-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a>
                        <a data-taskid="{{ $task->id }}" data-leadid="{{ $lead_id }}"  class="task-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="update_task_form_grid_{{ $task->id }}" class="card-body light-blue-bg p10 hidden">
        <form id="update_task_form_{{ $task->id }}" class="custom-form" action="#">
            @csrf
            {{ Form::hidden('lead_id', $task->lead_id) }}
            {{ Form::hidden('task_id', $task->id) }}
            <div class="form-group">
                <label>Task Decription</label>
                <input name="description" type="text" class="form-control" value="{{ $task->description }}">
            </div>
            <?php
                //$task_date = Carbon\Carbon::createFromFormat($global->date_format, $task->task_date)->toDateString()
                //$task_date2 = date(isset($global->date_picker_format)?$global->date_picker_format:'dd/mm/yyyy',strtotime($task->task_date));
            ?>
    
    
            <div class="form-group">
                <label>Date</label>
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text"><i class="icon-calendar22"></i></span>
                    </span>
                    <input name="task_date" type="text" class="form-control daterange-single" value="{{ $task_date }}">
                </div>
            </div>
    
            <div class="form-group">
                <label>Time</label>
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text"><i class="icon-alarm"></i></span>
                    </span>
                    <input name="task_time" type="text" class="form-control pickatime" value="{{ $task->task_time }}"" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label>Assign User</label>
                <select name="user_assigned_id" data-placeholder="" class="form-control">
                    @foreach($users as $user)
                    <option value="{{ $user->id }}"
                            @if($user->id == $task->user_assigned_id)
                            selected=""
                            @endif
                            >{{ ucwords($user->name) }}</option>
                    @endforeach
                </select>
            </div>
    
            <div class="d-flex justify-content-start align-items-center m-t-10">
                <button class="btn btn-light task-edit-btn" data-taskid="{{ $task->id }}">Cancel</button>
                <button type="button" class="btn bg-blue ml-3 update_task_btn" data-taskid="{{ $task->id }}">Update</button>
            </div>
    
        </form>
        </div>
    @endforeach
{{-- @endsection --}}


