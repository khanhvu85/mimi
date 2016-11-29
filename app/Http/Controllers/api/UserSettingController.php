<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\UserSetting;
use Auth;
use Validator;

class UserSettingController extends Controller
{
    /**
     * Function to setting Push Notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function settingNotify(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
            'vibrate' => 'required',
            'is_notify' => 'required',
            'sound' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //get data from request
        $data = $request->only('user_id', 'is_notify','vibrate', 'sound');

        //checking had setting yet
        $setting = UserSetting::where('user_id','=',$request->input('user_id'))->first();
        if($setting)	//if yes, update it
        {
            $setting->update($data);
        }
        else{	//if no, create it
        	$setting = UserSetting::create($data);
        }

        //response
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Function to get the setting of Push Notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSettingNotify(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //searching from database
        $setting = UserSetting::where('user_id','=',$request->input('user_id'))->first();
        if(!$setting){

            // return response()->json(["message" => "Settings does not exist!"], 222);
            return response()->json(["message" => "成功了。"], 222);
        }

        //response
        return response()->json($setting, 200);
    }
}
