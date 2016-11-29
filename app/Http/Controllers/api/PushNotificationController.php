<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use PushNotification;

use App\User;
use App\Device;
use Carbon\Carbon;
use App\Message;
use App\Conversation;
use Validator;

class PushNotificationController extends Controller
{
    /**
     * Push notification to an user.
     * @param User $user
     * @param array $data_push
     */
    public static function pushToUser($sender, $user, $data_push, $data_badge = 1, $is_dump = null){
        //get the setting
        $setting = $user->userSetting;

        //declare temporary variable
        //is_notify
        $is_notify = 1;
        if($setting && $setting->is_notify == 0){
            $is_notify = 0;
        }

        //sound
        $sound = 1;
        if($setting && $setting->sound == 0){
            $sound = 0;
        }

        //vibrate
        $vibrate = 1;
        if($setting && $setting->vibrate == 0){
            $vibrate = 0;
        }

        //check is_notify
        if($is_notify){   //if user accept to reciver motificatioon
            //get device
            $device = $user->device;
            if($device){
                //check os
                switch ($device->os) {
                    case 'ios':
                        $mess = json_decode($data_push['data']['message']);

                        //check the type to custom the alert
                        switch ($data_push['data']['type']) {
                            case 'video':
                                $alert = $sender->name.' sent you a video clip.';
                                break;
                            
                            case 'audio':
                                $alert = $sender->name.' 给您发送了一语音片段。';
                                break;
                            
                            case 'image':
                                $alert = $sender->name.' 给您发送了一图片。';
                                break;
                            
                            case 'emoji':
                                $alert = $sender->name.' 给您发送了一表情。';
                                break;
                            
                            case 'text':
                                $alert = $mess->message;
                                break;
                            
                            default:
                                $alert = $mess->message;
                                break;
                        }

                        //create mesage
                        $message = PushNotification::Message($alert,array(
                            'badge' => $data_badge,
                            'sound' => $sound ? 'default' : '',
                            'data' => $data_push
                        ));

                        //send notification
                        try {
                            $rs = PushNotification::app('proIOS')
                                                    ->to($device->device_token)
                                                    ->send($message);

                            if($is_dump == 1){
                                dd($rs);
                            }
                        } catch (\Exception $e) {
                            // $e->getMessage();
                        }
                        break;

                    case 'android':
                        try {
                            $rs = PushNotification::app('devAndroid')
                                                    ->to($device->device_token)
                                                    ->send(json_encode($data_push));
                        } catch (\Exception $e) {
                            // $e->getMessage();
                        }
                        break;

                    default:
                        //do nothing
                        break;
                }
            }
        }
    }


    /**
     * Test Push notification.
     */
    public function pushTesting(Request $request)
    {
        $rules = array(
            'type' => 'required|in:"text", "image","audio","video","emoij"',
            'sender_id' => 'required',
            'reciever_id' => 'required',
            'message' => 'required',
            'os' => 'required',
            'device_token' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
        }

        //checking sender existed
        $sender = User::find($request->input('sender_id'));
        if (!$sender) 
        {
            return response()->json(["message" => "Sender does not exist!"], 222);
        }

        $conversation = Conversation::where([
                                            ['user1_id','=',$request->input('sender_id')],
                                            ['user2_id','=',$request->input('reciever_id')],
                                            ])
                                    ->orWhere([
                                            ['user1_id','=',$request->input('reciever_id')],
                                            ['user2_id','=',$request->input('sender_id')],
                                            ])->first();

        if(!$conversation){ //if their conversation is not existed, create it
            $conversation_id = 0;
        }
        else{
            $conversation_id = $conversation->id;    
        }

        //getting informations from request
        $data = $request->only('os', 'device_token', 'type', 'sender_id', 'reciever_id', 'message');

        $data_push = array('push_type' => 1,    //chat
                            'data' => array('message_id' => 123456,
                                            'type' => $data['type'],
                                            'message' => $data['message'],
                                            'sending_time' => Carbon::now()->toDateTimeString(),
                                            'conversation_id' => $conversation_id,
                                            'sender' => array('id' => $sender->id,
                                                            'name' => $sender->name,
                                                            'avatar' => $sender->avatar)
                                            )
                            );
    	
    	switch ($data['os']) {
    		case 'ios':
                if($request->has('is_pro')){
                    if($request->input('is_pro') == 1){
                        try {
                            $message = PushNotification::Message($data['message'],array(
                                'badge' => 1,
                                'sound' => 'default',
                                'data' => $data_push
                            ));
                            $rs = PushNotification::app('proIOS')
                                ->to($data['device_token'])
                                ->send($message);
                            dd($rs);
                        } catch (\Exception $e) {
                            dd($e->getMessage());
                        }

                        break;
                    }
                }

                try {
                    $message = PushNotification::Message($data['message'],array(
                        'badge' => 1,
                        'sound' => 'default',
                        'data' => $data_push
                    ));
                    $rs = PushNotification::app('devIOS')
                        ->to($data['device_token'])
                        ->send($message);
                    dd($rs);
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
    			
    			break;

    		case 'android':
    			$rs = PushNotification::app('devAndroid')
    			->to($data['device_token'])
    			->send($data_push);
    			dd($rs);
    			break;

    		default:
    			# code...
    			break;
    	}
    }
}
