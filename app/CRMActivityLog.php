<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMActivityLog extends Model
{
    protected $table = 'crm_activity_log';

    public $timestamps = false;

    static $log_types = [4,5,9,11,14];

    protected $fillable = [
        'tenant_id', 'lead_id', 'user_id', 'job_id','log_from','log_to','log_subject','log_cc','log_bcc', 'log_type','log_message','log_date','external_message_id'
    ];

    public static  function log_count() {
        $logs = \DB::table('crm_activity_log')
            ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
            ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
            ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id, 'crm_activity_log.log_status'=>'unread', 'crm_opportunities.deleted'=>0])
            ->whereIn('crm_activity_log.log_type', self::$log_types)
            ->where('crm_activity_log.lead_id', '!=', 0)
            ->count();
                                        
        $tasks = \DB::table('crm_tasks')->where(['tenant_id'=>auth()->user()->tenant_id, 'status'=>'Active'])->count();
        return ($logs+$tasks);
    }

    public static  function opportunity_count() {
        return \DB::table('crm_opportunities')->where(['crm_opportunities.tenant_id'=>auth()->user()->tenant_id, 'crm_opportunities.deleted'=>0 ,'crm_opportunities.op_status'=>'New'])
        ->leftjoin('jobs_moving', function ($join) {
            $join->on('crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                ->where('crm_opportunities.op_type', '=', 'Moving');
        })
        ->leftjoin('jobs_cleaning', function ($join) {
            $join->on('crm_opportunities.id', '=', 'jobs_cleaning.crm_opportunity_id')
                ->where('crm_opportunities.op_type', '=', 'Cleaning');
        })
        ->where(function ($query) {
            $query->orWhere('jobs_moving.opportunity', '=', 'Y')
                  ->orWhere('jobs_cleaning.opportunity', '=', 'Y');
        })->count();
    }
    public static  function job_moving_count() {
        return \DB::table('jobs_moving')->where(['tenant_id'=>auth()->user()->tenant_id, 'opportunity'=>'N','job_status'=>'New', 'deleted'=>0])->count();
    }

    public static function beautifyEmailContent($html){
        // remove href attribute
        $html = preg_replace("/<\/?a( [^>]*)?>/i", "", $html);
        // remove base tag
        $html = preg_replace("/<\/?base( [^>]*)?>/i", "", $html);
        // remove style tag body
        $html = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $html);
        $inputHTML = htmlspecialchars($html);
        $doc = new \DOMDocument;
        $doc->encoding = 'utf-8';
        $doc->loadHTML( utf8_decode( $inputHTML ) );
        libxml_use_internal_errors(true);
        $xpath = new \DOMXPath($doc);
        $body = $xpath->query('/html/body');
        $Html = ($doc->saveXml($body->item(0)));
        // $Html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        // $Html .= $doc->saveHTML( $doc->documentElement ); // important!
        
        return html_entity_decode($Html);
    }
}