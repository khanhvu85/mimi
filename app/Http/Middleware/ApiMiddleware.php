<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Device;
use Carbon\Carbon;


class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //get device token from request
        $token = $request->input('device_token', null);

        //if the request has no device token, no need to check --- only for dev
        if(!$token){
            return $next($request);
        } 

        //search for device token
        $device = Device::where('device_token', '=', $token)->first();

        //check device existed
        if(!$device){
            return response()->json(["message" => "Unauthorized Device Token!"], 401);  
        }

        //check user
        if($device->user){
            //declare user
            $user = $device->user;

            //declare temporary variable
            $active_level = $user->active_level;
            $last_used_time = Carbon::parse($user->last_used_time);
            $now = Carbon::now();

            //check to update active level
            switch ($active_level) {
                //if database active level is hourly active
                case 1:
                    //check last used time
                    if($last_used_time->lt($now->subMonth())){
                        //last used time < 1 month ago
                        $user->update(array('active_level' => 5,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    elseif ($last_used_time->lt($now->subWeek())){
                        //last used time < 1 week ago
                        $user->update(array('active_level' => 4,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    elseif ($last_used_time->lt($now->subDay())){
                        //last used time < 1 day ago
                        $user->update(array('active_level' => 3,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    elseif ($last_used_time->lt($now->subHour())){
                        //last used time < 1 hour ago
                        $user->update(array('active_level' => 2,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    break;

                //if database active level is daily active
                case 2:
                    //check last used time
                    if($last_used_time->lt($now->subMonth())){
                        //last used time < 1 month ago
                        $user->update(array('active_level' => 5,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    elseif ($last_used_time->lt($now->subWeek())){
                        //last used time < 1 week ago
                        $user->update(array('active_level' => 4,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    elseif ($last_used_time->lt($now->subDay())){
                        //last used time < 1 day ago
                        $user->update(array('active_level' => 3,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    break;

                //if database active level is weekly active
                case 3:
                    //check last used time
                    if($last_used_time->lt($now->subMonth())){
                        //last used time < 1 month ago
                        $user->update(array('active_level' => 5,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    elseif ($last_used_time->lt($now->subWeek())){
                        //last used time < 1 week ago
                        $user->update(array('active_level' => 4,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    break;

                //if database active level is monthly active
                case 4:
                    //check last used time
                    if($last_used_time->lt($now->subMonth())){
                        //last used time < 1 month ago
                        $user->update(array('active_level' => 5,
                                            'last_used_time' => Carbon::now()->toDateTimeString()));
                    }
                    break;

                default:
                    break;
            }
        }

        //next to action
        return $next($request);
    }
}
