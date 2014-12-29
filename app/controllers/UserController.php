<?php

class UserController extends BaseController {
    protected $layout = 'layouts.admin';


    public function getLogin()
    {
        Session::flush();
        Sentry::logout();
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
            }elseif(Sentry::getUser()->role=='provider') {

                $provider = FProvider::where('user_id',Sentry::getUser()->id)->first();
                Session::put('logged_in_provider_id',$provider->id);
                Session::put('provider',$provider);
                return Redirect::action('AdminController@getCustomers');
            }
            elseif(Sentry::getUser()->role=='client') {

                //
                //dd($user);
                $client = Client::where('user_id',$user->id)->first();
                Session::put('client_id',$client->id);

                return Redirect::action('ClientController@getSteps');
            }
    }

    public function getLogout(){
            Session::flush();
            Sentry::logout();
            //return Redirect::to('/users/login');
            return Redirect::action('UserController@getLogin');
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
                'contact_first_name' => Input::get('contact_first_name'),
                'contact_last_name' => Input::get('contact_last_name'),
                'contact_email' => Input::get('contact_email'),
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
                'contact_first_name' => 'required',
                'contact_last_name' => 'required'
        ];

        // validate the input
        $v = Validator::make($input,$rules);
        if( $v->fails() )
            return Redirect::action('UserController@getProviderRegistration', $input)
            ->withErrors($v);
            //return Redirect::back()->withInput($input)->withErrors($v);

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
                'contact_first_name' => Input::get('contact_first_name'),
                'contact_last_name' => Input::get('contact_last_name'),
                'contact_email' => Input::get('contact_email'),
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
            //$admin = new AdminController();
            //$client_id = $admin->postAddBillingClient($provider->id);
            //if($client_id!='')$admin->postAddRecurringBillingToClient($provider->id);
            
            
            
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





    /*    MANAGE USERS TABLE
     *      Crud Functions
     *
     *
     *
     */
    public function getManageUsers() {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');


        $per_page = 50;
        if(Input::get('per'))$per_page = Input::get('per');

        $q = Input::get('q');
        if(strlen($q)>=3)
        {
            $users = User::where('email','like','%'.$q.'%')->where('role','admin')->orWhere('first_name','like','%'.$q.'%')
                ->orWhere('last_name','like','%'.$q.'%');
        }
        else $users = User::where('role','admin');

        $users = $users->orderBy('created_at', 'desc')->paginate($per_page);

        //dd($users);
        $data['users'] = $users;


        $this->layout->content = View::make('admin.manage-users', $data);
    }

    public function getNewUser(){
        $data['user'] = new User;
        $data['title'] = "Add New";
        $this->layout->content = View::make('admin.edit-users', $data);
    }

    public function getEditUser($id){
        $data['user'] = User::find($id);
        $data['title'] = "Edit";

        $this->layout->content = View::make('admin.edit-users', $data);
    }

    public function postUpdateUser($user_id=''){
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');

        $input = Input::all();
        if($user_id != '')$input['user']['id'] = $user_id;

        $user = User::find($input['user']['id']);

        if(!array_key_exists('activated', $input['user']))$input['user']['activated']='0';
        if(!array_key_exists('super_admin', $input['user']))$input['user']['super_admin']='0';


        if($user == null) {
            $user = UserController::getCreateUser($input['user']);
            Session::flash('success','User Created Successfully');
        }
        else {
            Session::flash('success','User Updated Successfully');
            $user->fill($input['user']);
            $user->save();

            // Okay all validation passed, we change the password
            if($input['user']['password']!=''){
                try {
                    $user->password = Hash::make($input['user']['password']);
                    $user->save();
                    Session::flash('success','Password Updated');
                } catch (Exception $e) {
                    Session::flash('error','Password Update Failed');
                }
            }

        }

        return Redirect::action('UserController@getManageUsers');
    }

    public function getUnDeleteUser($id){
        $user = User::withTrashed()->find($id);
        if($user!=null){
            $user->restore();
            Session::flash('success','User UnDeleted Successfully');
        }
        return Redirect::action('UserController@getManageUsers');
    }

    public function getDeleteUser($id){
        $user = User::find($id);
        if($user!=null){
            $user->delete();
        }
        Session::flash('success','User Soft Deleted Successfully');
        return Redirect::action('UserController@getManageUsers');
    }



    public function getCreateUser($user_info='') {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
        try
        {
            $input = Input::all();
            if($user_info == '')$user_info = $input['user'];
            //dd('test');
            // Create the user
            $user = Sentry::createUser(array(
                'email'     => $user_info['email'],
                'password'  => $user_info['password'],
                'first_name'  => $user_info['first_name'],
                'last_name'  => $user_info['last_name'],
                'role'  => 'admin',
                'activated' => true,
            ));
            // Find the group using the group id
            $adminGroup = Sentry::findGroupById(1);
            // Assign the group to the user
            $user->addGroup($adminGroup);

            $input = [
                'business_name' => $user_info['first_name'].' '.$user_info['last_name'],
                'email' => $user_info['email'],
                'plan_id' => 1,
                'admin_provider' => '1',
                'user_id' => $user->id,
                'provider_status'  => '0'
            ];


            // dd($input);
            //$new_provider = DB::table('providers')->insert($input);

            $provider = new FProvider();
            $provider->fill($input);
            $provider->save();

            $zips = new ProviderZip();
            $zips->fill( Array('zip'=>'97520', 'provider_id'=>$provider->id) );
            $zips->save();

            $pricing = new ProviderPricingOptions();
            $pricing->fill( Array('provider_id'=>$provider->id) );
            $pricing->save();
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
        return $user;
    }


}