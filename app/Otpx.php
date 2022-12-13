<?php
namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Otpx extends Model
{
    protected $table = 'otps';

    protected $fillable = [
        'identifier', 'token', 'validity','expired','no_times_generated','generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public static function generate($identifier)
    {
        Otpx::where('identifier', $identifier)
            ->delete();

        $pin = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp = Otpx::create([
            'identifier' => $identifier,
            'token' => $pin,
            'validity' => 5,
            'generated_at' => Carbon::now(),
        ]);

        return $pin;
    }

    public static function validate($identifier,$token)
    {
        $otp = Otpx::where(['identifier'=> $identifier,'token'=>$token])->first();
        if (! $otp) {
            $response = [
                'status' => false,
                'message' => 'Invalid OTP or expired, Please generate new OTP',
            ];
        }else{
            $response = [
                'status' => true
            ];
        }
        return $response;
    }

}
