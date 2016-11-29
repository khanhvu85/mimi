<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;

use DB;
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

use App\Http\Controllers\api\UserController;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show User demo page.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function userDemo(){
        //get total of users
        $user_count = User::count();

        /*
         * Searching database for user registered
         */
        //in 30 days
        $last_30_days = array();

        //declare temp date
        $temp_date = Carbon::now();
        $ago_30_days = Carbon::now()->subDays(30);

        //searching for database
        $db_registereds = User::selectRaw("count(id) as registereds, date(created_at) as date")
                ->whereRaw("date(created_at) > '".$ago_30_days->toDateString()."'")
                ->groupBy(DB::raw("date(created_at)"))
                ->orderByRaw("date(created_at) DESC")
                ->get();
        //check null
        if(!$db_registereds->isEmpty()){
            //looping the collection to fill all 30 days
            foreach ($db_registereds as $item) {
                //get the date string and translate to Date Object
                $item_date = Carbon::parse($item->date);

                //looping until the item date = temp date
                while($item_date->lte(Carbon::parse($temp_date->toDateString()))){
                    //if the item date = temp date
                    if($item_date->eq(Carbon::parse($temp_date->toDateString()))){
                        //add the item to the result array
                        array_push($last_30_days, array('date' => Carbon::parse($item->date)->format('M d'),
                                                    'registereds' => $item->registereds));

                        //subtraction temp date
                        $temp_date = $temp_date->subDay();

                        //and break the while loop
                        break;
                    }
                    //else, push the default item to the result array
                    array_push($last_30_days, array('date' => $temp_date->format('M d'),
                                                    'registereds' => 0));
                    //Subtraction temp date and continute the while loop
                    $temp_date = $temp_date->subDay();
                }
            };

            //check the temp date with the subtraction 30 days from now
            while(Carbon::parse($temp_date->toDateString())->gte(Carbon::parse($ago_30_days->toDateString()))){
                //if the temp date = the subtraction 30 days from now
                if(Carbon::parse($temp_date->toDateString())->eq(Carbon::parse($ago_30_days->toDateString()))){
                    //break the while loop
                    break;
                }
                //else, push the default item to the result array
                array_push($last_30_days, array('date' => $temp_date->format('M d'),
                                                'registereds' => 0));
                //Subtraction temp date and continute the while loop
                $temp_date = $temp_date->subDay();
            }
        } else{
            //fill all 30 days
            for($i=0; $i<30; $i++){
                //push the default item to the result array
                array_push($last_30_days, array('date' => Carbon::now()->subDays($i)->format('M d'),
                                                'registereds' => 0));
            }
        }

        //declare users registered
        $users_registered = array('last_30_days' => array_reverse($last_30_days));

        /*
         * Searching database for percentage of Gender
         */
        //percentage of male
        $males = User::where('gender','=', 'male')->count();

        //percentage of female
        $females = User::where('gender','=', 'female')->count();

        //percentage of female
        $unknown_gender = $user_count - ($males + $females);

        //declare gender compare
        $gender = array(['gender' => 'males',
                        'quantity' => $males],
                        ['gender' => 'females',
                        'quantity' => $females],
                        ['gender' => 'unknown',
                        'quantity' => $unknown_gender]);

        /*
         * Searching database for percentage of Age
         */
        //amount of < 15 years old
        $younger_than_15 = User::where('dob','>', Carbon::now()->subYears(15))->count();

        //amount of years old between 15-18
        $between_15_18 = User::where('dob','<=', Carbon::now()->subYears(15))
                            ->where('dob','>', Carbon::now()->subYears(18))
                            ->count();

        //amount of years old between 18-35
        $between_18_35 = User::where('dob','<=', Carbon::now()->subYears(18))
                            ->where('dob','>', Carbon::now()->subYears(35))
                            ->count();

        //amount of < 15 years old
        $older_than_35 = User::where('dob','<=', Carbon::now()->subYears(35))->count();

        //amount of unkown years old
        $unknown_year_old = $user_count - ($younger_than_15 + $between_15_18 + $between_18_35 + $older_than_35);

        //declare Age
        $age = array(['group' => '0 to 15',
                    'quantity' => $younger_than_15],
                    ['group' => '15 to 18',
                    'quantity' => $between_15_18],
                    ['group' => '18 to 35',
                    'quantity' => $between_18_35],
                    ['group' => 'over 35',
                    'quantity' => $older_than_35],
                    ['group' => 'unkown',
                    'quantity' => $unknown_year_old]);
        
        //render view uder demo with data
    	return view('back-end.admin.setting.demo', array('users_registered' => $users_registered,
                                        'gender' => $gender,
                                        'age' => $age));
    }

    /**
     * Show User behavior page.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function userBehaviors(){
        //get total of users
        $user_count = User::count();

        /*
         * Searching database for amount of messages send by user
         */
        //amount of users had send more than 10000 messages
        $more_than_10000 = count(Message::groupBy('sender_id')
                                    ->havingRaw("COUNT(sender_id) > 10000")
                                    ->get());

        //amount of users had send between 1000 and 10000 messages
        $between_1000_10000 = count(Message::groupBy('sender_id')
                                    ->havingRaw("COUNT(sender_id) BETWEEN 1000 AND 10000")
                                    ->get());
        
        //amount of users had send between 100 and 1000 messages
        $between_100_1000 = count(Message::groupBy('sender_id')
                                    ->havingRaw("COUNT(sender_id) BETWEEN 100 AND 1000")
                                    ->get());

        //amount of users had send less than 100 messages
        $less_than_100 = $user_count - ($more_than_10000 + $between_1000_10000 + $between_100_1000);

        //declare how angaged do users send messages
        $engaged_send = array('more_than_10000' => $more_than_10000,
                            'between_1000_10000' => $between_1000_10000,
                            'between_100_1000' => $between_100_1000,
                            'less_than_100' => $less_than_100,);

        /*
         * Searching database for when users use the app
         */
        //amount of users open the app in last 1h
        $in_last_hour = User::where('last_used_time','>=', Carbon::now()->subHour())->count();

        //amount of users open the app in last day
        $in_last_day = User::where('last_used_time','>=', Carbon::now()->subDay())->count();

        //amount of users open the app in last week
        $in_last_week = User::where('last_used_time','>=', Carbon::now()->subWeek())->count();

        //amount of users open the app before last week
        $in_last_month = User::where('last_used_time','>=', Carbon::now()->subHour()->subMonth())->count();

        //declare when use
        $when_use = array('in_last_hour' => $in_last_hour,
                        'in_last_day' => $in_last_day,
                        'in_last_week' => $in_last_week,
                        'in_last_month' => $in_last_month);

        /*
         * Searching database for active level
         */
        //hourly active -- ative level = 1
        $hourly = User::where('active_level', '<=', 1)
                        ->where('last_used_time', '>=', Carbon::now()->subHour())
                        ->count();

        //daily active -- ative level = 2
        $daily = User::where('active_level', '<=', 2)
                        ->where('last_used_time', '>=', Carbon::now()->subDay())
                        ->count();

        //weekly active -- ative level = 3
        $weekly = User::where('active_level', '<=', 3)
                        ->where('last_used_time', '>=', Carbon::now()->subWeek())
                        ->count();

        //weekly active -- ative level = 4
        $monthly = User::where('active_level', '<=', 4)
                        ->where('last_used_time', '>=', Carbon::now()->subMonth())
                        ->count();

        //declare how active
        $how_active = array('hourly' => $hourly,
                            'daily' => $daily,
                            'weekly' => $weekly,
                            'monthly' => $monthly);

        //render view user behavior with data
    	return view('back-end.admin.setting.beha', array('engaged_send' => $engaged_send,
                                        'when_use' => $when_use,
                                        'how_active' => $how_active));
    }

    /**
     * Show Settings page.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function setting()
    {
        $users = User::paginate(20);
        
        // Each user
        foreach ($users as $key => &$user) {
            $data = UserController::matching($user, 0, $user->match_with_verified);
            $user->matches = count($data);
        }

        return view('back-end.admin.setting.setting', compact('users'));
    }

    /**
     * Login page.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        return view('admin.login');
    }

    /**
     * Action handler login function.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function doLogin(Request $request){
        // set the remember me cookie if the user check the box
        $remember = $request->input(['remember'], true);
 
        // attempt to do the login
        $auth = Auth::guard('admin')->attempt(
            [
                'username'  => $request->input(['username']),
                'password'  => $request->input(['password'])
            ], $remember
        );
        if ($auth) {
            return redirect()->intended('admin/user-demo');
        } else {
            // validation not successful, send back to form 
            return Redirect::to('admin/login')
                ->with('username', $request->input(['username']))
                ->with('password', $request->input(['password']))
                ->with('error_message', 'Your username/password combination was incorrect!');
        }
    }
}
