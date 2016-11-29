<?php

namespace App\Http\Controllers\api;

use App\UserDelete;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Favourite;
use App\Block;
use App\Questionary;
use App\VerifyID;
use App\Device;
use App\Conversation;
use App\Message;
use App\Unnotify;
use App\ViewInfo;
use Auth;
use Validator;
use Carbon\Carbon;

class UserController extends Controller
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
        //getting informations from request
        $data = $request->only('phone', 'password');

        //encrypt password
        $data['password'] = bcrypt($data['password']);

        //get a random token
        $data['remember_token'] = str_random(50);
        $data['last_used_time'] = Carbon::now()->toDateTimeString();

        //create user and save to database
        $user = User::create($data);

        return $user;
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
     * Sign up an user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'phone'    => 'required', 
            'password' => 'required' 
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // if the validator fails, response to client
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::where('phone','=',$request->input('phone'))->first();
        if ($user) 
        {
            // return response()->json(["message" => "Phone number is existed!"], 224);
            return response()->json(["message" => "电话号码被列出。"], 224);
        }

        //validation successful + phone number not existed, sign up user
        $user = $this->store($request);

        //response by json
        return response()->json($user, 200);
    }

    /**
     * Function to log in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'phone'    => 'required', 
            'password' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        } else {

            // Request data
            $data = $request->only('phone', 'password');

            // Check phone
            $user = User::where('phone', $data['phone'])->first();

            if (!$user) {
                // return response()->json(["message" => "Phone number does not exist!"], 222);
                return response()->json(["message" => "电话号码没有列出。"], 222);
            }

            // attempt to do the login, validation successful
            if (Auth::attempt($data)) {

                //check device token
                if ($request->has('device_token')) {

                    //get device token data
                    $data_device = $request->only('os', 'device_token');

                    //check devive token
                    $dvtoken = Device::where('device_token', $data_device['device_token'])->first();
                    if($dvtoken){
                        if($dvtoken->user_id != $user->id){
                            $dvtoken->delete();
                        }
                    }

                    //search device follow user
                    $device = $user->device;
                    if(!$device){    //if user never login before, create device

                        $deviceSaved = Device::create([
                            'user_id' => $user->id,
                            'os' => $data_device['os'],
                            'device_token' => $data_device['device_token']
                        ]);
                    }
                    else{   //else, update token
                        $status = $device->update($data_device);
                    }

                } else{
                    
                    //search for other device
                    $device = Device::where('user_id', $user->id)->first();

                    //if there is other device in the database, delete it
                    if($device){    
                        $device->delete();
                    }
                }

                //update last used time
                $user->update(array('last_used_time' => Carbon::now()->toDateTimeString()));

                //remove device info from json
                $result = array_except($user, ['device']);

                // response user infors
                return response()->json($user, 200);

            } else {

                // validation not successful, response to client
                // return response()->json(["message" => "Wrong password!"], 223);
                return response()->json(["message" => "密码错误。"], 223);
            }

        }
    }

     /**
     * Function to report error when verify chinese ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reportIDError(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'phone'    => 'required', 
            'image' => 'required',
            'description' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        } else {
            //get data from params
            $data = $request->only('phone', 'image', 'description');

            //save it to database           
            VerifyID::create($data);
            
            //response
            return response()->json(["message" => "成功了。"], 200);            
        }
    }

    /**
     * Function to update user`s infomations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateInfo(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::find($request->input('id'));
        if (!$user)
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //get data from params
        $data = $request->all();

        //checking Chinese ID
        if($request->input('chinese_id')){
            $chinese_user = User::where('chinese_id','=',$request->input('chinese_id'))->first();
            if ($chinese_user)
            {
                // return response()->json(["message" => "Chinese ID has already exist!"], 222);
                return response()->json(["message" => "身份证号码列出。"], 222);
            }
        }
        else{
            $data = array_except($data, ['chinese_id']);
        }

        //save it to database
        $user->update($data);
        
        //response
        return response()->json($user, 200);
    }

    /**
     * Function to view user`s informations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::find($request->input('id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //searching for liked partners
        $liked_times = Favourite::where('partner_id','=',$user->id)->count();

        //add to user infomations
        $user['liked_times'] = $liked_times;

        //check unread messages
        $unread_messages = Message::where('reciever_id','=',$user->id)
                                    ->where('is_read','=',false)
                                    ->count();

        $user['unread_messages'] = $unread_messages;
        
        //response
        return response()->json($user, 200);
    }

    /**
     * Function to view user`s informations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewInfo(Request $request)
    {
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required'
        );

        $data = $request->all();

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //set the target to get info
        $target = $user;

        $is_favourite = false;

        //checking partner existed 
        if($request->input('partner_id')) {

            $partner = User::find($request->input('partner_id'));

            if (!$partner) {
                return response()->json(["message" => "出事了，请重试。"], 222);
            }

            //set the target to get info
            $target = $partner;
            $target['matching_score'] = UserController::matchingScore($user, $partner);

            //check to update ViewInfo
            $viewInfo = ViewInfo::where([
                                    ['user_id','=',$request->input('user_id')],
                                    ['partner_id','=',$request->input('partner_id')],
                                ])->first();
            // If user had viewed the partner before, update viewing time
            if($viewInfo) {
                $viewInfo->update(array('viewing_time'=>Carbon::now()->toDateTimeString()));

            // Create view info history
            } else{   
                ViewInfo::create(array('user_id' => $user->id,
                                        'partner_id' => $partner->id,
                                        'viewing_time' => Carbon::now()->toDateTimeString()));
            }

            // User and partner is favourite 
            $favourite = Favourite::where('user_id', $data['user_id'])->where('partner_id', $data['partner_id'])->first();
            if (!empty($favourite)) $is_favourite = true;
        }

        //searching for liked partners
        $liked_times = Favourite::where('partner_id','=',$target->id)->count();

        //add to user infomations
        $target['liked_times'] = $liked_times;

        //check unread messages
        $unread_messages = Message::where('reciever_id','=',$target->id)
                                    ->where('is_read','=',false)
                                    ->count();

        $target['unread_messages'] = $unread_messages;

        //get settings of Push Notification
        if($target->userSetting){
            $setting_notify = array('vibrate' => $target->userSetting->vibrate,
                                    'sound' => $target->userSetting->sound,
                                    'is_notify' => $target->userSetting->is_notify);
        }
        else{
            $setting_notify = array('vibrate' => 1,
                                    'sound' => 1,
                                    'is_notify' => 1);
        }

        $target['is_favourite'] = $is_favourite;
        $target['setting_notify'] = $setting_notify;

        $target = array_except($target, ['answers', 'user_setting']);

        //response
        return response()->json($target, 200);
    }

    /**
     * Function to update chinese ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateChineseID(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'id' => 'required',
            'chinese_id' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::find($request->input('id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking Chinese ID
        $chinese_user = User::where('chinese_id','=',$request->input('chinese_id'))->first();
        if ($chinese_user) 
        {
            // return response()->json(["message" => "Chinese ID has already exist!"], 222);
            return response()->json(["message" => "身份证号码没有列出。"], 222);
        }

        //get data from params
        $data = $request->only('chinese_id');

        //save it to database           
        $user->update($data);
        
        //response
        return response()->json($user, 200);
    }

    /**
     * Function to update location: latitude + longitude.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLocation(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::find($request->input('id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //get data from params
        $data = $request->only('latitude', 'longitude');

        //save it to database           
        $user->update($data);
        
        //response
        return response()->json($user, 200);
    }

    /**
     * Function to calculate distance from lat+long.
     * follow: https://www.geodatasource.com/developers/php
     *
     * @param  User  $user1
     * @param  User  $user2
     * @return double (kilometers)
     */
    public static function distance($user1, $user2)
    {
        $theta = $user1->longitude - $user2->longitude;
        $dist = sin(deg2rad($user1->latitude)) * sin(deg2rad($user2->latitude)) + cos(deg2rad($user1->latitude)) * cos(deg2rad($user2->latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $kms = $dist * 60 * 1.1515 * 1.609344;

        return $kms;
    }

    /**
     * Function to calculate the matching score of 2 users.
     *
     * @param  User  $user1
     * @param  User  $user2
     * @return integer
     */
    public static function matchingScore($user1, $user2)
    {
        //check to hardcode default value
        if(count($user1->answers) == 0) {
            $user1->answers = array(
                                (object) array("question_id"=>"0", "mine_answer"=>"18", "partner_answer"=>"50"),
                                (object) array("question_id"=>"1", "mine_answer"=>"0", "partner_answer"=>"0"),
                                (object) array("question_id"=>"2", "mine_answer"=>"0", "partner_answer"=>"100"),
                                (object) array("question_id"=>"3", "mine_answer"=>"0", "partner_answer"=>"100"),
                                (object) array("question_id"=>"4", "mine_answer"=>"0", "partner_answer"=>"100"),
                                (object) array("question_id"=>"5", "mine_answer"=>"0", "partner_answer"=>"100")
                            );
        }
        
        if(count($user2->answers) == 0) {
            $user2->answers = array(
                                (object) array("question_id"=>"0", "mine_answer"=>"18", "partner_answer"=>"50"),
                                (object) array("question_id"=>"1", "mine_answer"=>"0", "partner_answer"=>"0"),
                                (object) array("question_id"=>"2", "mine_answer"=>"0", "partner_answer"=>"100"),
                                (object) array("question_id"=>"3", "mine_answer"=>"0", "partner_answer"=>"100"),
                                (object) array("question_id"=>"4", "mine_answer"=>"0", "partner_answer"=>"100"),
                                (object) array("question_id"=>"5", "mine_answer"=>"0", "partner_answer"=>"100")
                            );
        }
        
        //get list ansers
        $answers1 = $user1->answers;
        $answers2 = $user2->answers;

        //declare score
        $score = 0;
        foreach ($answers1 as $key => $answer) {
            if($key > 1){
                $score += 100 - abs($answer->partner_answer - $answers2[$key]->mine_answer);
            }
            elseif ($key == 1) {
                $score += 100 - abs($answer->mine_answer - $answers2[$key]->mine_answer);
            }
        }

        return $score / 500;
    }

    /**
     * Function to matching, to multiple using in other controller.
     *
     * @param  User  $user
     * @param  Boolean  $is_check_online
     * @return array
     */
    public static function matching($user, $is_check_online, $match_with_verified = 0)
    {
        // Set value user id
        $userId = $user->id;
        
        //get list id of favourites
        $lFavourIds = array();
        if(!$user->favourites->isEmpty()){
            $lFavourIds = array_pluck($user->favourites, 'partner_id');
        }

        //get list id of blockeds
        $lBlockedIds = array();
        if(!$user->blockeds->isEmpty()){
            $lBlockedIds = array_pluck($user->blockeds, 'partner_id');
        }

        //filter follow age
        $question_age = Questionary::where([
                                            ['user_id','=',$user->id],
                                            ['question_id','=',0]
                                        ])->first();

        if($question_age){
            $date_max = Carbon::create(Carbon::now()->subYears(min($question_age['mine_answer'],$question_age['partner_answer']))->year, 1, 1, 0, 0, 0);
            $date_min = Carbon::create(Carbon::now()->subYears(max($question_age['mine_answer'],$question_age['partner_answer']))->year, 1, 1, 0, 0, 0);
        }
        else{
            $date_max = Carbon::create(Carbon::now()->subYears(18)->year, 1, 1, 0, 0, 0);
            $date_min = Carbon::create(Carbon::now()->subYears(50)->year, 1, 1, 0, 0, 0);
        }

        //get user`s latitude/longitude
        $lat =$user->latitude;
        $long =$user->longitude;

        //limiting latitude and longitude to reduce the number of record from db to optimize performance.
        //30kms ~ 0.3 unit latitude/longitude
        $lat_min = $lat - 0.3;
        $lat_max = $lat + 0.3;
        $long_min = $long - 0.3;
        $long_max = $long + 0.3;

        //get list user from db
        if($is_check_online){   //if only get online user (for socket ony)
            $ldbUsers = User::where([
                                        ['latitude','<>',0],
                                        ['latitude','>',$lat_min],
                                        ['latitude','<',$lat_max],
                                        ['longitude','<>',0],
                                        ['longitude','>',$long_min],
                                        ['longitude','<',$long_max],
                                        ['id','<>',$user->id],
                                        ['gender','<>',$user->gender],
                                        ['dob','>',$date_min],
                                        ['dob','<',$date_max]
                                    ])
                            ->whereNotNull('socket_id')
                            ->whereNotNull('gender')
                            ->get();
        } else{
            if ($user->chinese_id) {
                if ($match_with_verified) {
                    $ldbUsers = User::where([
                                            ['latitude','<>',0],
                                            ['latitude','>',$lat_min],
                                            ['latitude','<',$lat_max],
                                            ['longitude','<>',0],
                                            ['longitude','>',$long_min],
                                            ['longitude','<',$long_max],
                                            ['id','<>',$user->id],
                                            ['dob','>',$date_min],
                                            ['dob','<',$date_max],
                                    ])
                                ->whereNotNull('gender')
                                ->whereNotNull('chinese_id')
                                ->get();
                } else {
                    $ldbUsers = User::where([
                                            ['latitude','<>',0],
                                            ['latitude','>',$lat_min],
                                            ['latitude','<',$lat_max],
                                            ['longitude','<>',0],
                                            ['longitude','>',$long_min],
                                            ['longitude','<',$long_max],
                                            ['id','<>',$user->id],
                                            ['dob','>',$date_min],
                                            ['dob','<',$date_max],
                                    ])
                                ->where(function($query) {
                                    return $query->whereNull('chinese_id')
                                                ->orWhere([
                                                    ['chinese_id', '!=', null],
                                                    ['match_with_verified', 0]
                                                ])
                                                ->orWhere([
                                                    ['chinese_id', '!=', null],
                                                    ['match_with_verified', 1]
                                                ])
                                                ->whereNotNull('gender');
                                })
                                ->get();
                                // ->where('id', '!=', $userId)
                                // ->whereNull('chinese_id')
                                // ->orWhere([
                                //     ['chinese_id', '!=', null],
                                //     ['match_with_verified', 0],
                                //     ['id', '!=', $userId]
                                // ])
                                // ->orWhere([
                                //     ['chinese_id', '!=', null],
                                //     ['match_with_verified', 1],
                                //     ['id', '!=', $userId]
                                // ])
                                // ->whereNotNull('gender')
                }
                
            } else {
                $ldbUsers = User::where([
                                            ['latitude','<>',0],
                                            ['latitude','>',$lat_min],
                                            ['latitude','<',$lat_max],
                                            ['longitude','<>',0],
                                            ['longitude','>',$long_min],
                                            ['longitude','<',$long_max],
                                            ['id','<>',$user->id],
                                            ['dob','>',$date_min],
                                            ['dob','<',$date_max],
                                    ])
                                ->where('id', '!=', $userId)
                                ->whereNull('chinese_id')
                                ->orWhere([
                                    ['chinese_id', '!=', null],
                                    ['match_with_verified', 0],
                                    ['id', '!=', $userId]
                                ])
                                ->whereNotNull('gender')
                                ->get();
            }
        }

        if(!$ldbUsers){ //if there is no matching partner
            return array();
        }

        //declare list partners in the rank (30kms)
        $ldistUsers = array();

        // Get questionary of userlogined
        $questionary = Questionary::where('user_id', $user->id)->first();

        // If user answered and user choosen show_male or show_female
        if (!empty($questionary) && ($questionary->show_male != 0 || $questionary->show_female != 0)) {

            foreach ($ldbUsers as $dbUser) {
                       
                // Check blocked and favorite
                if(!in_array($dbUser->id, $lBlockedIds) && !in_array($dbUser->id, $lFavourIds)){

                    // Calculate distance
                    $dist = UserController::distance($user, $dbUser);

                    //check distance, requirement is min than 30kms
                    if($dist < 30){

                        // Set the distance and matching score for user
                        $dbUser->distance = $dist;
                        $dbUser->matching_score = 0;

                        // Get questionary partners
                        $questionaryPartner = Questionary::where('user_id', $dbUser->id)->first();
                         
                        // If partner choosen hidemefromscreen
                        if (!empty($questionaryPartner) && $questionaryPartner->hidemefromhomescreen == 1) continue;

                        // Show all gender
                        if ($questionary->show_male == 1 && $questionary->show_female == 1) {

                            array_push($ldistUsers, $dbUser);

                        // Only show male
                        } elseif ($questionary->show_male == 1 && $questionary->show_female == 0) {
                            if ($dbUser->gender == 'male') array_push($ldistUsers, $dbUser);

                        // Only show female
                        } elseif ($questionary->show_male == 0 && $questionary->show_female == 1) {
                            if ($dbUser->gender == 'female') array_push($ldistUsers, $dbUser);
                        }
                    }
                }
            }

        // If user not answered
        } else if (empty($questionary)) {

            foreach ($ldbUsers as $dbUser) {

                // Check blocked and favourited
                if(!in_array($dbUser->id, $lBlockedIds) && !in_array($dbUser->id, $lFavourIds)){

                    // Calculate distance
                    $dist = UserController::distance($user, $dbUser);

                    //check distance, requirement is min than 30kms
                    if($dist < 30){

                        // Set the distance and matching score for user
                        $dbUser->distance = $dist;
                        $dbUser->matching_score = 0;

                        // Get questionary partner
                        $questionaryPartner = Questionary::where('user_id', $dbUser->id)->first();

                        // If partner choosen hidemefromscreen
                        if (!empty($questionaryPartner) && $questionaryPartner->hidemefromhomescreen == 1) continue;

                        // If partner choosen hidemefromscreen
                        if (isset($questionaryPartner->hidemefromhomescreen) && $questionaryPartner->hidemefromhomescreen == 1) continue;

                        // If partner not answered and partner's gender different user's gender
                        if ($dbUser->gender != $user->gender) array_push($ldistUsers, $dbUser);
                    }
                }
            }
        }

        //if there is no partner in the rank 30kms
        if(count($ldistUsers) == 0){ 
            return array();
        }
        
        //add matching score to partner
        foreach ($ldistUsers as $distUser) {
            $distUser->matching_score = UserController::matchingScore($user, $distUser);

            //remove answer list in response json
            $distUser = array_except($distUser, ['answers']);
        }

        //declare list of matching partners order by score
        $partners = array_reverse(array_sort($ldistUsers, function ($value) {
            return $value['matching_score'];
        }));
      
        //response
        return $partners;
    }

    /**
     * Function to matching partners.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMatchedPartners(Request $request)
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

        //checking user location
        if($user->latitude == 0 & $user->longitude == 0){
            // return response()->json(["message" => "Please update your location!"], 223);
            return response()->json(["message" => "请更新您的位置。"], 223);
        }

        //call function to matching user
        $partners = $this->matching($user, false, $user->match_with_verified);
  
        //delacre pagination varaible
        $limit = 20;
        $offset = 0;

        //check pagination param
        if($request->input('page')){
            $offset = $limit * ($request->input('page') - 1);
        }

        //check the result
        if(count($partners) == 0){
            // return response()->json(['message' => 'No partner in the rank 30 kms!'], 200);
            return response()->json(['message' => '没有结果。'], 200);
        }
        elseif(count($partners) <= $offset){
            //if the length of array < the offset, there is no thing to result
            $result = array();
        }
        elseif (count($partners) <= ($offset + $limit)) {
            //return the rest of array
            $result = array_slice($partners, $offset);
        }
        else{
            //return the limit partners
            $result = array_slice($partners, $offset, $limit);
        }

        return response()->json($result, 200);
    }

    /**
     * Function to delete user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'phone' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::where('phone','=', $request->input('phone'))->first();
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //delete conversation
        Conversation::where('user1_id','=',$user->id)
                        ->orWhere('user2_id','=',$user->id)
                        ->delete();

        //delete message
        Message::where('sender_id','=',$user->id)
                        ->orWhere('reciever_id','=',$user->id)
                        ->delete();

        // save user to table delete
        $this->saveUserDelete($user, ($request->input('reason')) ? null : $request->input('reason'));

        //delete user
        $user->delete();
        
        //response
        return response()->json(["message" => "成功了。"], 200);
    }

    public function saveUserDelete($user, $reason) {
        $user_delete = new UserDelete();
        $user_delete->user_id = $user->id;
        $user_delete->phone = $user->phone;
        $user_delete->password = $user->password;
        if($reason != null) {
            $user_delete->reason = $reason;
        }
        $user_delete->name = $user->name;
        $user_delete->avatar = $user->avatar;
        $user_delete->photos = $user->photos;
        $user_delete->introduce = $user->introduce;
        $user_delete->dob = $user->dob;
        $user_delete->gender = $user->gender;
        $user_delete->email = $user->email;
        $user_delete->education = $user->education;
        $user_delete->income = $user->income;
        $user_delete->height = $user->height;
        $user_delete->weight = $user->weight;
        $user_delete->last_used_time = $user->last_used_time;
        $user_delete->active_level = $user->active_level;

//        dd($user_delete);
        $user_delete->save();
    }

    /**
     * Function to get who save me.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function whoSavedMe(Request $request){

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

        //declare result
        $lFans = array();

        //searching from database for who add ed user to favourite
        $lFavorites = Favourite::where('partner_id','=',$user->id)->get();
        if(!$lFavorites->isEmpty()){    //check null
            foreach ($lFavorites as $favour) {
                if($favour->user){   //check null
                    //add partner to the list
                    array_push($lFans, $favour->user);
                }
            }
        }
        
        //response
        return response()->json($lFans, 200);
    }

    /**
     * Function to change password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request){
        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required', 
            'old_password' => 'required',
            'new_password' => 'required'
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

        //get data to check old password
        $data = array('phone' => $user->phone,
                    'password' => $request->input('old_password'));

        // authentication the old password
        if (Auth::attempt($data)) {    // validation successful
            //update new password
            $user->update(array('password' => bcrypt($request->input('new_password'))));

            // response user infors
            return response()->json(["message" => "成功了。"], 200);

        } else {

            // validation not successful, response to client
            // return response()->json(["message" => "Wrong password!"], 223);
            return response()->json(["message" => "密码错误。"], 223);
        }
    }

    /**
     * Function to update phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePhoneNumber(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'id' => 'required',
            'phone' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::find($request->input('id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking user existed
        $phone = User::where('phone','=',$request->input('phone'))->first();
        if ($phone) 
        {
            // return response()->json(["message" => "Phone number has already existed!"], 222);
            return response()->json(["message" => "电话号码被列出。"], 222);
        }

        //get data from params
        $data = $request->only('phone');

        //save it to database           
        $user->update($data);
        
        //response
        return response()->json($user, 200);
    }

    /**
     * Function to update password with phone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request){
        // validate the info, create rules for the request
        $rules = array(
            'phone' => 'required',
            'password' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, response to client
        if ($validator->fails()) {
            return response()->json(["message" => "出事了，请重试。"], 221);

        }

        //checking user existed
        $user = User::where('phone','=',$request->input('phone'))->first();
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //update password
        $user->update(array('password' => bcrypt($request->input('password'))));

        
        // response user infors
        return response()->json($user, 200);
    }


    /**
     * Function to logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request){

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

        //searching for device
        $device = Device::where('user_id','=',$user->id)->first();

        //checking existed
        if($device){
            $device->delete();
        }

        //response
        return response()->json(["message" => "成功了。"], 200);
    }
}
