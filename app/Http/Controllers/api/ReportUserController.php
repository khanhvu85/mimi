<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\ReportUser;
use Auth;
use Validator;

class ReportUserController extends Controller
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
     * Function to report a partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addReport(Request $request){

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

        //get data from params
        $data = $request->only('user_id', 'partner_id', 'comment');

        //checking had reported yet
        $report = ReportUser::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();

        if ($report) //if existed, update comment
        {
            $report->update($data);
        }

        //save it to database           
        $favourite = ReportUser::create($data);
        
        //response
        return response()->json(["message" => "成功了。"], 200);
    }
}
