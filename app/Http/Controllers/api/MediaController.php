<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Media;
use Validator;

class MediaController extends Controller
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
     * Upload an media file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        // setting up rules
        $rules = array(
            'type'    => 'required|in:"image","audio","video"', 
            'file' => 'required' 
        );

        // run the validation rules on the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // if the validator fails, response to client
            // return response()->json(["message" => "Invalid params!"], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }

        // checking file is valid.
        if ($request->file('file')->isValid()) {
            $destinationPath = 'uploads/'.$request->input("type"); // upload path

            $extension = $request->file('file')->getClientOriginalExtension(); // getting image extension
            $current_time = Carbon::now()->timestamp; //getting currenrt time to make unique name

            $fileName = $current_time.str_random(30).'.'.$extension; // renameing image
            
            $request->file('file')->move($destinationPath, $fileName); // uploading file to given path

            //save to database
            $media_data = array('type' => $request->input("type"),
                                'name' => $fileName,
                                'url' => route('baseURL').'/'.$destinationPath.'/'.$fileName);

            $media = Media::create($media_data);

            // sending back with message
            return response()->json($media, 200);
        }
        else {
            // sending back with error message.
            // return response()->json(["message" => "Invalid uploaded file!"], 221);
            return response()->json(["message" => "出事了，请重试。"], 221);
        }


    }
}
