<?php
	function in_array_any($needles, $haystack) {
		return empty(array_diff($needles, $haystack));
	}
?>
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">
	<!-- Sidebar mobile toggler -->
	<div class="sidebar-mobile-toggler text-center">
		<a href="#" class="sidebar-mobile-main-toggle">
			<i class="icon-arrow-left8"></i>
		</a>
		Navigation
		<a href="#" class="sidebar-mobile-expand">
			<i class="icon-screen-full"></i>
			<i class="icon-screen-normal"></i>
		</a>
	</div>
	<!-- /sidebar mobile toggler -->
	<!-- Sidebar content -->
	<div class="sidebar-content">

		<!-- Main navigation -->
		<div class="card card-sidebar-mobile">
			<ul class="nav nav-sidebar" data-nav-type="accordion">
{{-- #########################    START::CRM Module     ####################################### --}}
			@if(!in_array_any(['inbox','opportunities','crm_settings','customers'],$user_permissions))
				<li class="nav-item-header">
					<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.crm')</div> <i class="icon-menu" title="@lang('app.menu.crm')"></i>
				</li>
				@if(!in_array('inbox',$user_permissions))
				<li class="nav-item"><a href="{{ route('admin.inbox') }}" class="nav-link">
						<img style="height:18px; width:18px;" src="../../../../../newassets/img/inbox@2x.png">
						<span style="margin-left:20px;">@lang('app.menu.inbox') </span>
						@if($new_log_count>0)<span class="badge count-bg-color align-self-center" id="allActvities">{{ $new_log_count }}</span>@endif
					</a>
				</li>		
				@endif		
				{{-- <li class="nav-item nav-item-submenu">
					<a href="{{ route('admin.task.index') }}" class="nav-link">

						<img src="../../../../../newassets/img/idea@2x.png">
						<span>@lang('app.menu.opportunities')</span> 
						@if($new_opportunity_count>0)<span class="badge count-bg-color align-self-center">{{ $new_opportunity_count }}</span>@endif
					</a>

					<ul class="nav nav-group-sub">
						<li class="nav-item"><a href="{{ route('admin.opportunity.pipeline') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.pipeline')</a></li>
						<li class="nav-item"><a href="{{ route('admin.opportunity.listdata') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.list')</a></li>
					</ul>
				</li> --}}
				@if(!in_array('opportunities',$user_permissions))
				<li class="nav-item">
					<a href="{{ route('admin.opportunity.listdata') }}" class="nav-link" style="width: 80%;float: left;">

						<img src="../../../../../newassets/img/idea@2x.png">
						<span>@lang('app.menu.opportunities')</span> 
						@if($new_opportunity_count>0)<span class="badge count-bg-color align-self-center">{{ $new_opportunity_count }}</span>@endif
					</a>
					<span id="create_new_opp_btn" data-toggle="modal" data-target="#add_new_lead_popup" class="align-self-center ml-auto cursor-pointer" style="width:20%;top:12px">
						<!-- <i class="fa fa-plus-square" style="margin-top: 10px;margin-left: 15px;"></i> -->
						<img style="margin-top: 11px;margin-left: 15px;" height="14px" width="14px" src="../../../../../newassets/img/add (2)@2x.png">
					</span>
				</li>
				@endif

				@if(!in_array('crm_settings',$user_permissions))
				<li class="nav-item"><a href="{{ route('admin.email-templates.index') }}" class="nav-link">

						<img style="margin-top:3.1px;margin-left: 4px;" height="15px" width="15px" src="../../../../../newassets/img/next@2x.png">
						<span style="margin-left:19px;">@lang('app.menu.crmSettings') </span></a>
				</li>
				@endif

				@if(!in_array('customers',$user_permissions))
				{{-- <li class="nav-item">
					<a href="{{ route('admin.crm-leads.index') }}" class="nav-link">
						<img style="width:17px; height:17px;" src="../../../../../newassets/img/statistics@2x.png">
						<span style="margin-left: 22px;">@lang('app.menu.customers') </span> 
					</a>				

				</li> --}}
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link">
						<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/statistics.png">
						<span> @lang('app.menu.customers') </span>
					</a>
					@if(!in_array('residentail',$user_permissions))
					<ul class="nav nav-group-sub">
						<li class="nav-item"><a href="{{ route('admin.crm-leads.residential') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.residential')</a></li>
					</ul>
					@endif
					@if(!in_array('commercial',$user_permissions))
					<ul class="nav nav-group-sub">
						<li class="nav-item"><a href="{{ route('admin.crm-leads.commercial') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.commercial')</a></li>
					</ul>
					@endif
				</li>
				@endif
				@endif
{{----------------------------    END::CRM Module     -----------------------------------------}}

{{-- #########################    START::Removal Module     ####################################### --}}
@if(!in_array_any(['jobs','list_jobs','job_schedule','removal_settings'],$user_permissions))
				@if($module_removals)
				<li class="nav-item-header">
					<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.removals')</div> <i class="icon-menu" title="@lang('app.menu.removals')"></i>
				</li>
				@if(in_array('moving',$modules))
				@if(!in_array('jobs',$user_permissions))
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link">
						<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png">
						<span> @lang('app.menu.jobs') </span>
						@if($new_job_moving_count>0)<span class="badge count-bg-color align-self-center">{{ $new_job_moving_count }}</span>@endif
					</a>
					<ul class="nav nav-group-sub">
					@if(!in_array('list_jobs',$user_permissions))
						<li class="nav-item"><a href="{{ route('admin.list-jobs.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.list_jobs')</a></li>
					@endif
					@if(!in_array('job_schedule',$user_permissions))
						<li class="nav-item"><a href="{{ route('admin.list-jobs.job-schedule') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.jobSchedule')</a></li>
					@endif
					{{-- @if(!in_array('daily_dairy',$user_permissions))
						<li class="nav-item"><a href="{{ route('admin.jobs.daily-diary') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.dailyDiary')</a></li>
					@endif --}}
					</ul>
				</li>
				@endif
				@if(!in_array('backloading',$user_permissions))
				<li class="nav-item">
					<a href="{{ route('admin.backloading.index') }}" class="nav-link">
						<i class="icon-truck" style="color: #999ca0;margin-right:0px"></i>
						<span>@lang('app.menu.backloading') </span>
					</a>
				</li>
				@endif
				@if(!in_array('vehicle_unavailability',$user_permissions))
				<li class="nav-item"><a href="{{ route('admin.vehicle-unavailability') }}" class="nav-link">
					<i class="icon-truck" style="color: #999ca0;margin-right:0px"></i>
					<span>@lang('app.menu.vehicleUnavailability') </span></a></li>
				@endif
				@endif
				@if(!in_array('removal_settings',$user_permissions))
				<li class="nav-item"><a href="{{ route('admin.vehicles.index') }}" class="nav-link">
						<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png">
						<span>@lang('app.menu.removalSettings') </span></a></li>
				@endif
{{----------------------------    END::Removal Module     -----------------------------------------}}

{{-- #########################    START::Storage Module     ####################################### --}}		
@if(!in_array_any(['units_list','storage_settings'],$user_permissions))
				<li class="nav-item-header">
					<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.storage')</div> <i class="icon-menu" title="@lang('app.menu.storage')"></i>
				</li>	
				@if(!in_array('units_list',$user_permissions))
				<li class="nav-item"><a href="{{ route('admin.units-list.index') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png">
					<span>@lang('app.menu.unitslist') </span></a></li>
				@endif
				@if(!in_array('storage_settings',$user_permissions))
				<li class="nav-item"><a href="{{ route('admin.storage-types') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png">
					<span>@lang('app.menu.storageSettings') </span></a></li>					
				@endif
				@endif
@endif
{{----------------------------    END::Storage Module     -----------------------------------------}}

					
				@if(in_array('moving',$modules) && auth()->user()->hasRole('admin'))
				{{-- <li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link">
										<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png">
						<span> @lang('app.menu.removalSettings') </span></a>
					<ul class="nav nav-group-sub">
						<li class="nav-item"><a href="{{ route('admin.companies.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.companies')</a></li>
				<li class="nav-item"><a href="{{ route('admin.job-templates.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.job_templates')</a></li>
				<li class="nav-item"><a href="{{ route('admin.vehicles.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.vehicles')</a></li>
				<li class="nav-item"><a href="{{ route('admin.enableAutoQuote.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.enableAutoQuote')</a></li>
				<li class="nav-item"><a href="{{ route('admin.pricingSettings.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.pricingSettings')</a></li>
			</ul>
			</li> --}}
			@endif
			@endif

{{-- #########################    START::Cleaning Module     ####################################### --}}

			@if($module_cleaning)
			<li class="nav-item-header">
				<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.cleaning')</div> <i class="icon-menu" title="@lang('app.menu.cleaning')"></i>
			</li>
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png">
					<span> @lang('app.menu.jobs') </span>
				</a>
				<ul class="nav nav-group-sub">
					<li class="nav-item"><a href="{{ route('admin.list-jobs-cleaning.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.list_jobs')</a></li>
					<li class="nav-item"><a href="{{ route('admin.team-roster-cleaning.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.team_roster')</a></li>
					<li class="nav-item"><a href="{{ route('admin.list-jobs-cleaning.team-calendar') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.teamCalendar')</a></li>
				</ul>
			</li>
			<li class="nav-item"><a href="{{ route('admin.generalQuoteFormSettings.index') }}" class="nav-link"><img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png"><span>@lang('app.menu.cleaningSettings') </span></a></li>
			@endif

{{----------------------------    END::Cleaning Module     -----------------------------------------}}

{{-- #########################    START::Finance Module     ####################################### --}}
@if(!in_array_any(['invoices','finance_settings'],$user_permissions))
			<li class="nav-item-header">
				<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.finance')</div>
				<i class="icon-menu" title="@lang('app.menu.finance')"></i>
			</li>			
			@if(!in_array('invoices',$user_permissions))
			<li class="nav-item"><a href="{{ route('admin.all-invoices.index') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/sigma@2x.png">
					<span>@lang('app.menu.invoices') </span></a>
			</li>
			@endif

			@if(!in_array('finance_settings',$user_permissions))
			<li class="nav-item"><a href="{{ route('admin.products.index') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png">
					<span>@lang('app.menu.financeSettings') </span></a>
			</li>
			@endif
@endif			

{{----------------------------    END::Finance Module     -----------------------------------------}}

{{-- #########################    START::People Operation Module     ####################################### --}}
@if(!in_array_any(['employees','people_settings'],$user_permissions))
			<li class="nav-item-header">
				<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.peopleOperations')</div>
				<i class="icon-menu" title="@lang('app.menu.peopleOperations')"></i>
			</li>
			@if(!in_array('employees',$user_permissions))
			<li class="nav-item"><a href="{{ route('admin.list-employees.index') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png">
					<span>@lang('app.menu.peopleOperationsEmployees') </span></a>
			</li>
			@endif

			@if(!in_array('people_settings',$user_permissions))
			<li class="nav-item"><a href="{{ route('admin.manage-roles') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png">
					<span>@lang('app.menu.peopleSettings') </span></a>
			</li>
			@endif
@endif

{{----------------------------    END::People Operation Module     -----------------------------------------}}

{{-- #########################    START::Dashboard Module     ####################################### --}}
@if(!in_array_any(['dashboard_removals','reporting_removals'],$user_permissions))
			<li class="nav-item-header">
				<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.dashboard')</div> <i class="icon-menu" title="@lang('app.menu.dashboard')"></i>
			</li>
			@if(!in_array('dashboard_removals',$user_permissions))
			<li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/browser@2x.png">
					<span>@lang('app.menu.dashboard') </span></a>
			</li>
			@endif

			@if(!in_array('reporting_removals',$user_permissions) || (!in_array('sales_pipeline',$user_permissions) && !in_array('operations_report',$user_permissions)))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/statistics.png">
					<span> @lang('app.report.reporting') </span>
					{{-- @if($new_job_moving_count>0)<span class="badge count-bg-color align-self-center">{{ $new_job_moving_count }}</span>@endif --}}
				</a>
				@if(!in_array('sales_pipeline',$user_permissions))
				<ul class="nav nav-group-sub">
					<li class="nav-item"><a href="{{ route('admin.sales-report') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.report.salesPipeline')</a></li>
				</ul>
				@endif
				@if(!in_array('operations_report',$user_permissions))
				<ul class="nav nav-group-sub">
					<li class="nav-item"><a href="{{ route('admin.crm-operations-report') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.report.operationsReport')</a></li>
				</ul>
				@endif
				@if(!in_array('lead_report',$user_permissions))
				<ul class="nav nav-group-sub">
					<li class="nav-item"><a href="{{ route('admin.crm-lead-report') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.report.leadReport')</a></li>
				</ul>
				@endif
				@if(!in_array('daily_vehicle_check',$user_permissions))
				<ul class="nav nav-group-sub">
					<li class="nav-item"><a href="{{ route('admin.crm-daily-vehicle-check') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.report.dailyVehicleCheck')</a></li>
				</ul>
				@endif
			</li>
			@endif
@endif			

{{----------------------------    END::Dashboard Module     -----------------------------------------}}

{{-- #########################    START::Settings Module     ####################################### --}}
@if(!in_array_any(['settings','documentation','support','manage_subscription'],$user_permissions))
			<li class="nav-item-header">
				<div class="text-capitalize font-size-xs line-height-xs">@lang('app.menu.settings')</div> <i class="icon-menu" title="@lang('app.menu.settings')"></i>
			</li>
			@if(!in_array('settings',$user_permissions))
			<li class="nav-item">
				<a href="{{ route('admin.settings.index') }}" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/settings-1@2x.png"><span> @lang('app.menu.settings')</span>
				</a>
			</li>
			@endif

			@if(!in_array('documentation',$user_permissions))
			<li class="nav-item">
				<a href="https://docs.onexfort.com/docs/" target="_blank" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png"><span> @lang('app.menu.documentation')</span>
				</a>
			</li>
			@endif

			@if(!in_array('support',$user_permissions))
			<li class="nav-item">
				<a href="https://onexfort.freshdesk.com" target="_blank" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png"><span> @lang('app.menu.support')</span>
				</a>
			</li>
			@endif

			@if(!in_array('manage_subscription',$user_permissions))
			<li class="nav-item">
				<a href="javascript:void(0)" data-cb-type="portal" class="nav-link">
					<img style="margin-left: 2px;" height="18px" width="18px" src="../../../../../newassets/img/license@2x.png"><span> @lang('app.menu.manageSubscription')</span>
				</a>
			</li>
			@endif
@endif			

{{----------------------------    END::Dashboard Module     -----------------------------------------}}

			{{-- <!-- @if(in_array('estimates',$modules) || in_array('invoices',$modules) || in_array('payments',$modules) || in_array('expenses',$modules) )
				<li class="nav-item nav-item-submenu">
					<a href="{{ route('admin.finance.index') }}" class="nav-link"><i class="fa fa-money"></i> <span> @lang('app.menu.finance') @if($unreadExpenseCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif </span></a>
					<ul class="nav nav-group-sub">
						@if(in_array('estimates',$modules))
						<li class="nav-item"><a href="{{ route('admin.estimates.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.estimates')</a> </li>
						@endif

						@if(in_array('invoices',$modules))
						<li class="nav-item"><a href="{{ route('admin.all-invoices.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.invoices')</a> </li>
						@endif

						@if(in_array('expenses',$modules))
						<li class="nav-item"><a href="{{ route('admin.expenses.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.expenses') @if($unreadExpenseCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif</a> </li>
						@endif
					</ul>
				</li>
				@endif -->



			<!-- @if(in_array('clients',$modules))
				<li class="nav-item"><a href="{{ route('admin.clients.index') }}" class="nav-link"><i class="icon-people"></i> <span>@lang('app.menu.clients')</span></a></li>
				@endif
				@if(in_array('leads',$modules))
				<li class="nav-item"><a href="{{ route('admin.leads.index') }}" class="nav-link"><i class="ti-receipt"></i> <span> @lang('app.menu.lead')</span></a></li>
				@endif
				@if(in_array('projects',$modules))
				<li class="nav-item"><a href="{{ route('admin.projects.index') }}" class="nav-link"><i class="icon-layers"></i> <span>@lang('app.menu.projects') </span></a> </li>
				@endif

				@if(in_array('tasks',$modules))
				<li class="nav-item nav-item-submenu">
					<a href="{{ route('admin.task.index') }}" class="nav-link"><i class="ti-layout-list-thumb"></i> <span>@lang('app.menu.tasks')</span></a>
					<ul class="nav nav-group-sub">
						<li class="nav-item"><a href="{{ route('admin.all-tasks.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.tasks')</a></li>
						<li class="nav-item"><a href="{{ route('admin.taskboard.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('modules.tasks.taskBoard')</a></li>
						<li class="nav-item"><a href="{{ route('admin.task-calendar.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.taskCalendar')</a></li>
					</ul>
				</li>
				@endif -->


			<!-- @if(in_array('products',$modules))
				<li class="nav-item"><a href="{{ route('admin.products.index') }}" class="nav-link"><i class="icon-basket"></i> <span>@lang('app.menu.products') </span></a> </li>
				@endif -->


			<!-- her -->

			<!-- @if(in_array('timelogs',$modules))
				<li class="nav-item"><a href="{{ route('admin.all-time-logs.index') }}" class="nav-link"><i class="icon-clock"></i> <span>@lang('app.menu.timeLogs') </span></a> </li>
				@endif

				@if(in_array('tickets',$modules))
				<li class="nav-item"><a href="{{ route('admin.tickets.index') }}" class="nav-link"><i class="ti-ticket"></i> <span>@lang('app.menu.tickets')</span> @if($unreadTicketCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif</a> </li>
				@endif


				@if(in_array('employees',$modules))
				<li class="nav-item nav-item-submenu">
					<a href="{{ route('admin.employees.index') }}" class="nav-link"><i class="ti-user"></i> <span> @lang('app.menu.employees') </span></a>
					<ul class="nav nav-group-sub">
						<li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link"><i class="icon-circle"></i> @lang('app.menu.employeeList')</a></li>
					</ul>
				</li>
				@endif


				@if(in_array('attendance',$modules))
				<li class="nav-item"><a href="{{ route('admin.attendances.index') }}" class="nav-link"><i class="icon-clock"></i> <span>@lang('app.menu.attendance') </span></a> </li>
				@endif
				@if(in_array('holidays',$modules))
				<li class="nav-item"><a href="{{ route('admin.holidays.index') }}" class="nav-link"><i class="ti-calendar"></i> <span> @lang('app.menu.holiday')</span></a>
				</li>
				@endif -->


			<!-- @if(in_array('messages',$modules))
				<li class="nav-item"><a href="{{ route('admin.user-chat.index') }}" class="nav-link"><i class="icon-envelope"></i> <span>@lang('app.menu.messages') @if($unreadMessageCount > 0)<span class="label label-rouded label-custom pull-right">{{ $unreadMessageCount }}</span> @endif</span></a> </li>
				@endif

				@if(in_array('events',$modules))
				<li class="nav-item"><a href="{{ route('admin.events.index') }}" class="nav-link"><i class="icon-calender"></i> <span>@lang('app.menu.Events')</span></a> </li>
				@endif

				@if(in_array('leaves',$modules))
				<li class="nav-item"><a href="{{ route('admin.leaves.index') }}" class="nav-link"><i class="icon-logout"></i> <span>@lang('app.menu.leaves')</span></a> </li>
				@endif

				@if(in_array('notices',$modules))
				<li class="nav-item"><a href="{{ route('admin.notices.index') }}" class="nav-link"><i class="ti-layout-media-overlay"></i> <span>@lang('app.menu.noticeBoard') </span></a> </li>
				@endif -->


			<!-- /layout --> --}}

			</ul>
		</div>
		<!-- /main navigation -->

	</div>
	<!-- /sidebar content -->

</div>