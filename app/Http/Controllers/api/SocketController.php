<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Device;
use App\Favourite;
use App\Conversation;
use App\Message;
use Validator;
use Carbon\Carbon;

class SocketController extends Controller
{
    /**
     * Handler when Socket Server start.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function startServer(Request $request)
    {
        User::where('socket_id', '<>', null)->update(array('socket_id' => null));

        //response to client
        return response()->json(["code" => 200,
                                "message" => "Success!"]);
    }

    /**
     * Get list of id of partner that listen for user.
     *
     * @param  User  $user
     * @return array
     */
    public function getListenerIds($user)
    {
        //get partner whom user favourite
        $lOnlFansId = array();

        //searching from database
        $lFans = Favourite::where('partner_id', '=', $user->id)->get();
        if(!$lFans->isEmpty()){
            foreach ($lFans as $fan) {
                //check user existed
                if($fan->user){
                    //check user online
                    if($fan->user->socket_id != null && $fan->user->socket_id != ''){
                        //add user to the list
                        array_push($lOnlFansId, $fan->user->id);
                    }
                }
            }
        }

        //get partner who had chat with user
        $lOnlChatedsId = array();

        //searching for conversations
        $lConversations = Conversation::where('user1_id','=',$user->id)
                                        ->orWhere('user2_id','=',$user->id)->get();
        if (!$lConversations->isEmpty()) 
        {
            foreach ($lConversations as $conversation) {
                //check to add partner id
                if($conversation->user1_id == $user->id){
                    array_push( $lOnlChatedsId, $conversation->user2_id);
                }
                else{
                    array_push( $lOnlChatedsId, $conversation->user1_id);
                }
            }
        }

        //get matching partners
        $lOnlMatchingsId = array();

        //call action matching from UserController
        $lOnlMatchingPartners =  UserController::matching($user, true, 1);

        if(count($lOnlMatchingPartners) > 0){
            $lOnlMatchingsId = array_pluck($lOnlMatchingPartners, 'id');
        }

        //merging those ids
        $listenIds = array_values(array_unique(array_collapse([$lOnlFansId, $lOnlChatedsId, $lOnlMatchingsId])));

        //return value
        return $listenIds;
    }

    /**
     * Get user information when join socket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request)
    {
    	// validate the info, create rules for the request
    	$rules = array(
    	    'user_id' => 'required',
    	    'socket_id' => 'required'
    	);

    	// run the validation rules on the request data
    	$validator = Validator::make($request->all(), $rules);

    	// if the validator fails, response to client
    	if ($validator->fails()) {
    	    return response()->json(["code" => 221,
    	    						 "message" => "出事了，请重试。"]);
    	}

    	//checking user existed
    	$user = User::find($request->input('user_id'));
    	if (!$user) 
		{
			return response()->json(["code" => 221,
    	    						 "message" => "出事了，请重试。"]);
		}

        //searching for device token
        $device = Device::where('user_id','=',$user->id)->first();

        //checking device token
        if($request->has('device_token')){
            //declare temporary variable
            $err_token = 0;

            //checking device token
            if(!$device){   //user has not login
                $err_token = 1;
            }
            else{
                if($device->device_token != $request->input('device_token')){
                    //user login on other device
                    $err_token = 1;
                }
            }

            //checking temporary variable
            if($err_token == 1){
                return response()->json(["code" => 224,
                                     "message" => "You are logged on another device. Please login again!"]);
            }
        }
        else{
            if($device){    //if user logged in other device
                return response()->json(["code" => 224,
                                     "message" => "You are logged on another device. Please login again!"]);
            }
        }

		//update socketID for user
        // dd($request->input('socket_id'));
		$user->update(array('socket_id' => $request->input('socket_id')));

		//get list of favourite who is online
		$lOnlFavours = array();

		if(count($user->favourites) > 0){	//get list of favourites
			foreach ($user->favourites as $favourite) {
				//check favourite existed
				if($favourite->partner){
					//check favourite online
					if($favourite->partner->socket_id != null && $favourite->partner->socket_id !=''){
						//add partner to the list
						array_push($lOnlFavours, array('user_id' => $favourite->partner->id,
													'socket_id' => $favourite->partner->socket_id));
					}
				}
			}
		}

		//get list of id of listeners
        $listenIds = $this->getListenerIds($user);

		//declare result
		$result = array('user_id' => $request->input('user_id'),
						'socket_id' => $request->input('socket_id'),
						'onl_favourites' => $lOnlFavours,
						'listenIds' => $listenIds);

		//response to client
		return response()->json(["code" => 200,
	    						"message" => "Success!",
	    						'data' => $result]);
    }

    /**
     * Get user information when disconnect from socket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leave(Request $request)
    {
    	// validate the info, create rules for the request
    	$rules = array(
    	    'user_id' => 'required'
    	);

    	// run the validation rules on the request data
    	$validator = Validator::make($request->all(), $rules);

    	// if the validator fails, response to client
    	if ($validator->fails()) {
    	    return response()->json(["code" => 221,
    	    						 "message" => "Invalid params ".$validator->errors()->first()]);
    	}

    	//checking user existed
    	$user = User::find($request->input('user_id'));
    	if (!$user) 
		{
			return response()->json(["code" => 221,
    	    						 "message" => "User does not exist!"]);
		}

		//update socketID for user
		$user->update(array('socket_id' => null));
		
        //get list of id of listeners
        $listenIds = $this->getListenerIds($user);

		//declare result
		$result = array('user_id' => $request->input('user_id'),
						'socket_id' => $request->input('socket_id'),
						'listenIds' => $listenIds);

		//response to client
		return response()->json(["code" => 200,
	    						"message" => "Success!",
	    						'data' => $result]);
    }

    /**
     * Adding a chat mesage, call from socket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function chatMessage(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'type' => 'required|in:"text", "image","audio","video","emoij"',
            'sender_id' => 'required',
            'reciever_id' => 'required',
            'message' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["code" => 221,
    	    						 "message" => "Invalid params ".$validator->errors()->first()]);
        }

        //checking sender existed
        $sender = User::find($request->input('sender_id'));
        if (!$sender) 
        {
            return response()->json(["code" => 221,
    	    						 "message" => "Sender does not exist!"]);
        }

        //checking reciever existed
        $reciever = User::find($request->input('reciever_id'));
        if (!$reciever) 
        {
			return response()->json(["code" => 221,
    	    						 "message" => "Reciever does not exist!"]);
        }

        //checking the reciever block list
        if(count($reciever->blockeds) > 0){   //get list of blocked user
            foreach ($reciever->blockeds as $blocked) {
                //check the id 
                if($blocked->partner_id == $sender->id){
                    //if the reciever blocked user, deni chat request
        			return response()->json(["code" => 223,
            	    						 "message" => "He/She blocked you!"]);
                }
            }
        }

        //checking conversation messages
		$conversation = Conversation::where([
                                            ['user1_id','=',$request->input('sender_id')],
                                            ['user2_id','=',$request->input('reciever_id')],
                                            ])
                                    ->orWhere([
                                            ['user1_id','=',$request->input('reciever_id')],
                                            ['user2_id','=',$request->input('sender_id')],
                                            ])->first();

        if(!$conversation){ //if their conversation is not existed, create it
            $conversation = Conversation::create(['user1_id'=>$request->input('sender_id'),
                                                'user2_id'=>$request->input('reciever_id')]);
        }

		//get info from request
		$data = $request->only('type', 'sender_id', 'reciever_id', 'message');

		//set other info
		$data['conversation_id'] = $conversation->id;
		$data['sending_time'] = Carbon::now()->toDateTimeString();
		$data['is_read'] = false; //default that reciver has not read the message
		$data['deleter_id'] = 0;  //default that no one deleted the message

		//save to datebase
		$message = Message::create($data);

		//update conversation
        $dtConver = array('last_message_id' => $message->id);

        if($conversation->deleter_id == $sender->id){ //if only user had deleted the conversation
            $dtConver['deleter_id'] = 0;
        }
        elseif ($conversation->deleter_id == -1) {  //if user + partner had deleted the conversation
            //check to find partner id
            if($conversation->user1_id == $sender->id){
                $dtConver['deleter_id'] = $conversation->user2_id;
            }
            else{
                $dtConver['deleter_id'] = $conversation->user1_id;
            }
        }

        //update conversation to database
		$conversation->update($dtConver);

        //check to push notification
        if($request->input('is_push') != 0){
            //declare temporary variable
            $is_push = 1;

            //checking the reciever unnotify list
            if(count($reciever->unNotifies) > 0){   //get list of unnotify partner
                foreach ($reciever->unNotifies as $unNotify) {
                    //check the id 
                    if($unNotify->partner_id == $sender->id){
                        //if the reciever unnotify from sender
                        $is_push = 0;
                        break;
                    }
                }
            }

            //check is_push
            if($is_push){
                //get data to push
                $data_push = array('push_type' => 1,    //chat
                                    'data' => array('message_id' => $message->id,
                                                    'type' => $message->type,
                                                    'message' => $message->message,
                                                    'sending_time' => $message->sending_time,
                                                    'conversation_id' => $conversation->id,
                                                    'sender' => array('id' => $sender->id,
                                                                    'name' => $sender->name,
                                                                    'avatar' => $sender->avatar)
                                                    )
                                    );
                //dump to check response (only for dev + test)
                $is_dump = 0;
                if($request->input('is_dump') == 1){ $is_dump = 1; }

                //get the IOS badge
                $data_badge = Message::where('conversation_id','=',$conversation->id)
                                ->where('reciever_id','=',$reciever->id)
                                ->where('is_read','=',false)
                                ->count();

                //call function push
                PushNotificationController::pushToUser($sender, $reciever, $data_push, $data_badge, $is_dump);
            }
        }

		//response to client
        return response()->json(["code" => 200,
        						"message" => "Success!",
                                "data" => $message]);
    }

    /**
     * Reading a chat message, call from socket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function readMessage(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'message_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["code" => 221,
            						"message" => "Invalid params: ".$validator->errors()->first()]);
        }

        //checking message existed
        $message = Message::find($request->input('message_id'));
        if (!$message) 
        {
        	return response()->json(["code" => 221,
        							"message" => "Message does not exist!"]);
        }

        //update from database
        $message->update(array('is_read' => true));

        //get data to response
        $data_mes = array('message_id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'reciever_id' => $message->reciever_id);

        //response to client
        return response()->json(["code" => 200,
        						"message" => "Success!",
                                "data" => $data_mes]);
    }

    /**
     * Reading all chat messages of a conversation, call from socket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function readConversation(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
            'partner_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["code" => 221,
                                    "message" => "Invalid params: ".$validator->errors()->first()]);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["code" => 221,
                                    "message" => "User does not exist!"]);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            return response()->json(["code" => 221,
                                    "message" => "Partner does not exist!"]);
        }

        //checking conversation 
        $conversation = Conversation::where([
                                            ['user1_id','=',$user->id],
                                            ['user2_id','=',$partner->id],
                                            ])
                                        ->orWhere([
                                            ['user1_id','=',$partner->id],
                                            ['user2_id','=',$user->id],
                                            ])->first();
        if (!$conversation) 
        {
            //if there is no message send before
            return response()->json(["code" => 221,
                                    "message" => "Conversation does not exist!"]);
        }

        //searching to update messages in conversation
        Message::where('conversation_id','=',$conversation->id)
                ->where('reciever_id','=',$user->id)
                ->where('is_read','=',false)
                ->update(array('is_read' => true));

        //get data to response
        $data_conver = array('user_id' => $user->id,
                            'partner_id' => $partner->id);

        //response to client
        return response()->json(["code" => 200,
                                "message" => "Success!",
                                "data" => $data_conver]);
    }
}
