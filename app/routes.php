<?php


//Are we showing from another site?
if(Input::get('no-frame')=='y')Session::put('no-frame','y');
elseif(Input::get('no-frame')=='n')Session::put('no-frame',null);

/* CATCHALLS FOR ENTERING THE REGISTRATION AND PASSING PROVIDER IDS */
Route::get('clients/steps/provider_id={id}', function($id)
{
    return Redirect::to('clients/steps?provider_id='.$id);
});
Route::get('clients/steps/provider={id}', function($id)
{

    return Redirect::to('clients/steps?provider_id='.$id);
});
Route::get('clients/steps/id={id}', function($id)
{

    return Redirect::to('clients/steps?provider_id='.$id);
});
/* END CATCHALLS FOR ENTERING THE REGISTRATION AND PASSING PROVIDER IDS */



Route::group(array('before' => 'Sentry|inGroup:Admin'), function()
{
    Route::controller('admin','AdminController');

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
Route::group(array('before' => 'Sentry|inGroup:Provider'), function()
{
	//Route::controller('providers','ProviderController');
    Route::controller('admin','AdminController');
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
Route::controller('admin','AdminController');


Route::get('test', function(){
    $user = Sentry::createUser(array(
            'email'       => 'bendavol@gmail.com',
            'password'    => 'test',
            'activated'   => true,
        ));
});
Route::get('test2',function(){
    //return User::all();
    /*
    Mail::send('emails.provider-welcome', array('provider'=>new FProvider(),'pass'=>'testpass'), function($message)
        {

            $message->from('forcremation@gmail.com', 'ForCremation');

            $message->to('bendavol@gmail.com');
            //$message->attach($pathToFile);
        });*/
            
});

Route::get('/',function(){

    header("Location: https://provider.forcremation.com/clients/steps");
    die();
});


App::missing(function($exception) {
    //Log::info("Route Missing Path:: ".Request::path());
    //Log::info("Route Missing: ".implode(',',$_SERVER));
    header("Location: https://provider.forcremation.com/clients/steps");
    die();
});
