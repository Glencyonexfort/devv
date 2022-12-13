<p>@lang('modules.lead.similarLead')</p>
<div style="max-height: 300px;overflow-y: scroll;">
<table class="table popup-table table-bordered">
    <thead>
        <th style="width:25%">@lang('modules.lead.lead_name')</th>
        <th style="width:25%">@lang('modules.lead.email')</th>
        <th style="width:20%">@lang('modules.lead.mobile')</th>
        <th style="width:15%">@lang('modules.lead.status')</th>
        <th style="width:15%">@lang('modules.lead.created')</th>
    </thead>
    <tbody>
        @foreach($leads as $lead)
        <?php
        $mobile = Illuminate\Support\Facades\DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contact_details.detail')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $lead->id, 'crm_contact_details.detail_type' => 'Mobile'])
            ->first();
        $email = Illuminate\Support\Facades\DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contact_details.detail')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $lead->id, 'crm_contact_details.detail_type' => 'Email'])
            ->first(); 
        ?>
        <tr>
            <td><a class="font-weight-bold txt-blue" href="{{ route("admin.crm-leads.view", $lead->id) }}">{{ $lead->name }}</a></td>
            <td style="overflow-wrap: break-word;">{{ ($email)? $email->detail:'' }}</td>
            <td>{{ ($mobile)? $mobile->detail:'' }}</td>
            <td>{{ $lead->lead_status }}</td>
            <td>{{ $lead->created_at->diffForHumans() }}</td>
            </tr>
    @endforeach
    </tbody>
</table>
</div>