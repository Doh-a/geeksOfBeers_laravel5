<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Home :
Route::get('/', 'HomeController@showIndex');

Route::get('homeBeers', 'HomeController@randomBeers');
Route::get('homeStats', 'HomeController@homeStats');

//Search:
Route::post( 'searchQuery', 'SearchController@mainSearch');
Route::get( 'searchQuery/{searchKey}', 'SearchController@mainSearch');

//User account
Route::controller('account','AccountController' );


//Brasserie
Route::get('brasserie/{id}', 'BrasserieController@brasseriePresentation');
Route::get('brasseriesComments/{id}/{number}/{from}', 'BrasserieController@brasserieComments');

Route::get('brasserieFanAction/{id}/{status}', 'BrasserieUserController@brasserieFan');
Route::get('brasserieUserDeleteComment/{id}', 'BrasserieUserController@brasserieDeleteComment');
Route::post( 'brasserie', array(
    'as' => 'brewery.new_comment',
    'uses' => 'BrasserieUserController@brasserieNewComment'
) );

Route::post( 'findBrewery', 'BrasserieController@brasserieSearch');
Route::get( 'findBrewery/{searchKey}', 'BrasserieController@brasserieSearch');
Route::get( 'breweriesUserStats/{user_id}', 'BrasserieUserController@brasseriesTested');

if (Auth::user() != null)
	Route::get('brasseries' , 'BrasserieController@brasseriesHomeLogged');
else
	Route::get('brasseries' , 'BrasserieController@brasseriesHome');

Route::get('brasseriesListe', function()
{
	$brasseries = Brasserie::paginate(15);
    
	return View::make('brasserie/brasseriesListe')->with('brasseries', $brasseries);
});
Route::get( '/brasseriesUser/{brasserieId}/{userId}', 'BrasserieUserController@bieresTestee' );


//User biere
Route::get('userBiere/{biereId}/{userId}/{newNote}', 'BiereUserController@rateBiere');
Route::get('userBiere/{userId}', 'BiereUserController@getBeersRated');
Route::get('userBiereComment/{biereId}/{userId}', 'BiereUserController@getUserBeerComment');
Route::get('userFriendsRates/{biereId}/{userId}', 'BiereUserController@getFriendsRates');

//Beers
Route::get('add', 'BiereController@addForm');
Route::post('add', array(
	'before' => 'csrf',
	'as' => 'add.biere',
	'uses' => 'BiereController@addBiere')
	);
Route::get('biere/{id}', array('as' => 'biere', 'uses' => 'BiereController@bierePresentation'));
Route::post( 'addBeerComment', array(
    'as' => 'beers.new_comment',
    'uses' => 'BiereUserController@biereNewComment'
) );
Route::get('biere', 'BiereController@getBeersHomePage');

Route::get( 'findBeer/{searchKey}', 'BiereController@beerSearch');

//Users
Route::get('user/{id}', 'UserController@userPresentation');
Route::get('userFriendAction/{id}/{status}', 'UserController@userFriend');
Route::get('userStats/{id}', 'UserController@userStats');


//Timelines 
Route::get('timeline/{user_id}', 'TimelineController@getMainTimeline');

//Users events
Route::get('likeEvent/{id_event}/{like_type}', 'UserEvents@likeEvent');
Route::post('commentEvent/{id_event}', 'UserEvents@commentEvent');
Route::get('commentEvent/{id_event}', 'UserEvents@commentEvent');

//Database management:
Route::get('database', 'DatabaseController@breweries');
Route::get('database/breweries', 'DatabaseController@breweries');
Route::get('database/editBrewery/{id_brewery}', 'DatabaseController@editBreweryForm');
Route::post('database/editBrewery', 'DatabaseController@editBrewery');
Route::get('database/beers', 'DatabaseController@beers');

//File upload
Route::post('upload',array('as'=>'upload', 'before'=>'auth','uses'=>'UploadController@index'));

//Dingo Api:
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
	$api->get('biere/{id}', function($beer_id)
	{
		if(app('Dingo\Api\Auth\Auth')->user())
			return App::call('\App\Api\V1\Controllers\BiereController@biereUserDatasAsText', array('biere' => $beer_id));
		return App::call('\App\Api\V1\Controllers\BiereController@biereDatasAsText', array('biere' => $beer_id));
	});
		
	$api->get('login', function(){
		if(app('Dingo\Api\Auth\Auth')->user())
			return App::call('\App\Api\V1\Controllers\UserController@loggedUserInfo');
		return null;
	});
		
	$api->get('randomBeers', array('as' => 'biere', 'uses' => 'App\Api\V1\Controllers\BiereController@randomBeers'));

	$api->get('listTypes', 'App\Api\V1\Controllers\TypeController@listAll');

	$api->get('lookForBrewery/{text}', 'App\Api\V1\Controllers\BreweryController@lookForBrewery');

	$api->post('addBeer', 'App\Api\V1\Controllers\BiereController@addBeer');
});