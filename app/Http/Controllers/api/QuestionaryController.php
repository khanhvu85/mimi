<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Questionary;
use Auth;
use Validator;

class QuestionaryController extends Controller
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
     * Add an answer for an user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function answer(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'user_id'    => 'required',
            'answer_data'    => 'required',
            'hidemefromhomescreen' => 'required',
            'show_male' => 'required',
            'show_female' => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // if the validator fails, response to client
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user)
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        //check only verified
        if($request->has('match_with_verified')){
            //check registration
            if($user->chinese_id != null){
                //update match_with_verified
                if($user->match_with_verified != $request->input('match_with_verified')){
                    $user->update(array('match_with_verified' => $request->input('match_with_verified')));
                }
            }
        }

        //get data from params
        $answer_data = json_decode($request->input('answer_data'), true);

        //check answer data
        if(count($answer_data) == 0){ //if there is no answer
            return response()->json(["message" => "出事了，请重试。"], 221);
        }
        foreach ($answer_data as $answer) {
            //get data for each answer
            $answer = array_only($answer, array('question_id', 'mine_answer', 'partner_answer'));
            $answer['user_id'] = $request->input('user_id');
            $answer['hidemefromhomescreen'] = $request->input('hidemefromhomescreen');
            $answer['show_male'] = $request->input('show_male');
            $answer['show_female'] = $request->input('show_female');

            //check question_id=0: default mine_answer=18, partner_answer=50
            if($answer['question_id'] == 0){
                if($answer['mine_answer'] == 0){
                    $answer['mine_answer'] = 18;
                }

                if($answer['partner_answer'] == 100){
                    $answer['partner_answer'] = 50;
                }
            }

            $quest = Questionary::where([
                ['user_id','=',$answer['user_id']],
                ['question_id','=',$answer['question_id']],
            ])->first();

            if(!$quest){    //user+question not existed, create an answer
                $question = Questionary::create($answer);
            }
            else {  //user+question is existed, update answer
                $question = $quest->update($answer);
            }
        }

        //checking user+question existed

        //response by json
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * List os answers of an user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAnswerList(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'user_id'    => 'required'
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // if the validator fails, response to client
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        //checking user existed
        $user = User::find($request->input('user_id'));
        if (!$user) 
        {
            return response()->json(["message" => "出事了，请重试。"], 222);
        }

        // $resultOject = object;
        //if user have not answer any question
        if(count($user->answers) == 0){
            return response()->json((object) null, 200);
        }

        //declare answers
        $answers = $user->answers;
        $item_remove = null;
        // remove hidemefromhomescreen, show_male, show_female
        foreach ($answers as $answer) {
            $item_remove['hidemefromhomescreen'] = $answer['hidemefromhomescreen'];
            $item_remove['show_male'] = $answer['show_male'];
            $item_remove['show_female'] = $answer['show_female'];
            unset($answer['hidemefromhomescreen'], $answer['show_male'], $answer['show_female']);
        }

        //result to response
        $result = array(
            'match_with_verified' => $user->match_with_verified,
            'hidemefromhomescreen' => $item_remove['hidemefromhomescreen'],
            'show_male' => $item_remove['show_male'],
            'show_female' => $item_remove['show_female'],
            'answers' => $answers
        );

        //response by json
        return response()->json($result, 200);
    }
}
