<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Http\Middleware\ApiMiddleware;

Route::get('/', ['as' => 'baseURL', function () {
    return view('welcome');
}]);

//Routing to Admin Panel
Route::group(['namespace' => 'admin', 'prefix' => 'admin'], function()
{
	//Routing to general APIs: signup/login/report ChineseID error
	Route::get('user-demo', 'SiteController@userDemo');
	Route::get('user-behaviors', 'SiteController@userBehaviors');
	Route::get('setting', 'SiteController@setting');

	Route::get('/', 'SiteController@login');
	Route::get('login', [ 'as' => 'admin.login',
		'uses' => 'SiteController@login']);
	Route::post('doLogin', [ 'as' => 'admin.doLogin',
		'uses' => 'SiteController@doLogin']);

	Route::resource('admins', 'AdminController');

});

//Routing to APIs
Route::group(['namespace' => 'api', 'prefix' => 'api'], function()
{
	//Routing to general APIs: signup/login/report ChineseID error
	Route::post('signup', 'UserController@signup');
	Route::post('login', 'UserController@login');
	Route::post('logout', 'UserController@logout');
	Route::post('reportIDError', 'UserController@reportIDError');

	//Routing to upload media: image/audio/video
	Route::post('upload', 'MediaController@upload');

	// //Use middleware
	// Route::group(['middleware' => 'auth.api'], function()
	// {
		//Routing to User management
		Route::get('user/info', 'UserController@info');
		Route::get('user/viewInfo', 'UserController@viewInfo');
		Route::post('user/updateInfo', 'UserController@updateInfo');
		Route::post('user/updateChineseID', 'UserController@updateChineseID');
		Route::post('user/updateLocation', 'UserController@updateLocation');
		Route::post('user/updatePhoneNumber', 'UserController@updatePhoneNumber');
		Route::post('user/changePassword', 'UserController@changePassword');
		Route::post('user/updatePassword', 'UserController@updatePassword');

		Route::get('user/whoSavedMe', 'UserController@whoSavedMe');
		Route::get('user/whoViewedMe', 'ViewInfoController@whoViewedMe');

		//Routing to favourite
		Route::post('user/addFavourite', 'FavouriteController@addFavourite');
		Route::post('user/removeFavourite', 'FavouriteController@removeFavourite');
		Route::get('user/getFavouriteList', 'FavouriteController@getFavouriteList');
		Route::get('user/searchFavourite', 'FavouriteController@searchFavourite');

		//Routing to Questionaries
		Route::post('user/answerQuestion', 'QuestionaryController@answer');
		Route::get('user/getAnswerList', 'QuestionaryController@getAnswerList');	

		//Routing to block partner
		Route::post('user/blockPartner', 'BlockController@blockPartner');
		Route::post('user/unblockPartner', 'BlockController@unblockPartner');
		Route::get('user/getBlockedList', 'BlockController@getBlockedList');

		//Routing to unnotify from a partner
		Route::post('user/unNotifyFromPartner', 'UnnotifyController@unNotify');
		Route::post('user/cancelUnNotify', 'UnnotifyController@cancelUnNotify');

		//Routing to setting Push Notification
		Route::post('user/settingNotify', 'UserSettingController@settingNotify');
		Route::get('user/getSettingNotify', 'UserSettingController@getSettingNotify');

		//Routing to report a partner
		Route::post('user/reportPartner', 'ReportUserController@addReport');

		//Routing to get list matching partners
		Route::get('user/getMatchedPartners', 'UserController@getMatchedPartners');

		//Routing message
		Route::post('message/add', 'MessageController@add');
		Route::post('message/remove', 'MessageController@remove');
		Route::post('message/read', 'MessageController@read');
		Route::post('message/readGroup', 'MessageController@readGroup');

		//Routing to clear all chatting histories
		Route::post('message/clearAllChattingHistories', 'MessageController@clearAllChattingHistories');

		//Routing to Conversation
		Route::get('conversation/getConversation', 'ConversationController@getConversation');
		Route::get('conversation/getConversationList', 'ConversationController@getConversationList');
		Route::get('conversation/getRecentConversations', 'ConversationController@getRecentConversations');
		Route::get('conversation/getSettings', 'ConversationController@getSettings');
		Route::get('conversation/getDeleteMessageSentTime', 'ConversationController@getDeleteMessageSentTime');

		Route::post('conversation/updateDeleteMessageSentTime', 'ConversationController@updateDeleteMessageSentTime');
		Route::post('conversation/updateConversation', 'ConversationController@updateConversation');
		Route::post('conversation/remove', 'ConversationController@remove');
		Route::post('conversation/readAllMessages', 'ConversationController@readAllMessages');
		Route::post('conversation/clearAllMessages', 'ConversationController@clearAllMessages');
	// });

	//Routing for action call from socket
	Route::post('socket/startServer', 'SocketController@startServer');
	Route::post('socket/join', 'SocketController@join');
	Route::post('socket/leave', 'SocketController@leave');
	Route::post('socket/chatMessage', 'SocketController@chatMessage');
	Route::post('socket/readMessage', 'SocketController@readMessage');
	Route::post('socket/readConversation', 'SocketController@readConversation');


	//Routing to test push notification
	Route::get('pushTesting', 'PushNotificationController@pushTesting');
	//Routing to User management
	Route::post('user/delete', 'UserController@delete');

});