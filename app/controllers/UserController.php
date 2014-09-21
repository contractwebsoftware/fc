<?php

class UserController extends BaseController {

	public function getLogin()
	{
		return View::make('users.login');
	}

	public function postLogin()
	{
		// take the input
		$credentials = ['email'=>Input::get('email'),'password'=>Input::get('password')];
		$rules = ['email'=>'required|email','password'=>'required'];

		// validate the credentials
		$v = Validator::make($credentials,$rules);
		if( $v->fails() ) return Redirect::back()->withErrors($v);

	    // Try to authenticate the user
	    // $user = Sentry::authenticate($credentials, false);

		// Process the data and redirect to the main page
		try
		{
		    // Try to authenticate the user
		    $user = Sentry::authenticate($credentials, false);
                  
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
			Session::flash('error','Login field is required');
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
			Session::flash('error','Password field is required');
		}
		catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
		{
		    Session::flash('error','Wrong password, please try again!');
		    return Redirect::back();
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    Session::flash('error','User not found');
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
		    Session::flash('error','User is not activated');
		}
		if(Sentry::getUser()->role=='admin') {
                    //dd(Session::get('provider'));
            
                    return Redirect::action('AdminController@getProviders');
		}else{
                    
                    $provider = FProvider::where('user_id',Sentry::getUser()->id)->first();
                    Session::put('logged_in_provider_id',$provider->id);
                    Session::put('provider',$provider);
                    return Redirect::action('AdminController@getCustomers');
		}
	}

	public function getLogout(){
                Session::flush();
                Sentry::logout();
		//return Redirect::to('/users/login');
                return Redirect::action('UserController@getLogin');
	}
	public function getCreateUser() {
		try
		{
		    // Create the user
		    $user = Sentry::createUser(array(
		        'email'     => 'bendavol@gmail.com',
		        'password'  => 'testing',
		        'activated' => true,
		    ));

		    // Find the group using the group id
		    $adminGroup = Sentry::findGroupById(1);

		    // Assign the group to the user
		    $user->addGroup($adminGroup);
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
		    echo 'Login field is required.';
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
		    echo 'Password field is required.';
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
		    echo 'User with this login already exists.';
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
		    echo 'Group was not found.';
		}
	}

}