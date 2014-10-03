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



        
    /*
     * ProviderRegistration
     * 
     */
    
    public function getProviderRegistration(){
        $input = Input::all();
        $input['provider'] = new FProvider();
        $input['provider_plan_basic'] = ProviderPlans::where('id','1')->first();
        $input['provider_plan_premium'] = ProviderPlans::where('id','2')->first();
        
        return View::make('users.provider-registration', $input);
    }

   
    public function postRegisterProvider()
    {
        Session::put('new_provider_data', Input::all());
        $input = [
                'plan_id' => Input::get('plan_id'),
                'business_name' => Input::get('business_name'),
                'address' => Input::get('address'),
                'city' => Input::get('city'),
                'state' => Input::get('state'),
                'zip' => Input::get('zip'),
                'website' => Input::get('website'),
                'phone' => Input::get('phone'),
                'fax' => Input::get('fax'),
                'provider_radius' => 5,
                'email' => Input::get('email'),
                'password' => Input::get('password'),
                'password_confirmation' => Input::get('password_confirmation')
        ];
        $rules = [
                'business_name' => 'required',
                //'address' => 'required',
                //'city' => 'required',
                //'state' => 'required',
                'zip' => 'required',
                //'website' => 'required|url',
                //'phone' => 'required',
                //'fax' => 'required',
                //'provider_radius' => 'required',
                'email' => 'required|email',
                //'email' => 'required',
                'password' => 'required|confirmed',
        ];

        // validate the input
        $v = Validator::make($input,$rules);
        if( $v->fails() ) return Redirect::back()->withErrors($v);

        $users = User::where('email', Input::get('email'))->first();
        if($users!=null)return Redirect::back()->withErrors("User already exists");

        // run the transaction
        return DB::transaction(function()
        {
                // Create User
            $user = Sentry::createUser(array(
                'email'       => Input::get('email'),
                'password'    => Input::get('password'),
                'role'        => 'provider',
                'activated'   => true,
            ));

            $input = [
                'plan_id' => Input::get('plan_id'),
                'business_name' => Input::get('business_name'),
                'email' => Input::get('email'),
                'address' => Input::get('address'),
                'city' => Input::get('city'),
                'state' => Input::get('state'),
                'zip' => Input::get('zip'),
                'website' => Input::get('website'),
                'phone' => Input::get('phone'),
                'fax' => Input::get('fax'),
                'provider_radius' => 5,
                'user_id' => $user->id
            ];


            // dd($input);
            //$new_provider = DB::table('providers')->insert($input);

            $provider = new FProvider();  
            $provider->fill($input);
            $provider->save(); 

            $zips = new ProviderZip();
            $zips->fill( Array('zip'=>Input::get('zip'), 'provider_id'=>$provider->id) );
            $zips->save(); 

            $pricing = new ProviderPricingOptions();
            $pricing->fill( Array('provider_id'=>$provider->id) );
            $pricing->save(); 

            $bill = Billing::first();
            
            $billing = new ProviderBilling();
            $billing->fill( Array('provider_id'=>$provider->id) );
            $billing->monthly_fee = $bill->monthly_fee;
            $billing->per_case_fee = $bill->per_case_fee;
            $billing->save(); 


            //CREATE FRESHBOOKS ENTRY
            $admin = new AdminController();
            $client_id = $admin->postAddBillingClient($provider->id);
            if($client_id!='')$admin->postAddRecurringBillingToClient($provider->id);
            // $provider = FProvider::create($input);
             //dd($provider);

            
            DB::table('users_groups')->insert(['user_id'=>$user->id,'group_id'=>2]);

            //$user = Sentry::findUserById($user->id);
            //Sentry::login($user, false);
            
            
            $admin_settings = AdminSetting::where('setting_type','provider_registration_message')->first();
            $success = $admin_settings->setting_value;
              
            $mail_data['provider'] = $provider;
            $mail_data['pass']= Input::get('password');
            Mail::send('emails.provider-welcome', $mail_data, function($message) use($mail_data)
            {
                //$message->from('us@example.com', 'Laravel');
                $message->to($mail_data['provider']->email)->cc('forcremation@gmail.com');
                //$message->attach($pathToFile);
            });
            
            //Session::put('new_provider_data',array());
            Session::flash('success', $success);
            return Redirect::action('UserController@getProviderRegistration');
        });
    }

    
    
    public function getProviderTerms(){
        $admin_settings = AdminSetting::where('setting_type','provider_registration_letter')->first();
        echo $admin_settings->setting_value;
        die();
    }
        
}