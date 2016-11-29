<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Unnotify;
use Auth;
use Validator;

class UnnotifyController extends Controller
{
    
    /**
     * Function to refuse notification from a partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unNotify(Request $request)
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
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking had refused yet
        $unNo = Unnotify::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if($unNo) 
        {
            // return response()->json(["message" => "You had refused notification from him/her!"], 223);
            return response()->json(["message" => "成功了。"], 223);
        }

        //get data from params
        $data = $request->only('user_id', 'partner_id');

        //save it to database           
        $favourite = Unnotify::create($data);
        
        //response
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Function to cancel the refuse
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancelUnNotify(Request $request)
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
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking partner existed
        $partner = User::find($request->input('partner_id'));
        if (!$partner) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //checking had refused yet
        $unNo = Unnotify::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if(!$unNo)
        {
            // return response()->json(["message" => "You had no refused notification from him/her!"], 223);
            return response()->json(["message" => "成功了。"], 223);
        }

        //delete te refuse
        $unNo->delete();
        
        //response
        return response()->json(["message" => "成功了。"], 200);
    }
}
