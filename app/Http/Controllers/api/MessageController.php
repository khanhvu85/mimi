<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Message;
use App\Conversation;
use Validator;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Add a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'type' => 'required|in:"text", "image","audio","video", "emoij"',
            'sender_id' => 'required',
            'reciever_id' => 'required',
            'message' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking sender existed
        $sender = User::find($request->input('sender_id'));
        if (!$sender) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking reciever existed
        $reciever = User::find($request->input('reciever_id'));
        if (!$reciever) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking the reciever block list
        if(count($reciever->blockeds) > 0){   //get list of blocked user
            foreach ($reciever->blockeds as $blocked) {
                //check the id 
                if($blocked->partner_id == $sender->id){
                    //if the reciever blocked user, deni chat request
                    // return response()->json(["message" => "He/She blocked you!"], 223);
                    return response()->json(["message" => "用户块你。"], 223);
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

		//response to client
		return response()->json($message, 200);
    }

    /**
     * Remove a message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'message_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking message existed
        $message = Message::find($request->input('message_id'));
        if (!$message) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //get the conversation
        $conversation = $message->conversation;

        //check the existing
        if($conversation){
            //check the last message id of the conversation
            if($conversation->last_message_id == $message->id){
                //if the message is the last message of the conversation
                //searching DB to update the last message id.
                //get the last message of conversation
                $last_message = Message::where('conversation_id','=',$conversation->id)
                                        ->where('id','<>',$message->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                //if there is another message
                if($last_message){
                    //update last_message_id for the converastion
                    $conversation->update(['last_message_id'=> $last_message->id]);
                }
                else{
                    //if there is no other message, delete the conversation
                    $conversation->delete();
                }
            }
        }

		//delete from database
		$message->delete();

		//response to client
		return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Clear all chatting history of an user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearAllChattingHistories(Request $request)
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

        //clear all messages
        //get messages list
        $messages = array_collapse([$user->sendMessages, $user->recievedMessages]);

        if(count($messages) > 0){
            foreach ($messages as $message) {
                if($message->deleter_id == 0){  //if on one had deleted the message
                    $message->update(array('deleter_id' => $user->id));
                }
                //if the partner had deleted the message, deleted it
                elseif ($message->deleter_id != $user->id) {    
                    $message->delete();
                }
            }
        }

        //clear all conversations
        //get conversations list
        $conversations = array_collapse([$user->sendConversations, $user->recievedConversations]);

        if(count($conversations) > 0){
            foreach ($conversations as $conversation) {
                if($conversation->deleter_id == 0){ //if no one deleted the conversation
                    //update deleter_id for conversation
                    $conversation->update(array('deleter_id'=> $user->id));
                }
                //if the partner had removed the conversation
                elseif ($conversation->deleter_id != $user->id && $conversation->deleter_id != -1) {
                    //update deleter_id for conversation
                    $conversation->update(array('deleter_id'=> -1));
                }
            }
        }

        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Marking that a message is read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'message_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking message existed
        $message = Message::find($request->input('message_id'));
        if (!$message) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //update from database
        $message->update(array('is_read' => true));

        //response to client
        return response()->json(["message" => "成功了。"], 200);
    }


    /**
     * Marking that a group of message is read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function readGroup(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'list_message_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //get the id list
        $lmes_ids = json_decode($request->input('list_message_id'), true);

        //get messages list and update to database
        if(count($lmes_ids) > 0){
            Message::whereIn('id', $lmes_ids)->update(array('is_read' => true));
        }

        //response to client
        return response()->json(["message" => "成功了。"], 200);
    }

}
