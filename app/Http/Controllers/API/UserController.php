<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\PplPeople;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends BaseController
{
    /**
     * User profile Data.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserProfile(Request $request)
    {
        try{
            //Get requested parameters
            $user_id = $request->user_id;            
            //get records
            $people = PplPeople::where(['id'=>$user_id])->first();                          
            if(!$people){
                return $this->sendError('notFound', 'Records not found.');
            }
            $data = User::where('id', $people->user_id)
                    ->first(); 
            
            $image = request()->getHttpHost().'/public/user-uploads/avatar/'.$data->image;
            $response=[
                'image' => $image,
                'first_name' => $people->first_name,
                'last_name' => $people->last_name,
                'email' => $data->email,
                'mobile' => $people->mobile,
                'api_token' => $data->api_token,
            ];
            return $this->sendResponse($response, 'User Profile.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }
    
    /**
     * Update User profile Data.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUserProfile(Request $request)
    {
        try{
            if($request->user_id == '' || $request->user_id <= 0){
                return $this->sendError('notValid', 'User ID is not valid.');
            }
            $user_id = $request->user_id;
            $people = PplPeople::where(['id'=>$user_id])->first();                          
            if(!$people){
                return $this->sendError('notFound', 'Ppl records not found.');
            }
            $user = User::find($people->user_id);
            if(!$user){
                return $this->sendError('notFound', 'User record not found.');
            }
            if(isset($request->first_name) && $request->first_name != ''){
                $people->first_name = $request->first_name;
            }
            if(isset($request->last_name) && $request->last_name != ''){
                $people->last_name = $request->last_name;
            }
            if(isset($request->email) && $request->email != ''){
                $user->email = $request->email;
            }
            if(isset($request->mobile) && $request->mobile != ''){
                $people->mobile = $request->mobile;
                $user->mobile = $request->mobile;
            }
            if (isset($request->image) && $request->hasFile('image')) {
                File::delete('user-uploads/avatar/'.$user->image);

                $user->image = $request->image->hashName();
                $request->image->store('user-uploads/avatar');

                // resize the image to a width of 300 and constrain aspect ratio (auto height)
                $img = Image::make('user-uploads/avatar/'.$user->image);
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save();
            }

            $user->save();
            $people->save();
            return $this->sendResponse('Success', 'User profile updated.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }
}
