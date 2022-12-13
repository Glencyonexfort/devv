<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Exception;
use SebastianBergmann\Environment\Console;

class JobsMovingLegs extends Model {

    protected $table = 'jobs_moving_legs';
    protected $primaryKey  = 'id';    
    protected $dates = ['leg_date'];
    protected $fillable = [
        'tenant_id','job_id', 'leg_number', 'job_type','leg_status','pickup_address','drop_off_address',
        // 'pickup_geo_location','drop_off_geo_location',/
        'est_start_time','est_finish_time','driver_id','vehicle_id','leg_date'
    ];
    use Notifiable;

    public function calculateTimeDuration()
    {
        if($this->actual_start_time!=NULL && $this->actual_finish_time!=NULL){
            $start = Carbon::parse($this->actual_start_time);
            
            $end = Carbon::parse($this->actual_finish_time);
            $minutes = $end->diffInMinutes($start) / 60;
            $total_hours = ceil($minutes * 4) / 4;
            $total_hours = number_format((float)$total_hours, 2, '.', '');            
        }else{
            $total_hours = 0;
        }
        echo $start;
        return $total_hours;
    }

    public function calculateTimeDurations($actual_start_time, $actual_finish_time)
    {
        if($actual_start_time!=NULL && $actual_finish_time!=NULL)
        {
            $start = Carbon::parse($this->actual_start_time);
            $end = Carbon::parse($this->actual_finish_time);
            $minutes = $end->diffInMinutes($start) / 60;
            $total_hours = ceil($minutes * 4) / 4;
            $total_hours = number_format((float)$total_hours, 2, '.', '');            
        }else{
            $total_hours = 0;
        }
        return $total_hours;
    }

    public function calculateTimeDurationForUpdate()
    {
        if($this->actual_start_time!=NULL && $this->actual_finish_time!=NULL){
            $start = Carbon::parse($this->actual_start_time);
            $end = Carbon::parse($this->actual_finish_time);
            $minutes = $end->diffInMinutes($start) / 60;
            $total_hours = ceil($minutes * 4) / 4;
            $total_hours = number_format((float)$total_hours, 2, '.', '');
            $price_additional = DB::table('jobs_moving_pricing_additional as t1')
                    //->select('t1.tenant_id')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                    ->first();
            if($price_additional){
                if($total_hours<$price_additional->hourly_pricing_min_hours){
                    $total_hours = $price_additional->hourly_pricing_min_hours;
                }
            }
        }else{
            $total_hours = 0;
        }
        return $total_hours;
    }    

    public static function getGeoLocation($api_key, $address){
        try{
            $geo_location=NULL;
            $address = str_replace(' ', '+', $address);
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . $api_key;
            $geocode = self::curl_get_file_contents($url);
    
            $output = json_decode($geocode);
            if($output->status=='OK'){
            //print_r($output);exit;
                $latitude = substr(($output->results[0]->geometry->location->lat), 0, 15);
                $longitude = substr(($output->results[0]->geometry->location->lng), 0, 15);
                $geo_location = $latitude.','.$longitude;
            }
            return $geo_location;
        }catch (Exception $ex) {
            return NULL;
        }
    }

    public static function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) {
            return $contents;
        } else {
            return false;
        }

    }
}
