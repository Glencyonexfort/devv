@extends('layouts.app')
@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
@endsection
<style>
    .borderbottom{
        border-bottom: 1px solid #eee;        
        }
    .level-icon{
        font-size: 26px!important;
        color: #777;
    }
</style>
@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
            @include('sections.people_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">Role: {{$role->display_name}}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            
                            {{-- <div class="table-responsive">
                                <table class="table" id="permissions-table"> --}}
                                    @foreach($app_modules as $module)
                                    <?php
                                        $sub_modules = \App\Permission::where(['active'=>'1', 'module_id'=>$module->id])->get();
                                    ?>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <ul>
                                                    <h5 class="font-weight-semibold mt-2">
                                                        {{ $module->display_name }}
                                                    </h5>
                                                    @foreach($sub_modules as $sub_module)
                                                        <?php
                                                            $permission = \App\PermissionRole::where(['role_id'=>$role->id, 'permission_id'=>$sub_module->id, 'tenant_id'=>auth()->user()->tenant_id])->exists();
                                                            if(!($permission)){
                                                                $active = 'Y';
                                                            }else{
                                                                $active = 'N';
                                                            }
                                                            
                                                        ?>
                                                        <div class="row borderbottom">
                                                            <div class="col-sm-9">
                                                            <h6 class="font-weight-semibold mt-2 ml-4">
                                                                @if($sub_module->level==1)
                                                                    <i class="icon-arrow-right5 level-icon mr-2 ml-4"></i>
                                                                @elseif($sub_module->level==2)
                                                                    <i class="icon-arrow-right5 level-icon mr-2 ml-4"></i> <i class="icon-arrow-right5 level-icon mr-2"></i>
                                                                @endif
                                                                <span class="ml-2">{{ $sub_module->display_name }}</span>
                                                            </h6>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div class="switchery-demo pull-right" style="padding-top: 10px;">
                                                                    <input type="checkbox" name="role-permission" class="js-switch role-permission" value="{{ $active }}" data-color="#f96262" data-permission_id="{{ $sub_module->id }}"
                                                                    {{ $active == 'Y' ? 'checked=""' : '' }}
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                {{-- </table>

                            </div> --}}
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('footer-script')

<script>

var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });

    $('body').on('change', '.role-permission', function(e) {

        var has_access = $(this).val();
        var permission_id = $(this).data('permission_id');
        var role_id = {{ $role->id }};

        $.ajax({
            url: "/admin/peopleoperations/ajax-update-role-permissions",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "has_access":has_access,"permission_id":permission_id,"role_id":role_id

            },
            dataType: "json",
            beforeSend: function() {
                $(".preloader").show();
            },
            complete: function() {
                $(".preloader").hide();
            },
            success: function(result) {
              
                if (result.error == 0) {
                    $.toast({
                        heading: 'Success',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                    //..
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
    });
</script>

@endpush