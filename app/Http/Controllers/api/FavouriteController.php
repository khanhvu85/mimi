<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Favourite;
use App\Block;
use Auth;
use Validator;

class FavouriteController extends Controller
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
     * Function to add favourite.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addFavourite(Request $request){

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

        //checking have added yet
        $favour = Favourite::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if ($favour) 
        {
            // return response()->json(["message" => "You had added him/her to your favourite!"], 223);
            return response()->json(["message" => "成功了。"], 223);
        }

        //get data from params
        $data = $request->only('user_id', 'partner_id');

        //save it to database           
        $favourite = Favourite::create($data);
        
        //response
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Function to remove favourite.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeFavourite(Request $request){

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

        //checking have added yet
        $favour = Favourite::where([
                                ['user_id','=',$request->input('user_id')],
                                ['partner_id','=',$request->input('partner_id')],
                            ])->first();
        if (!$favour) 
        {
            // return response()->json(["message" => "Him/Her is not existed in your favourite!"], 223);
            return response()->json(["message" => "成功了。"], 223);
        }

        //save it to database           
        $favour->delete();
        
        //response
        return response()->json(["message" => "成功了。"], 200);
    }

    /**
     * Function to get favourite list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFavouriteList(Request $request){

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

        //declare return list
        $result = array();

        //delacre pagination varaible
        $limit = 20;
        $offset = 0;

        //check pagination param
        if($request->input('page')){
            $offset = $limit * ($request->input('page')-1);
        }

        //get list favourite from DB
        $favourites = Favourite::where('user_id','=',$user->id)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        //check to add to the list
        if(!$favourites->isEmpty()){
            foreach ($favourites as $favourite) {
                if($favourite->partner){
                    //checking blocked
                    $blocked = Block::where([
                        ['user_id','=',$user->id],
                        ['partner_id','=',$favourite->partner->id]
                    ])->first();

                    if(!$blocked){  //if partner is not exist in the blocked list
                        array_push($result, $favourite->partner);
                    }
                }
            }
        }

        //response
        return response()->json($result, 200);
    }

    /**
     * Function to search favourite list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchFavourite(Request $request){

        // validate the info, create rules for the request
        $rules = array(
            'user_id' => 'required',
            'keys' => 'required'
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

        //get partner whom user favourite
        $lOnlFansId = array();

        //searching from database
        $lFans = Favourite::select('partner_id')->where('user_id', '=', $user->id)->get();

        //get list id
        $lFansid = array_pluck($lFans->toArray(), 'partner_id');

        //get searching keys
        $str_search = $request->input('keys');

        //searching for database
        $result = User::where(function($query) use ($str_search)
                {
                    $columns = ['name', 'phone'];

                    foreach ($columns as $column)
                    {
                        $query->orWhere($column, 'LIKE', '%'.$str_search.'%');
                    }
                })
                ->whereIn('id', $lFansid)
                ->get();
        
        //response
        return response()->json($result, 200);
    }
}
