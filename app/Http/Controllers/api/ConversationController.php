<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Message;
use App\Unnotify;
use App\Block;
use App\Conversation;
use Validator;
use Carbon\Carbon;

class ConversationController extends Controller
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
     * Get all message of a conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getConversation(Request $request)
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
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::select(['id', 'name', 'avatar'])->find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
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
            return response()->json(array(), 200);
        }

        //pagination
        //delacre pagination varaible
        $limit = 20;
        $offset = 0;
        $page = 1;

        //check pagination param
        if($request->input('page')){
            //set page
            $page = $request->input('page');

            //set offset
            $offset = $limit * ($page-1);
        }

        //check pagination by last page`s user id
        if($request->input('last_id')){
            //searching from database
            $messages = Message::where('conversation_id','=',$conversation->id)
                                ->where('deleter_id','<>',$user->id)
                                ->where('id','<',$request->input('last_id'))
                                ->orderBy('id', 'desc')
                                ->limit($limit)
                                ->offset($offset)
                                ->get();
        }
        else{        
            //searching from database
            $messages = Message::where('conversation_id','=',$conversation->id)
                                ->where('deleter_id','<>',$user->id)
                                ->orderBy('id', 'desc')
                                ->limit($limit)
                                ->offset($offset)
                                ->get();
        }

        //check the list
        if(!$messages->isEmpty()){
            foreach ($messages as $message) {
                //add milisecond folow request from IOS dev
                $message['mili_sending_time'] = Carbon::parse($message->sending_time)->format('Y-m-d H:i:s.u');
            }
        }

        //declare result
        $result = array('partner' => $partner,
                        'messages' => $messages,
                        'page' => $page);

        //response to client
        return response()->json($result, 200);
    }

    /**
     * Get list of conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getConversationList(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //declare result
        $result = array();

        //delacre pagination varaible
        $limit = 20;
        $offset = 0;

        //check pagination param
        if($request->input('page')){
            $offset = $limit * ($request->input('page')-1);
        }

        //searching from database
        $conversations = Conversation::where('user1_id','=',$request->input('user_id'))
                                    ->orWhere('user2_id','=',$request->input('user_id'))
                                    ->where('deleter_id','>=',0)
                                    ->where('deleter_id','<>',$user->id)
                                    ->orderBy('updated_at', 'desc')
                                    ->limit($limit)
                                    ->offset($offset)
                                    ->get();
        if($conversations->count() == 0){    //if there is no conversation
            return response()->json($result, 200);
        }
        else {
            foreach ($conversations as $conversation) {
                $tconver = array();

                //check partner
                if($conversation->user1_id == $user->id){
                    //searching partner
                    $partner = User::select(['id', 'name', 'avatar'])->find($conversation->user2_id);

                    $deleted_time = $conversation->user1_deleted_time;
                }else {
                    $partner = User::select(['id', 'name', 'avatar'])->find($conversation->user1_id);

                    $deleted_time = $conversation->user2_deleted_time;
                }

                if($partner){   //if partner is exiested in database
                    //checking blocked
                    $blocked = Block::where([
                                                ['user_id','=',$user->id],
                                                ['partner_id','=',$partner->id]
                                            ])->first();
                    if(!$blocked){  //if partner is not exist in the blocked list
                        $is_add = 1;
                        //checking deleted time
                        if($deleted_time !== '' && $deleted_time !== null && $deleted_time == $conversation->sent_time){
                            $is_add = 0;
                        }

                        if($is_add ==1){
                            $tconver['id'] = $conversation->id;

                            //get partner info
                            $tconver['partner_id'] = $partner->id;
                            $tconver['partner_name'] = $partner->name;
                            $tconver['partner_avatar'] = $partner->avatar;

                            //get last message
                            $tconver['lastest_message'] = $conversation->lastest_message;
                            $tconver['sent_time'] = $conversation->sent_time;
                            // $tconver['deleted_time'] = $deleted_time;

                            array_push($result, $tconver);
                        }
                    }
                }
            }
        }

        //response to client
        return response()->json($result, 200);
    }

    /**
     * Get recent conversations (in 7 days).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRecentConversations(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["message" => "User does not exist!"], 222);
        }

        //declare result
        $result = array();

        //searching from database
        $conversations = Conversation::where('user1_id','=',$request->input('user_id'))
                                    ->orWhere('user2_id','=',$request->input('user_id'))
                                    ->where('deleter_id','>=',0)
                                    ->where('deleter_id','<>',$user->id)
                                    ->where('updated_at','>',Carbon::now()->subDays(7))
                                    ->orderBy('updated_at', 'desc')
                                    ->get();
        if($conversations->count() == 0){    //if there is no conversation
            return response()->json($result, 200);
        }
        else {
            foreach ($conversations as $conversation) {
                $tconver = array();

                //check partner
                if($conversation->user1_id == $user->id){
                    $partner = User::select(['id', 'name', 'avatar'])->find($conversation->user2_id);
                }else {
                    $partner = User::select(['id', 'name', 'avatar'])->find($conversation->user1_id);
                }

                if($partner){   //if partner is exiested in database
                    $tconver['id'] = $conversation->id;

                    //get partner info
                    $tconver['partner'] = $partner;

                    //get the last message to/from partner
                    $tmes = null;
                    $message = Message::find($conversation->last_message_id);

                    if($message){   //if the message is existed
                        if($message->deleter_id != $user->id){  //if user had not deleted the message yet
                            $tmes = $message;
                        }
                    }

                    //add the message
                    $tconver['last_message'] = $tmes;

                    array_push($result, $tconver);
                }
            }
        }

        //response to client
        return response()->json($result, 200);
    }


    /**
     * Remove a conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
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
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::select(['id', 'name', 'avatar'])->find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
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
            // return response()->json(array("message" => "Conversation does not exist!"), 222);
            return response()->json(array("message" => "出事了，请重试。"), 222);
        }

        //check conversation deleted
        if($conversation->deleter_id == 0){ //if no one deleted the conversation
            //update deleter_id for conversation
            $conversation->update(array('deleter_id'=> $user->id));
        }
        elseif ($conversation->deleter_id == $user->id) {   //if user had removed the conversation
            // return response()->json(["message" => "You had removed this conversation!"], 222);
            return response()->json(["message" => "您已经移除了这个谈话。"], 222);
        }
        elseif ($conversation->deleter_id == -1) {  //if user + partner had removed the conversation
            // return response()->json(["message" => "You had removed this conversation!"], 222);
            return response()->json(["message" => "您已经移除了这个谈话。"], 222);
        }
        else{   //the partner had removed the conversation
            //update deleter_id for conversation
            $conversation->update(array('deleter_id'=> -1));
        }

        //response to client
        // return response()->json(["message" => "Remove conversation successful!"], 200);
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Clear all messages in a conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearAllMessages(Request $request)
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
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
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
            // return response()->json(array("message" => "Conversation does not exist!"), 200);
            return response()->json(array("message" => "出事了，请重试。"), 200);
        }

        //searching messages in conversation
        if(count($conversation->messages) > 0){
            foreach ($conversation->messages as $message) {
                if($message->deleter_id == 0){  //if on one had deleted the message
                    $message->update(array('deleter_id'=> $user->id));
                }
                //if the partner had deleted the message, deleted it
                elseif ($message->deleter_id != $user->id) {
                    $message->delete();
                }
            }
        }

        // return response()->json(["message" => "Clear successful!"], 200);
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Read all messages in a conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function readAllMessages(Request $request)
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
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
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
            // return response()->json(array("message" => "Conversation does not exist!"), 200);
            return response()->json(array("message" => "出事了，请重试。"), 200);
        }

        //searching to update messages in conversation
        Message::where('conversation_id','=',$conversation->id)
                ->where('reciever_id','=',$user->id)
                ->where('is_read','=',false)
                ->update(array('is_read' => true));

        // return response()->json(array("message" => "Read successful!"), 200);
        return response()->json(array("message" => "成功了。"), 200);
    }

    /**
     * Get the settings of a conversation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSettings(Request $request)
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
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //declare unNotify and blocked
        $unNotify = false;
        $blocked = false;

        //checking had refused yet
        $unNo = Unnotify::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if($unNo)   //if user had refused notification from the partner
        {
            $unNotify = true;
        }

        //checking have blocked yet
        $block = Block::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if ($block) 
        {
            $blocked = true;
        }

        //declare result
        $result = array('is_unnotify' => $unNotify,
                        'is_blocked' => $blocked);
        
        //response
        return response()->json($result, 200);
    }

    /**
     * UpdateConversation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateConversation(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
            'partner_id' => 'required',
            'sent_time' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::select(['id', 'name', 'avatar'])->find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //get data from request
        $lmessage = $request->has('lastest_message') ? $request->input('lastest_message') : '';
        $sent_time = $request->input('sent_time');

        //checking conversation messages
        $conversation = Conversation::where([
                                            ['user1_id','=',$user->id],
                                            ['user2_id','=',$partner->id],
                                            ])
                                    ->orWhere([
                                            ['user1_id','=',$partner->id],
                                            ['user2_id','=',$user->id],
                                            ])->first();

        if(!$conversation){ //if their conversation is not existed, create it
            $conversation = Conversation::create(['user1_id'=>$user->id,
                                                'user2_id'=>$partner->id,
                                                'lastest_message'=>$lmessage,
                                                'sent_time'=>$sent_time,
                                                ]);
        }
        else{
            $conversation->update(array('lastest_message' => $lmessage,
                                        'sent_time' => $sent_time));
        }
        return response()->json(["status" => true], 200);
    }

    /**
     * UpdateDeleteMessageSentTime
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDeleteMessageSentTime(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
            'partner_id' => 'required',
            'sent_time' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking conversation messages
        $conversation = Conversation::where([
                                            ['user1_id','=',$user->id],
                                            ['user2_id','=',$partner->id],
                                            ])
                                    ->orWhere([
                                            ['user1_id','=',$partner->id],
                                            ['user2_id','=',$user->id],
                                            ])->first();

        if(!$conversation){ //if their conversation is not existed
            return response()->json(["message" => "出事了，请重试。"], 223);
        }

        //check partner
        if($conversation->user1_id == $user->id){
            $conversation->update(array('user1_deleted_time' => $request->input('sent_time')));
        }else {
            $conversation->update(array('user2_deleted_time' => $request->input('sent_time')));
        }
        return response()->json(["status" => true], 200);
    }

    /**
     * GetDeleteMessageSentTime
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDeleteMessageSentTime(Request $request)
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
            // return response()->json(["message" => "Invalid params: ".$validator->errors()->first()], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            // return response()->json(["message" => "User does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            // return response()->json(["message" => "Partner does not exist!"], 222);
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking conversation messages
        $conversation = Conversation::where([
                                            ['user1_id','=',$user->id],
                                            ['user2_id','=',$partner->id],
                                            ])
                                    ->orWhere([
                                            ['user1_id','=',$partner->id],
                                            ['user2_id','=',$user->id],
                                            ])->first();

        if(!$conversation){ //if their conversation is not existed
            return response()->json(["message" => "出事了，请重试。"], 223);
        }

        //check partner
        if($conversation->user1_id == $user->id){
            $sent_time = $conversation->user1_deleted_time;
        }else {
            $sent_time = $conversation->user2_deleted_time;
        }
        return response()->json(["sent_time" => $sent_time], 200);
    }
}
