<?php

class ClientController extends BaseController {

	protected $layout = 'layouts.client';
	
	public function getLogin()
	{
		return View::make('clients.login');
	}
	public function getSteps($goToStep=0, $jsonReturn=false, $client=null)
	{
            
            if($client == null){
                if(Session::get('client_id')!='') {
                    //dd(Session::get('client_id'));
                    $client = Client::where('id',Session::get('client_id'))->first();
                    $client = ClientController::fillOutClientTables($client);
                }
                elseif(Sentry::getUser()){
                    //echo 'found client in sentry, userid:'.Sentry::getUser()->id;
                    $client = Client::where('user_id',Sentry::getUser()->id)->first();
                    $client = ClientController::fillOutClientTables($client);
                }
                
                if($client == null){
                    //echo 'client not pulled form sentry or session';
                    $client = New Client();
                }
            }
            
            if($client->DeceasedFamilyInfo == null)$client->DeceasedFamilyInfo = New DeceasedFamilyInfo();
            if($client->DeceasedInfo == null)$client->DeceasedInfo = New DeceasedInfo();
            if($client->CremainsInfo == null)$client->CremainsInfo = New CremainsInfo();
            if($client->DeceasedInfoPresentLoc == null)$client->DeceasedInfoPresentLoc = New DeceasedInfoPresentLoc();
            if($client->User == null)$client->User = New User();
            
            
            //echo 'SESSION<br />';
            //print_r(Session::all());
            
            //echo '<br /><br />SENTRY<br />';
            //print_r(Sentry::getUser($client->user_id));
            
            //echo '<Br /><Br /> client: <pre>'; 
            //print_r($client); echo '</pre><Br /><br />';
            
            
            $steps_r = Step::whereRaw("status='1' and display_in_menu='1'")->get();
           
            
            $states = DB::table('state')->distinct()->get();
            if(Input::get('provider_id')){
                $provider_id = Input::get('provider_id');
                $provider_id = str_replace('provider-','',$provider_id);
                $provider = ClientController::updateProvider($provider_id);
                    #elseif(!is_object(Session::get('provider')))$provider = ClientController::updateProvider(1); //default the provider to OCCS
            }
            elseif($client->FProvider!=null){
                $provider = ClientController::updateProvider($client->FProvider->id); 
            }
            //REFRESHING SO PROVIDER INFO ISN'T CACHED
            elseif(is_object(Session::get('provider'))) {
                $provider = Session::get('provider');
                $provider = ClientController::updateProvider($provider->id);
            }
            else $provider = ClientController::updateProvider(1); 
            
                //echo '<br>INPUTS:<br />'; print_r(Input::get());  
                //echo '<br>$provider:<br />'; print_r($provider);  
          
            
            if($goToStep != 0)Session::put('step', $goToStep);
            elseif(Input::get('submit')=="submit"){
                
                $current_step = Step::where('step_number', '=', Input::get('step',1))->firstOrFail();
                Session::put('step', $current_step->next_step_number);
            } 
            else Session::put('step', Input::get('step',1));
            
            $client->sale_summary_r = ClientController::getSaleTotals($client, $provider);
            //if(Session::get('step')==9)$client->sale_summary_r = ClientController::getSaleTotals($client, $provider);
            //else $client->sale_summary_r = Array();
            //dd($sale_summary_r);
            
            /* IF WE'RE ACTUALLY IN ADMINSITRATION THEN SHOW ALL STEPS AT ONCE */
            if(Sentry::getUser()){
                $group = json_decode(Sentry::getUser()->getGroups());
                if( $group[0]->name == 'Provider')Session::put('inAdminGroup','Provider');
                elseif( $group[0]->name == 'Admin')Session::put('inAdminGroup','Admin');
                else Session::put('inAdminGroup','');
            }
            else Session::put('inAdminGroup','');
            
            if($jsonReturn)return Response::json(Input::get());
            else return View::make('clients.steps',['states'=>$states, 'client'=>$client, 'steps_r'=>$steps_r, 'provider'=>$provider]);
            
	}
        /*
        public function saveClientInfo($input){
            
            // IF LOGGED IN THEN SAVE DATA TO THE ACCOUNT AS WELL AS SESSION
            if(Sentry::getUser())$client = Client::where('user_id',Sentry::getUser()->id)->first();
            
            if(isarray($input))
            foreach($input as $key=>$value){
                Session::put($key, $value);
                if($client != null)$client->$$key
                
            } 
        }*/
        
        public function updateProvider($provider_id='', $client=''){
            $provider = DB::table('providers')->where('id', $provider_id)->first();
            $provider->pricing_options = DB::table('provider_pricing_options')->where('provider_id', $provider_id)->first();
           
            if(is_object($client)){
                $client_provider = DB::table('clients_providers')->where('client_id', $client->id)->first();
                if(count($client_provider)>0){
                    DB::table('clients_providers')->where('client_id', $client->id)->update(array('provider_id'=>$provider_id));
                }
                else DB::table('clients_providers')->insert(array('provider_id'=>$provider_id, 'client_id'=>$client->id));
            }
            Session::put('provider', $provider);
            Session::put('provider_pricing_options', $provider->pricing_options);
            
            
            return $provider;
        }
        
	public function getSaveZip(){ 
            Session::put('zip',Input::get('set_zip'));
            $provider_zip = DB::table('provider_zips')->where('zip', Input::get('set_zip'))->first();
            
            if(count($provider_zip)>0)$provider_id = $provider_zip->provider_id;
            else $provider_id = 1;
            
            ClientController::updateProvider($provider_id);
            //print_r(Session::all());
            
            return ClientController::getSteps(1); 
        }
        
        public function getSteps1(){ return ClientController::getSteps(1); }
        public function getSteps2(){ return ClientController::getSteps(2); }
        public function getSteps3(){ return ClientController::getSteps(3); }
        public function getSteps4(){ return ClientController::getSteps(4); }
        public function getSteps5(){ return ClientController::getSteps(5); }
        public function getSteps6(){ return ClientController::getSteps(6); }
        public function getSteps7(){ return ClientController::getSteps(7); }
        public function getSteps8(){ return ClientController::getSteps(8); }
        public function getSteps9(){ return ClientController::getSteps(9); }
        public function getSteps10(){ return ClientController::getSteps(10); }
        public function getSteps11(){ return ClientController::getSteps(11); }
        public function getSteps12(){ return ClientController::getSteps(12); }
        public function getSteps13(){ return ClientController::getSteps(13); }
        public function getSteps14(){ return ClientController::getSteps(14); }
        public function getSteps15(){ return ClientController::getSteps(15); }
        
        public function getStepsAsClient(){ 
            
            return ClientController::getSteps(1,false); 
        }
        
        
	public function postSteps2()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('deceased_info'))){
                $input['deceased_info'] = Input::get('deceased_info');
                if(!array_key_exists('medical_donation',$input['deceased_info']))$input['deceased_info']['medical_donation']=0;
                $client->DeceasedInfo->fill($input['deceased_info']);
                $client->DeceasedInfo->save(); 
            }
            
            if(is_array(Input::get('cremains_info'))){
                $input['cremains_info'] = Input::get('cremains_info');
                
                $provider = Session::get('provider');
                if($provider->pricing_options->custom1_included=='1' && $input['cremains_info']['package_plan']=='2')$input['cremains_info']['custom1'] = '0';
                if($provider->pricing_options->custom2_included=='1' && $input['cremains_info']['package_plan']=='2')$input['cremains_info']['custom2'] = '0';
                if($provider->pricing_options->custom3_included=='1' && $input['cremains_info']['package_plan']=='2')$input['cremains_info']['custom3'] = '0';
                
                if($provider->pricing_options->custom1_included=='2' && $input['cremains_info']['package_plan']=='1')$input['cremains_info']['custom1'] = '0';
                if($provider->pricing_options->custom2_included=='2' && $input['cremains_info']['package_plan']=='1')$input['cremains_info']['custom2'] = '0';
                if($provider->pricing_options->custom3_included=='2' && $input['cremains_info']['package_plan']=='1')$input['cremains_info']['custom3'] = '0';
                
               
                    
                $client->CremainsInfo->fill($input['cremains_info']);
                $client->CremainsInfo->save(); 
            }
            
            //$rules = ['zip'=>'required'] ;
            // Let's validate
            //$v = Validator::make($input,$rules);
            //if($v->fails())return Redirect::back()->withErrors($v);

            //Session::put('deceased_info[cremation_reason]', $input['deceased_info[cremation_reason]']);
           
            //SAVE A STORED PROVIDER THAT MATCHED A ZIP
            
            if(Input::get('provider_id')!='')ClientController::updateProvider(Input::get('provider_id'), $client);
            
            
            return ClientController::getSteps(0, false, $client);	
	}
	public function postSteps3()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('deceased_info'))){
                $input['deceased_info'] = Input::get('deceased_info');
                $client->DeceasedInfo->fill($input['deceased_info']);
                $client->DeceasedInfo->save(); 
            }
            return ClientController::getSteps();	            
        }
	public function postSteps4()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('deceased_info'))){
                $input['deceased_info'] = Input::get('deceased_info');
                $client->DeceasedInfo->fill($input['deceased_info']);
                $client->DeceasedInfo->save(); 
            }
            if(is_array(Input::get('deceased_family_info'))){
                $input['deceased_family_info'] = Input::get('deceased_family_info');
                $client->DeceasedFamilyInfo->fill($input['deceased_family_info']);
                $client->DeceasedFamilyInfo->save(); 
            }
            
            return ClientController::getSteps();	
        }
	public function postSteps5()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('deceased_info_present_loc'))){
                $input['deceased_info_present_loc'] = Input::get('deceased_info_present_loc');
                $client->DeceasedInfoPresentLoc->fill($input['deceased_info_present_loc']);
                $client->DeceasedInfoPresentLoc->save(); 
            }
            return ClientController::getSteps();	
        }
	public function postSteps6()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('client'))){
                $input['client'] = Input::get('client');
                $client->fill($input['client']);
                DB::table('clients')->where('id', $client->id)->update($input['client']);
            }
            return ClientController::getSteps();	   
        }
	public function postSteps7()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('client'))){
                $input['client'] = Input::get('client');
                $client->fill($input['client']);
                DB::table('clients')->where('id', $client->id)->update($input['client']);
            }
            if(is_array(Input::get('cremains_info'))){
                $input['cremains_info'] = Input::get('cremains_info');
                $client->CremainsInfo->fill($input['cremains_info']);
                $client->CremainsInfo->save(); 
            }
            
            return ClientController::getSteps();	   
        }
	public function postSteps8()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('cremains_info'))){
                $input['cremains_info'] = Input::get('cremains_info');
                $client->CremainsInfo->fill($input['cremains_info']);
                $client->CremainsInfo->save(); 
            }
            
            return ClientController::getSteps();	   
        }
	public function postSteps9()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('cremains_info'))){
                $input['cremains_info'] = Input::get('cremains_info');
                $client->CremainsInfo->fill($input['cremains_info']);
                $client->CremainsInfo->save(); 
            }
            return ClientController::getSteps();	   
        }
	public function postSteps10()
	{
            $client = ClientController::registerUser();
            $input['client']['agreed_to_ftc'] = 1;
            DB::table('clients')->where('id', $client->id)->update($input['client']);
            return ClientController::getSteps();	  
        }
	public function postSteps11()
	{
           
            if(is_array(Input::get('client'))){
                $input['client'] = Input::get('client');
                if(!array_key_exists('confirmed_legal_auth',$input['client']))$input['client']['confirmed_legal_auth'] = null;
                if(!array_key_exists('confirmed_correct_info',$input['client']))$input['client']['confirmed_correct_info'] = null;
                if(!array_key_exists('confirmed_legal_auth_name',$input['client']))$input['client']['confirmed_legal_auth_name'] = null;
                if(!array_key_exists('confirmed_correct_info_initial',$input['client']))$input['client']['confirmed_correct_info_initial'] = null;
                
                $validate_inputs = ['Confirmed Legal Authorization Checkbox'=>$input['client']['confirmed_legal_auth'],
                                    'Confirmed Correct Information Checkbox'=>$input['client']['confirmed_correct_info'],
                                    'Your Name'=>$input['client']['confirmed_legal_auth_name'],
                                    'Your Initials'=>$input['client']['confirmed_correct_info_initial']];
                $rules = ['Confirmed Legal Authorization Checkbox'=>'required','Confirmed Correct Information Checkbox'=>'required',
                            'Your Name'=>'required','Your Initials'=>'required'];
                $v = Validator::make($validate_inputs,$rules);
                if( $v->fails() ) return Redirect::back()->withErrors($v);
            } else return Redirect::back();
            
            $client = ClientController::registerUser();
            if(is_array(Input::get('client'))){
                $input['client'] = Input::get('client');
                $client->fill($input['client']);
                DB::table('clients')->where('id', $client->id)->update($input['client']);
            }
            return ClientController::getSteps();	  
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
			return Redirect::action('AdminController@getProviders');
		}else{
			return Redirect::action('ProviderController@getCustomers');
		}
	}

        // MAKE SURE ALL CLIENT RELATING TABLES GET POPULATED
        public function fillOutClientTables($client){
            
            //if($client == null)$client = ClientController::registerUser();
            $client_details_input = Array('client_id' => $client->id); 
            
            $client->DeceasedFamilyInfo = DeceasedFamilyInfo::where('client_id', $client->id)->first();
            if($client->DeceasedFamilyInfo == null){
                $client->DeceasedFamilyInfo = new DeceasedFamilyInfo();  
                $client->DeceasedFamilyInfo->fill($client_details_input);
                $client->DeceasedFamilyInfo->save(); 
            }      
            
            $client->DeceasedInfo = DeceasedInfo::where('client_id', $client->id)->first();
            if($client->DeceasedInfo == null){
                $client->DeceasedInfo = new DeceasedInfo();  
                $client->DeceasedInfo->fill($client_details_input);
                $client->DeceasedInfo->save(); 
            }      
            
            $client->CremainsInfo = CremainsInfo::where('client_id', $client->id)->first();
            if($client->CremainsInfo == null){
                $client->CremainsInfo = new CremainsInfo();  
                $client->CremainsInfo->fill($client_details_input);
                $client->CremainsInfo->save(); 
            }      
            
            $client->DeceasedInfoPresentLoc = DeceasedInfoPresentLoc::where('client_id', $client->id)->first();
            if($client->DeceasedInfoPresentLoc == null){
                $client->DeceasedInfoPresentLoc = new DeceasedInfoPresentLoc();  
                $client->DeceasedInfoPresentLoc->fill($client_details_input);
                $client->DeceasedInfoPresentLoc->save(); 
            }
            
            $provider_id = DB::table('clients_providers')->select(DB::raw('provider_id'))->where('client_id','=', $client->id)->first();
            //dd($provider_id->provider_id);
            if($provider_id!=null)$client->FProvider = FProvider::find($provider_id->provider_id);
            if($client->FProvider == null){
                $client->FProvider = FProvider::find(1);  
            }
            
            $client->User = User::where('id', $client->user_id)->first();
            if($client->User == null){
                $client->User = new User();
            }
            
            return $client;
        }
        
        
        //CREATE A NEW CLIENT ACCOUNT OR TEMP CLIENT ACCOUNT
        public function registerUser(){
	
            
            $client = null;
            if(Session::get('client_id')!=""){
                $client = Client::where('id',Session::get('client_id'))->first();
                $client = ClientController::fillOutClientTables($client);
                //echo "fethced existing client from session"; 
                //dd($client);
                
            }
            elseif(Sentry::getUser()){
                $client = Client::where('user_id',Sentry::getUser()->id)->first();
                $client = ClientController::fillOutClientTables($client);
                //echo "fetched existing user sentry";
            }   
            elseif(Input::get('client_id')!=""){
                $client = Client::where('id',Input::get('client_id'))->first();
                $client = ClientController::fillOutClientTables($client);
                
                try
                {
                    // Find the user using the user id
                    $user = Sentry::findUserById($client->user_id);

                    // Log the user in
                    Sentry::login($user, true);
                }
                catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
                {
                    echo 'Login field is required.';
                }
                catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
                {
                    echo 'User not found.';
                }
                catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
                {
                    echo 'User not activated.';
                }

                // Following is only needed if throttle is enabled
                catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
                {
                    $time = $throttle->getSuspensionTime();

                    echo "User is suspended for [$time] minutes.";
                }
                catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
                {
                    echo 'User is banned.';
                }
                
                //echo "fethced existing client from input";    
                
            }
            else{
               
                $client_input = Array(
                        'first_name' => "unregistered",
                        'last_name' => "unregistered"
                    );
                    
 
                $client = new Client();  
                $client->fill($client_input);     
                $client->save();     
                //dd( "creatd new user:".$client->id);
                
                $user = Sentry::createUser(array(
                        'email'     => 'unregistered'.$client->id.'@user.com',
                        'password'  => 'unregistered', 
                        'role'  => 'client',
                        'activated' => true
                    ));
                
                $client->user_id = $user->id;  
                $client->update();
                
                $client->User = User::where('id', $client->user_id)->first();
                Session::put('client_id', $client->id);
                
                
                 // Find the group using the group id
                $clientGroup = Sentry::findGroupById(3);
                $user->addGroup($clientGroup);
                
                //Auth::attempt(array('email' => 'unregistered'.$client->id.'@user.com', 'password' => 'unregistered'));
                Sentry::login($user, true);
                
                $client = ClientController::fillOutClientTables($client);
                
                
                //echo "created client in registerUser";    
                   
                
            }
            
            
            return $client;
	}
        
	public function getLogout(){
		Sentry::logout();
                Session::flush();
                return Redirect::to('/clients/steps1');
	}
        
	public function getCreateUser() {
		try
		{
		    // Create the user
		    $user = Sentry::createUser(array(
		        'email'     => 'fikri.desertlion@gmail.com',
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
        
        public function getLoginJson(){
            
            $credentials = ['email'=>Input::get('login_email'),'password'=>Input::get('login_password')];
            
            try
            {
                // Authenticate the user
                $user = Sentry::authenticate($credentials, false);
            }
            catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
            {
                $message['fail'] = 'Login field is required.';
            }
            catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
            {
                $message['fail'] = 'Password field is required.';
            }
            catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
            {
                $message['fail'] = 'Wrong password, try again.';
            }
            catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
            {
                $message['fail'] = 'User was not found.';
            }
            catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
            {
                $message['fail'] = 'User is not activated.';
            }

            // The following is only required if the throttling is enabled
            catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
            {
                $message['fail'] = 'User is suspended.';
            }
            catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
            {
                $message['fail'] = 'User is banned.';
            }
            $message['success']='';
            
            //$client = Client::where('user_id',Sentry::getUser()->id)->first();
            return Response::json($message);      
        }
        
        
        public function getCreationAccountJson(){
            
            
            if(DB::table('users')->where('email', Input::get('register_email'))->first())return Response::json(array('fail'=>'You Already Registered With That Email'));
            if(Input::get('register_phone')=='' || Input::get('register_email')=='' || Input::get('register_name')=='' || Input::get('register_password')=='')
                return Response::json(array('fail'=>'Please Enter All Information'));
            $first_space = strpos(Input::get('register_name'),' ');
            if($first_space==0 || $first_space=='')return Response::json(array('fail'=>'Please Enter A First and Last Name'));
                
            $client = ClientController::registerUser();
            $user = Sentry::findUserById($client->user_id);
            
            
            $input['client']['first_name'] = substr(Input::get('register_name'), 0, $first_space);
            $input['client']['last_name'] = substr(Input::get('register_name'), $first_space+1, strlen(Input::get('register_name')) );
            $input['client']['phone'] = Input::get('register_phone');
            
            $input['user']['email'] = Input::get('register_email'); 
            
            DB::table('clients')->where('id', $client->id)->update($input['client']);
            DB::table('users')->where('id', $client->user_id)->update($input['user']);
            if ($user->attemptResetPassword($user->getResetPasswordCode(), Input::get('register_password')));                
            
            $client = Client::where('user_id',Sentry::getUser()->id)->first();
            return Response::json($client);
        }
        
        
        // FIND THE TOTAL COST TO END USER FOR THIS REGISTRATION
        public function getSaleTotals($client, $provider){
            $TOTAL_PRICE = 0;
            //dd();
            
            
            if($client->CremainsInfo->package_plan == "1") $saleSummary['report']['package_plan']['price'] = $provider->pricing_options->basic_cremation;
            elseif($client->CremainsInfo->package_plan == "2") $saleSummary['report']['package_plan']['price'] = $provider->pricing_options->premium_cremation;
            else $saleSummary['report']['package_plan']['price'] = '';
            $saleSummary['report']['package_plan']['desc'] = "Cremation Plan: " . ($client->CremainsInfo->package_plan==1?'Package A':'Package B');
            $TOTAL_PRICE += $saleSummary['report']['package_plan']['price'];
           

            if($client->DeceasedInfo->weight == "weight_lt_250") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_lt_250;
            elseif($client->DeceasedInfo->weight == "weight_lt_300") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_lt_300;
            elseif($client->DeceasedInfo->weight == "weight_lt_350") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_lt_350;
            elseif($client->DeceasedInfo->weight == "weight_gt_350") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_gt_350;
            else $saleSummary['report']['weight']['price'] = '';
            $saleSummary['report']['weight']['desc'] = "Weight: " . $client->DeceasedInfo->weight;
            $TOTAL_PRICE += $saleSummary['report']['weight']['price'];
            
            

            if($client->DeceasedInfo->has_pace_maker == "1") $saleSummary['report']['has_pace_maker']['price'] = $provider->pricing_options->pacemaker;
            else $saleSummary['report']['has_pace_maker']['price'] = ''; 
            $saleSummary['report']['has_pace_maker']['desc'] = "Has Pacemaker: " . ($client->DeceasedInfo->has_pace_maker?'Yes':'No');
            $TOTAL_PRICE += $saleSummary['report']['has_pace_maker']['price'];
            
            /*
            if($client->CremainsInfo->cremain_plan == "scatter_on_land") $saleSummary['report']['cremain_plan']['price'] = $provider->pricing_options->scatter_on_land;
            elseif($client->CremainsInfo->cremain_plan == "scatter_at_sea") $saleSummary['report']['cremain_plan']['price'] = $provider->pricing_options->scatter_at_sea;
            else $saleSummary['report']['cremain_plan']['price'] = '';
            $saleSummary['report']['cremain_plan']['desc'] = "Plan For Cremation Remains: " . $client->CremainsInfo->cremain_plan;
            $TOTAL_PRICE += $saleSummary['report']['cremain_plan']['price'];
            */
            
            if($client->CremainsInfo->cremation_shipping_plan == "scatter_on_land") $saleSummary['report']['cremation_shipping_plan']['price'] = $provider->pricing_options->scatter_on_land;
            elseif($client->CremainsInfo->cremation_shipping_plan == "scatter_at_sea") $saleSummary['report']['cremation_shipping_plan']['price'] = $provider->pricing_options->scatter_at_sea;
            elseif($client->CremainsInfo->cremation_shipping_plan == "ship_to_you") $saleSummary['report']['cremation_shipping_plan']['price'] = $provider->pricing_options->ship_to_you;
            else $saleSummary['report']['cremation_shipping_plan']['price'] = '';
            $saleSummary['report']['cremation_shipping_plan']['desc'] = "Plan For Cremation Remains: " . $client->CremainsInfo->cremation_shipping_plan;
            $TOTAL_PRICE += $saleSummary['report']['cremation_shipping_plan']['price'];
            

            if($client->CremainsInfo->cert_plan == "deathcert_wurn") {$saleSummary['report']['cert_plan']['price'] = $provider->pricing_options->deathcert_wurn; $desc = "Mail certificate(s) with urn";}
            elseif($client->CremainsInfo->cert_plan == "deathcert_cep") {$saleSummary['report']['cert_plan']['price'] = $provider->pricing_options->deathcert_cep; $desc = "Mail certificate(s) seperately";}
            elseif($client->CremainsInfo->cert_plan == "deathcert_pickup") {$saleSummary['report']['cert_plan']['price'] = $provider->pricing_options->deathcert_pickup; $desc = "Pick up certificate(s) at our office";}
            else { $saleSummary['report']['cert_plan']['price'] = $desc = ''; }            
            $saleSummary['report']['cert_plan']['desc'] = "Certificate Shipping: " . $desc;
            
            $TOTAL_PRICE += $saleSummary['report']['cert_plan']['price'];
            //echo '<pre>';print_r($client->CremainsInfo); print_r($provider->pricing_options); echo '</pre>'; die();
            
            //dd($cremain_info);
            $saleSummary['report']['number_of_certs']['desc'] = 'Number of Certificates ($'.$provider->pricing_options->deathcert_each." each): " . $client->CremainsInfo->number_of_certs;
            $saleSummary['report']['number_of_certs']['price'] = number_format((float)$provider->pricing_options->deathcert_each * ($client->CremainsInfo->number_of_certs?$client->CremainsInfo->number_of_certs:0), 2, '.', '');
            $TOTAL_PRICE += $saleSummary['report']['number_of_certs']['price'];
            
            
            $saleSummary['report']['filing_fee']['desc'] = "Filing Fee: " . $provider->pricing_options->filing_fee;
            $saleSummary['report']['filing_fee']['price'] = $provider->pricing_options->filing_fee;
            $TOTAL_PRICE += $saleSummary['report']['filing_fee']['price'];

            if($provider->pricing_options->custom1_text != "" and ($client->CremainsInfo->custom1==1) and ClientController::customerCustomPlanOptions($client->CremainsInfo->package_plan, $provider->pricing_options->custom1_included)) {
                $saleSummary['report']['custom1']['desc'] = $provider->pricing_options->custom1_text;
                $saleSummary['report']['custom1']['price'] = $provider->pricing_options->custom1;
                $TOTAL_PRICE += $saleSummary['report']['custom1']['price'];
            }
            if($provider->pricing_options->custom2_text != "" and ($client->CremainsInfo->custom2==1) and ClientController::customerCustomPlanOptions($client->CremainsInfo->package_plan, $provider->pricing_options->custom2_included)) {
                $saleSummary['report']['custom2']['desc'] = $provider->pricing_options->custom2_text;
                $saleSummary['report']['custom2']['price'] = $provider->pricing_options->custom2;
                $TOTAL_PRICE += $saleSummary['report']['custom2']['price'];
            }
            if($provider->pricing_options->custom3_text != "" and ($client->CremainsInfo->custom3==1) and ClientController::customerCustomPlanOptions($client->CremainsInfo->package_plan, $provider->pricing_options->custom3_included)) {
                $saleSummary['report']['custom3']['desc'] = $provider->pricing_options->custom3_text;
                $saleSummary['report']['custom3']['price'] = $provider->pricing_options->custom3;
                $TOTAL_PRICE += $saleSummary['report']['custom3']['price'];
            }


            $saleSummary['total'] = number_format((float)$TOTAL_PRICE, 2, '.', '');
            
            return $saleSummary;
        }
        
        //DECIDE IF WE SHOULD COUNT THIS CUSTOM OPTION TOWARDS THE SALE
	function customerCustomPlanOptions($package_plan, $custom_included){			
            if($package_plan == $custom_included)return true;
            elseif($custom_included == 3)return true;
            else return false;
	}   
        
        
     
    /*
     * PDF FORM BUILDER USING DOMPDF
     * 
     * 
     */    
    function getCustomerDocuments($client_id='', $provider_id=''){
        if($client_id=='')$client_id = Session::get('client_id');
        if($provider_id=='')$provider_id = Session::get('provider_id');
        
        $client = Client::find($client_id);
        $client = ClientController::fillOutClientTables($client);
        $provider = FProvider::find($provider_id);
        
        
        $html = $provider->customer_form_1;
        $options = Array(
            'client_status',
            'client_first_name',
            'client_middle_name',
            'client_last_name',
            'client_legal_name',
            'client_initials',
            'client_relationship',
            'client_apt',
            'client_address',
            'client_city',
            'client_state',
            'client_zip',
            'client_phone',
            'client_agreed_to_ftc',
            'client_confirmed_legal_auth',
            'client_confirmed_legal_auth_name',
            'client_confirmed_correct_info',
            'client_confirmed_correct_info_initial',
            'client_feedback_questions_text',
            'client_feedback_suggestions',
            'client_is_junk',
            'client_purchase_option',
            'client_majority_count',
            
            'provider_business_name',
            'provider_address',
            'provider_city',
            'provider_state',
            'provider_zip',
            'provider_email',
            'provider_website',
            'provider_phone',
            'provider_fax',
            'provider_provider_radius',
            'provider_provider_status',
            'provider_logo_url',
            
            'DeceasedFamilyInfo_srdp_first_name',
            'DeceasedFamilyInfo_srdp_middle_name',
            'DeceasedFamilyInfo_srdp_last_name',
            'DeceasedFamilyInfo_fthr_first_name',
            'DeceasedFamilyInfo_fthr_middle_name',
            'DeceasedFamilyInfo_fthr_last_name',
            'DeceasedFamilyInfo_fthr_birth_city',
            'DeceasedFamilyInfo_mthr_first_name',
            'DeceasedFamilyInfo_mthr_middle_name',
            'DeceasedFamilyInfo_mthr_last_name',            
            'DeceasedFamilyInfo_mthr_birth_city',
            
            'DeceasedInfo_first_name',
            'DeceasedInfo_middle_name',
            'DeceasedInfo_last_name',
            'DeceasedInfo_aka',
            'DeceasedInfo_location',
            'DeceasedInfo_type_of_location',
            'DeceasedInfo_address',
            'DeceasedInfo_apt',
            'DeceasedInfo_city',
            'DeceasedInfo_state',
            'DeceasedInfo_zip',
            'DeceasedInfo_county',
            'DeceasedInfo_phone',
            'DeceasedInfo_dob',
            'DeceasedInfo_gender',
            'DeceasedInfo_race',
            'DeceasedInfo_weight',
            'DeceasedInfo_marriage_status',
            'DeceasedInfo_birth_city_state',
            'DeceasedInfo_yrs_in_county',
            'DeceasedInfo_schooling_level',
            'DeceasedInfo_military',
            'DeceasedInfo_occupation',
            'DeceasedInfo_business_type',
            'DeceasedInfo_years_in_occupation',
            'DeceasedInfo_has_pace_maker',
            'DeceasedInfo_cremation_reason',
            'DeceasedInfo_medical_donation',
            'DeceasedInfo_ssn',
            
            'CremainsInfo_number_of_certs',
            'CremainsInfo_cert_plan',
            'CremainsInfo_cremain_plan',
            'CremainsInfo_cremation_shipping_plan',
            'CremainsInfo_keeper_of_cremains',
            'CremainsInfo_shipto_first_name',
            'CremainsInfo_shipto_middle_name',
            'CremainsInfo_shipto_last_name',
            'CremainsInfo_shipto_address',
            'CremainsInfo_shipto_apt',
            'CremainsInfo_shipto_city',
            'CremainsInfo_shipto_state',
            'CremainsInfo_shipto_zip',
            'CremainsInfo_shipto_phone',
            'CremainsInfo_shipto_email',
            'CremainsInfo_package_plan',
            'CremainsInfo_custom1',
            'CremainsInfo_custom2',
            'CremainsInfo_custom3',
            
            'DeceasedInfoPresentLoc_location',
            'DeceasedInfoPresentLoc_type_of_location',
            'DeceasedInfoPresentLoc_address',
            'DeceasedInfoPresentLoc_apt',
            'DeceasedInfoPresentLoc_city',
            'DeceasedInfoPresentLoc_state',
            'DeceasedInfoPresentLoc_zip'            
        );
      
          
        foreach($options as $val){
           $key = substr($val, strpos($val, '_')+1, strlen($val));
           $object = substr($val, 0, strpos($val, '_'));
           
           switch($object){
               case 'client': $class = $client; break;
               case 'provider': $class = $provider; break;
               case 'DeceasedFamilyInfo': $class = $client->DeceasedFamilyInfo; break;
               case 'DeceasedInfo': $class = $client->DeceasedInfo; break;
               case 'CremainsInfo': $class = $client->CremainsInfo; break;
               case 'DeceasedInfoPresentLoc': $class = $client->DeceasedInfoPresentLoc; break;
               default:break;
           }
           
           $html = str_replace('{{'.$val.'}}', $class->$key, $html); 
           $html = str_replace('{{'.$val.'}}', '', $html); 
           
        }
        
        
        
        
        
        
        
        //dd();
        
        $pdf = App::make('dompdf');
        $pdf->loadHTML($html);
        return $pdf->stream();   

    }
    
        
}