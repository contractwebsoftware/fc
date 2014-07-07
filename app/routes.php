<?php

Route::group(array('before' => 'Sentry|inGroup:Admin'), function()
{
	Route::controller('admin','AdminController');
});
Route::group(array('before' => 'Sentry|inGroup:Provider'), function()
{
	Route::controller('providers','ProviderController');
});

Route::group(array('before' => 'Sentry|inGroup:Customer'), function()
{
	 Route::controller('clients','ClientController');
});

Route::get('this', array('as'=>'routename','uses'=>'controllerName@method'));
/*
Route::get('/', ['as'=>'clients.steps',function(){
    
        return Redirect::to('/clients/steps');
}]);*/

Route::get('users', ['as'=>'user.login',function(){
	return Redirect::to('users/login');
}]);
Route::controller('users','UserController');
Route::controller('usergroups','UsergroupController');

Route::controller('clients','ClientController');
Route::controller('steps','StepController');


Route::get('test', function(){
	$user = Sentry::createUser(array(
	        'email'       => 'bendavol@gmail.com',
	        'password'    => 'test',
	        'activated'   => true,
	    ));
});
Route::get('test2',function(){
	return User::all();
});