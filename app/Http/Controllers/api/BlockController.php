<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Block;
use Auth;
use Validator;

class BlockController extends Controller
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
     * Function to block a partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function blockPartner(Request $request){

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

        //checking had blocked yet
        $favour = Block::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if ($favour) 
        {
            // return response()->json(["message" => "You had blocked the user!"], 223);
            return response()->json(["message" => "您已经封锁了用户。"], 223);
        }

        //get data from params
        $data = $request->only('user_id', 'partner_id');

        //save it to database           
        $favourite = Block::create($data);
        
        //response
        // return response()->json(["message" => "Block successful!"], 200);
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Function to unblock a partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unblockPartner(Request $request){

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

        //checking have added yet
        $block = Block::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if (!$block) 
        {
            // return response()->json(["message" => "The user is not existed in your blocked list!"], 223);
            return response()->json(["message" => "你没有阻止用户。"], 223);
        }

        //save it to database           
        $block->delete();
        
        //response
        // return response()->json(["message" => "Unblock successfull!"], 200);
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Function to get blocked list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBlockedList(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
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

        //declare return list
        $result = array();

        //delacre pagination varaible
        $limit = 20;
        $offset = 0;

        //check pagination param
        if($request->input('page')){
            $offset = $limit * ($request->input('page')-1);
        }

        //get list blocked from DB
        $blockeds = Block::where('user_id','=',$user->id)
                            ->orderBy('updated_at', 'desc')
                            ->limit($limit)
                            ->offset($offset)
                            ->get();

        //check to add to the list
        if(!$blockeds->isEmpty()){
            foreach ($blockeds as $block) {
                if($block->partner){
                    array_push($result, $block->partner);
                }
            }    
        }
        
        //response
        return response()->json($result, 200);
    }

}
