<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\PplPeople;
use App\User;
use Illuminate\Support\Str;

class LoginController extends BaseController
{
    /**
     * Return User Record against credentials.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try{
            $sys_job_type = 'Moving';
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            if (auth()->attempt($credentials)) {
                $api_token = Str::random(80);
                $user = User::find(auth()->user()->id);
                $user->api_token = $api_token;
                $user->save();

                $people = PplPeople::where(['user_id'=>auth()->user()->id])->first();

                if($people){
                    if($people->sys_job_type==1){
                        $sys_job_type = 'Moving';
                    }elseif($people->sys_job_type==2){
                        $sys_job_type = 'Cleaning';
                    }
                    $user_id = $people->id;
                    $first_name = $people->first_name;
                    $last_name = $people->last_name;
                }

                $image = public_path().'/user-uploads/avatar/'.$user->image;
                $image_url = substr($image, strrpos($image, '/public') + 1);
                
                $data = array(
                    'user_id' => $user_id,
                    'register_id'=>auth()->user()->id,
                    'tenant_id' => auth()->user()->tenant_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => auth()->user()->email,
                    'api_token' => $api_token,
                    'image' => url('/'.$image_url),
                    'mobile' => $people->mobile,
                    'sys_job_type'=>$sys_job_type
                );
                return $this->sendResponse($data, 'Login successfully.');
            } else {
                return $this->sendError('UnAuthorised', 'Credentials not match.');
            }
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function registerDeviceToken(Request $request){
        try{
            if(empty($request->register_id) || $request->register_id <= 0){
                return $this->sendError('notValid', 'Register ID is not valid.');
            }
            if(empty($request->device_token)){
                return $this->sendError('notValid', 'Device Token is not valid.');
            }
            $register_id = $request->register_id;
            $device_token = $request->device_token;

            //update token
            $data = User::where('id',$register_id)
            ->update([
                'device_token' => $device_token
            ]);
            if(!$data){
                return $this->sendError('notFound', 'Register id not found.');
            }
            return $this->sendResponse($data, 'Device Token has been registered successfully');
            } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
            }
    }
}
