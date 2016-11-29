<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\ViewInfo;
use Auth;
use Validator;
use Carbon\Carbon;

class ViewInfoController extends Controller
{
    /**
     * Function to get list partner that viewed user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function whoViewedMe(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
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
        $result = array();

        //delacre pagination varaible
        $limit = 20;
        $offset = 0;

        //check pagination param
        if($request->input('page')){
        	$offset = $limit * ($request->input('page')-1);
        }

        //searching from database
        $viewers = ViewInfo::where('partner_id','=',$user->id)->limit($limit)->offset($offset)->orderBy('updated_at', 'desc')->get();
        if(!$viewers->isEmpty()){
        	foreach ($viewers as $viewer) {
        		if($viewer->user){	//check user existed
                    //declare a temporary varaible
                    $tuser = $viewer->user;

                    //set the viewing time
                    $tuser['viewing_time'] = $viewer->viewing_time;

                    //set the viewing time
                    $tuser['viewing_time_ago'] = Carbon::parse($viewer->viewing_time)->diffForHumans();
                    
                    //get the matching score
                    $tuser['matching_score'] = UserController::matchingScore($user, $viewer->user);

                    $tuser = array_except($tuser, ['answers']);

        			//add to the list
        			array_push($result, $tuser);
        		}
        	}
        }
        
        //response
        return response()->json($result, 200);
    }
}
