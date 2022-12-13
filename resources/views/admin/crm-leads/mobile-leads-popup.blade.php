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
        @foreach($crmContactDetails as $detail)
        <?php
        // $mobile = ($detail->detail_type == 'Mobile') ? $detail->detail_type : '';
        $contact = Illuminate\Support\Facades\DB::table('crm_contacts')->where('id', $detail->contact_id)->first();
        if($contact){
        $lead_detail = Illuminate\Support\Facades\DB::table('crm_leads')->where('id', $contact->lead_id)->first();
        // $email = ($detail->detail_type == 'Email') ? $detail->detail_type : '';
        // $email = Illuminate\Support\Facades\DB::table('crm_contacts')
        //     ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
        //     ->select('crm_contact_details.detail')
        //     ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $lead->id, 'crm_contact_details.detail_type' => 'Email'])
        //     ->first(); 
        $email = Illuminate\Support\Facades\DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contact_details.detail')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $lead_detail->id, 'crm_contact_details.detail_type' => 'Email'])
            ->first(); 
        }else{
            continue;
        }
        ?>
        <tr>
            <td><a class="font-weight-bold txt-blue" href="{{ route("admin.crm-leads.view", $lead_detail->id) }}">{{ $lead_detail->name }}</a></td>
            <td style="overflow-wrap: break-word;">{{ ($email)? $email->detail:'' }}</td>
            <td>{{ ($detail->detail_type == 'Mobile')? $detail->detail:'' }}</td>
            <td>{{ $lead_detail->lead_status }}</td>
            <td>{{ $lead_detail->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>