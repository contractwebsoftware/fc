<?php

class ClientController extends BaseController {

	protected $layout = 'layouts.client';
	
	public function getLogin()
	{
		return View::make('clients.login');
	}
	public function getSteps($goToStep=0, $jsonReturn=false, $client=null)
	{
            //dd(Session::get('inAdminGroup'));
            if(Input::get('client_id')!=''){
                //echo 'found client in sentry, userid:'.Sentry::getUser()->id;
                $client = Client::where('id',Input::get('client_id'))->first();
                $client = ClientController::fillOutClientTables($client);
                Session::put('client_id',Input::get('client_id'));
            }
            elseif($client == null){
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
            if(strpos($goToStep, 'provider-')!==false or strpos($goToStep, 'provider=')!==false){

                $provider_id = str_replace('provider-','',$goToStep);
                $provider_id = str_replace('provider=','',$goToStep);
                //dd($provider_id);

                $provider = ClientController::updateProvider($provider_id);
            }
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
          
            
           
            /*
             * USE THE PROVIDERS PRODUCT PRICES IF THEY HAVE ANY 
             */
                $products = ProviderProducts::where('provider_id',$provider->id)->get();
                if($products == null || count($products)<1)$products = Products::get();

                $client_product = ClientProducts::where('provider_id',$provider->id)->where('client_id',$client->id)->first();
                if($client_product == null || count($client_product)<1){
                    $client_product = new ClientProducts();
                    $client_product->product_id = 1;
                }
            /* END PROVIDER PRODUCTS */
            
            
            if($goToStep != 0)Session::put('step', $goToStep);
            elseif(Input::get('submit')=="submit" || Input::get('submit')=="save"){
                
                $current_step = Step::where('step_number', '=', Input::get('step',1))->firstOrFail();
                Session::put('step', $current_step->next_step_number);
            } 
            else Session::put('step', Input::get('step',1));
            if(Session::get('step')>11) Session::put('step',11);

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
            else return View::make('clients.steps',['states'=>$states, 'client'=>$client, 'steps_r'=>$steps_r, 'provider'=>$provider, 'products'=>$products,'client_product'=>$client_product]);
            
	}

    public function getProviderLocation(){

        $states = DB::table('state')->distinct()->get();
        $html = '<html><body style="background-color: #FFF;background-image: none;padding-top:0px;">
                <div id="select_location" class="row pull-left" style="margin-left:5px;margin-right:0px;">

                 <table ><tr><td align=left style="width: 265px;padding-left: 30px;">
                    <b>Select State</b>:<br />
                    <select id="state" name="state">
                        <option value="">--</option>';

                        foreach($states as $key=>$row){
                            $html .= '<option value="'.$row->name_shor.'">'.$row->name_long.'</option>';
                        }

        $html .= ' </select>
                    <br /><b>Select City</b>:<br />
                    <select id="city" name="city"><option value="">--</option></select>
                    <br /><b>Select Provider</b>:<br />
                    <select id="new_provider" name="new_provider"><option value="">--</option></select>
                    <br />
                    <button class="btn btn-primary" id="choose_provider">Select Provider</button>
                    <br style="float:none;clear:both;" /><Br />
                </td>
                <td align=left>
                    <iframe style="width:200px;height:290px;border:none;" src="http://www.forcremation.com/slides/v_welcome.swf" frameborder="0"></iframe>
               </td></tr></table>
            </div>
            <link rel="stylesheet" href="'. asset('packages/Bootflat/css/bootstrap.min.css') .'">
            <link rel="stylesheet" href="'. asset('css/client.css') .'">
            <!--[if IE]>
            <link rel="stylesheet" type="text/css" href="'. asset('css/ie-only.css') .'" />
            <![endif]-->
            <script src="'. asset('packages/Bootflat/js/jquery-1.11.1.min.js') .'"></script>
            <script src="'. asset('js/jquery.chained.remote.min.js') .'"></script>
            <script>

                $("#city").remoteChained("#state", "'.action('StepController@getCities').'");
                $("#new_provider").remoteChained("#city", "'.action('StepController@getProvidersByCity').'");
                $("#new_provider").on("change", function(){
                    $("#new_provider option:contains(\'funeralhome-\')").attr("disabled",true);
                    $("#new_provider option[value*=\'funeralhome-\']").attr("disabled", true );

                    var val = $(this).val();
                    if(val == "" || val.toLowerCase().indexOf("funeralhome-") >= 0){
                        $("#new_provider option:contains(\'provider-\')").attr("selected",true);
                        $("#new_provider option[value*=\'provider-\']").attr("selected", true );
                    }
                });

                $("#choose_provider").click(function(){
                    //window.top.location.href = "'.action('ClientController@getSteps1').'?provider_id="+$("#new_provider").val();
                    window.top.location.href = "http://www.forcremation.com?providerhome="+$("#new_provider").val();
                    return false;
                });

                </script>
            </body></html>';
            echo $html;
        die();
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
        
    public function updateProvider($provider_id='', $client='')
    {
        $provider = DB::table('providers')->where('id', $provider_id)->first();
        $provider->pricing_options = DB::table('provider_pricing_options')->where('provider_id', $provider_id)->first();

        $provider->ProviderFiles = ProviderFiles::where('provider_id', $provider->id)->where('file_type', 'pricing')->first();
        if ($provider->ProviderFiles == null) $provider->ProviderFiles = new ProviderFiles();
        $provider->ProviderPriceSheet = ProviderFiles::where('provider_id', $provider->id)->where('file_type', 'pricing')->first();
        $provider->provider_logo = ProviderFiles::where('provider_id', $provider->id)->where('file_type','like','provider_logo')->first();
        if ($provider->provider_logo == null)$provider->provider_logo = 'http://forcremation.com/providers/badges/logo.png?v=2';
        else $provider->provider_logo = 'http://provider.forcremation.com/provider_files/'.$provider->id.'/logo.png';


        if (is_object($client)) {
            $client_provider = DB::table('clients_providers')->where('client_id', $client->id)->first();
            if (count($client_provider) > 0) {
                DB::table('clients_providers')->where('client_id', $client->id)->update(array('provider_id' => $provider_id));
            } else DB::table('clients_providers')->insert(array('provider_id' => $provider_id, 'client_id' => $client->id));
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
            if(Session::get('client_id')==null || Session::get('client_id')=='')$plan_change = false;
            else $plan_change = true; 
                
            $client = ClientController::registerUser();
            if(is_array(Input::get('deceased_info'))){
                $input['deceased_info'] = Input::get('deceased_info');
                if(!array_key_exists('medical_donation',$input['deceased_info']))$input['deceased_info']['medical_donation']=0;
                $client->DeceasedInfo->fill($input['deceased_info']);
                $client->DeceasedInfo->save(); 
            }
            
            if(Input::get('provider_id')!='')ClientController::updateProvider(Input::get('provider_id'), $client);
            
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

            if($plan_change == true){
                if(is_object(Session::get('provider')))$mail_data['provider'] = Session::get('provider');
                else {
                    $provider_id = Session::get('provider_id')==''?1:Session::get('provider_id');
                    $mail_data['provider'] = FProvider::find($provider_id);
                }
                $mail_data['client'] = $client;
                $mail_data['type'] = 'plan_change';
                Mail::send('emails.provider-client-status', $mail_data, function($message) use($mail_data)
                {
                    $message->subject('A client has selected a new ForCremation package');
                    $message->to($mail_data['provider']->email);
                    Log::info('Emailed A client has selected a new ForCremation package to '.$mail_data['provider']->email);
                });


            }
            
            //$rules = ['zip'=>'required'] ;
            // Let's validate
            //$v = Validator::make($input,$rules);
            //if($v->fails())return Redirect::back()->withErrors($v);

            //Session::put('deceased_info[cremation_reason]', $input['deceased_info[cremation_reason]']);
           
            //SAVE A STORED PROVIDER THAT MATCHED A ZIP
            
            
            
            return ClientController::getSteps(0, false, $client);	
	}
	public function postSteps3()
	{
            $client = ClientController::registerUser();
            if(is_array(Input::get('deceased_info'))){
                $input['deceased_info'] = Input::get('deceased_info');

                if($input['deceased_info']['dob']!='')$input['deceased_info']['dob'] = date('Y-m-d', strtotime($input['deceased_info']['dob']));
                if($input['deceased_info']['dod']!='')$input['deceased_info']['dod'] = date('Y-m-d', strtotime($input['deceased_info']['dod']));
                //echo '<pre>';dd($input['deceased_info']);
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
            if($client->CremainsInfo->keeper_of_cremains == "" and $client->address=='')$new_client=true;
            else $new_client=false;
            
            
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
            
            
            if(is_array(Input::get('client_product'))){
                $client_product = Input::get('client_product');
                
                $cliend_product_r = ClientProducts::where('provider_id', Input::get('provider_id'))->where('client_id', Input::get('client_id'))->first();
                //dd(DB::getQueryLog());
                if($cliend_product_r == null){
                    $cliend_product_r = new ClientProducts();
                    $cliend_product_r->provider_id = Session::get('provider_id')==''?1:Session::get('provider_id');
                    $cliend_product_r->client_id = $client->id;
                }
                $cliend_product_r->product_id = $client_product['product_id'];
                $cliend_product_r->price = $client_product['price'][$client_product['product_id']];
                $cliend_product_r->note = $client_product['note'];
                $cliend_product_r->save();
                
            }
            
            
            if($new_client){
                if(is_object(Session::get('provider')))$mail_data['provider'] = Session::get('provider');
                else {
                    $provider_id = Session::get('provider_id')==''?1:Session::get('provider_id');
                    $mail_data['provider'] = FProvider::find($provider_id);
                }
                $mail_data['client'] = $client;
                $mail_data['type'] = 'started';
                Mail::send('emails.provider-client-status', $mail_data, function($message) use($mail_data)
                {
                    $message->subject('A new customer has started the ForCremation Registration');
                    $message->to($mail_data['provider']->email);
                    Log::info('A new customer has started the ForCremation Registration Emailed: '.$mail_data['provider']->email);
                });
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
                
                if(is_object(Session::get('provider')))$mail_data['provider'] = Session::get('provider');
                else {
                    $provider_id = Session::get('provider_id')==''?1:Session::get('provider_id');
                    $mail_data['provider'] = FProvider::find($provider_id);
                }
                $mail_data['client'] = $client;
                $mail_data['type'] = 'completed';
                //echo '<pre>';dd($mail_data);
                Mail::send('emails.provider-client-status', $mail_data, function($message) use($mail_data)
                {
                    //$message->from('us@example.com', 'Laravel');
                    $message->subject('New customer has completed ForCremation Registration Process');
                    $message->to($mail_data['provider']->email);
                    //dd($mail_data['provider']->email);
                    //$message->attach($pathToFile);
                    Log::info('New customer has completed ForCremation Registration Process Emailed: '.$mail_data['provider']->email);
                });

            }
            return ClientController::getSteps();	  
        }

    public function postUpdateEmail()
    {
        if(is_array(Input::get('client'))) {
            $input['client'] = Input::get('client');

            //$existing_client = Client::where('email', strtolower($input['client']['email']))->first();
            $existing_user = User::where('email', strtolower($input['client']['email']))->first();


                if($existing_user != null){
                    if(strtolower($input['client']['email']) != strtolower(Input::get('old_client_email') ))
                    Session::flash('error','A Client with that Email and Login already exists');
                }
                else {
                    $client = Client::where('id', strtolower($input['client']['id']))->first();
                    //$client->email = $input['client']['email'];
                    //$client->save();

                    $user = User::where('id', strtolower($client->user_id))->first();
                    if($user != null){
                        $user->email = $input['client']['email'];
                        $user->save();
                    }

                    Session::flash('success','Clients\'s Email and Login have been updated');

                }

            if(Input::get('password')!='' and (Input::get('password') == Input::get('confirm_password'))){
                $client = Client::where('id', strtolower($input['client']['id']))->first();
                $user = User::where('id', strtolower($client->user_id))->first();

                $user->password = Hash::make( Input::get('password') );
                $user->save();
                Session::flash('success','Login Updated');
            }

        }
        return Redirect::action('ClientController@getSteps');

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
            
            if($client == null || !is_object($client))return null;
                //$client = new Client();
           
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
            
            
            $client->ClientProducts = ClientProducts::where('client_id', $client->id)->where('provider_id',$client->FProvider->id)->first();
            if($client->ClientProducts == null){
                $client_product = ProviderProducts::where('provider_id',$client->FProvider->id)->where('product_id',1)->first();
                if($client_product == null) $client_product = Products::where('id',1)->first();            
           
                $client->ClientProducts = new ClientProducts();  
                $client->ClientProducts->provider_id = $client->FProvider->id;
                $client->ClientProducts->product_id = 1;
                $client->ClientProducts->price = $client_product->price;
                $client->ClientProducts->fill($client_details_input);
                $client->ClientProducts->save(); 
            }
            
                 
            $client->User = User::where('id', $client->user_id)->first();
            if($client->User == null){
                $client->User = new User();
            }
            
            return $client;
        }
        
        
        //CREATE A NEW CLIENT ACCOUNT OR TEMP CLIENT ACCOUNT
        public function registerUser(){
            
            $create_client = false;
            $client = null;
            //dd(Session::get('client_id'));
            if(Session::get('client_id')!=""){
                
                $client = Client::where('id',Session::get('client_id'))->first();
                
                if($client==null)$create_client = true;
                else $client = ClientController::fillOutClientTables($client);
                
                //echo "fethced existing client from session"; 
               
                
                
            }
            elseif(Sentry::getUser() && !Session::get('inAdminGroup')){
                $client = Client::where('user_id',Sentry::getUser()->id)->first();
                if($client==null)$create_client = true;
                else $client = ClientController::fillOutClientTables($client);
                //echo "fetched existing user sentry";
               
            }   
            elseif(Input::get('client_id')!=""){
                $client = Client::where('id',Input::get('client_id'))->first();
                if($client==null)$create_client = true;
                else {
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

                }
                
                //echo "fethced existing client from input"; 
                    
            }
            else $create_client = true;
            
            //if($client==null)$create_client = true;
            //elseif($client->id==null)$create_client = true;
             
            if($create_client)
            {
                //dd('creating user');
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
            //dd($client);
            if($client)Session::put('client_id', $client->id);
            //dd(Session::get('client_id'));
            
            return $client;
	}
        
	public function getLogout(){
		Sentry::logout();
                Session::flush();
                return Redirect::to('/clients/steps1');
	}
        
	public function getCreateUser() {
		try
		{/*
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
                 * */
                 
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
        
        
        public function getCreationAccountJson()
        {
                // run the transaction
           return DB::transaction(function()
           {
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

               
                //dd($client);
               
                $mail_data['client'] = $client;
                $mail_data['login'] = $input['user']['email'];
                $mail_data['pass']= Input::get('register_password');
                Mail::send('emails.client-welcome', $mail_data, function($message) use ($input)
                {
                   
                 //dd();
                    //$message->from('us@example.com', 'Laravel');
                    $message->to($input['user']['email'])->cc('forcremation@gmail.com');
                    //$message->attach($pathToFile);

                    Log::info('New customer Registered Emailed: '.$input['user']['email']);
                });
                
                return Response::json($client);
           });
        }
        
        
        // FIND THE TOTAL COST TO END USER FOR THIS REGISTRATION
        public function getSaleTotals($client, $provider){
            $TOTAL_PRICE = 0;
            //dd();

            //echo '<pre>';dd($provider);
            if($client->CremainsInfo->package_plan == "1") $saleSummary['report']['package_plan']['price'] = $provider->pricing_options->basic_cremation;
            elseif($client->CremainsInfo->package_plan == "2") $saleSummary['report']['package_plan']['price'] = $provider->pricing_options->premium_cremation;
            else $saleSummary['report']['package_plan']['price'] = '';

            $saleSummary['report']['package_plan']['name'] = 'Plan';
            $saleSummary['report']['package_plan']['qnt'] = '1';
            $saleSummary['report']['package_plan']['desc'] = $client->CremainsInfo->package_plan==1?'Package A':'Package B';
            $TOTAL_PRICE += $saleSummary['report']['package_plan']['price'];
           

            if($client->DeceasedInfo->weight == "weight_lt_250") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_lt_250;
            elseif($client->DeceasedInfo->weight == "weight_lt_300") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_lt_300;
            elseif($client->DeceasedInfo->weight == "weight_lt_350") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_lt_350;
            elseif($client->DeceasedInfo->weight == "weight_gt_350") $saleSummary['report']['weight']['price'] = $provider->pricing_options->weight_gt_350;
            else $saleSummary['report']['weight']['price'] = '';

            $saleSummary['report']['weight']['name'] = 'Weight';
            $saleSummary['report']['weight']['qnt'] = '1';
            $saleSummary['report']['weight']['desc'] = $client->DeceasedInfo->weight;
            $TOTAL_PRICE += $saleSummary['report']['weight']['price'];
            
            

            if($client->DeceasedInfo->has_pace_maker == "1") $saleSummary['report']['has_pace_maker']['price'] = $provider->pricing_options->pacemaker;
            else $saleSummary['report']['has_pace_maker']['price'] = '';
            $saleSummary['report']['has_pace_maker']['name'] = 'Has Pacemaker';
            $saleSummary['report']['has_pace_maker']['qnt'] = '1';
            $saleSummary['report']['has_pace_maker']['desc'] = $client->DeceasedInfo->has_pace_maker?'Yes':'No';
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
            $saleSummary['report']['cremation_shipping_plan']['name'] = 'Plan For Cremation Remains';
            $saleSummary['report']['cremation_shipping_plan']['qnt'] = '1';
            $saleSummary['report']['cremation_shipping_plan']['desc'] = $client->CremainsInfo->cremation_shipping_plan;
            $TOTAL_PRICE += $saleSummary['report']['cremation_shipping_plan']['price'];
            

            if($client->CremainsInfo->cert_plan == "deathcert_wurn") {$saleSummary['report']['cert_plan']['price'] = $provider->pricing_options->deathcert_wurn; $desc = "Mail certificate(s) with urn";}
            elseif($client->CremainsInfo->cert_plan == "deathcert_cep") {$saleSummary['report']['cert_plan']['price'] = $provider->pricing_options->deathcert_cep; $desc = "Mail certificate(s) seperately";}
            elseif($client->CremainsInfo->cert_plan == "deathcert_pickup") {$saleSummary['report']['cert_plan']['price'] = $provider->pricing_options->deathcert_pickup; $desc = "Pick up certificate(s) at our office";}
            else { $saleSummary['report']['cert_plan']['price'] = $desc = ''; }
            $saleSummary['report']['cert_plan']['name'] = 'Certificate Shipping';
            $saleSummary['report']['cert_plan']['qnt'] = '1';
            $saleSummary['report']['cert_plan']['desc'] = $desc;
            
            $TOTAL_PRICE += $saleSummary['report']['cert_plan']['price'];
            //echo '<pre>';print_r($client->CremainsInfo); print_r($provider->pricing_options); echo '</pre>'; die();
            
            //dd($cremain_info);
            $saleSummary['report']['number_of_certs']['name'] = 'Certificates';
            $saleSummary['report']['number_of_certs']['desc'] = 'Number of Certificates ($'.$provider->pricing_options->deathcert_each." each): " . $client->CremainsInfo->number_of_certs;
            $saleSummary['report']['number_of_certs']['qnt'] = '1';
            $saleSummary['report']['number_of_certs']['price'] = number_format((float)$provider->pricing_options->deathcert_each * ($client->CremainsInfo->number_of_certs?$client->CremainsInfo->number_of_certs:0), 2, '.', '');
            $TOTAL_PRICE += $saleSummary['report']['number_of_certs']['price'];


            $saleSummary['report']['filing_fee']['name'] = 'Filing Fee';
            $saleSummary['report']['filing_fee']['qnt'] = '1';
            $saleSummary['report']['filing_fee']['desc'] = '';
            $saleSummary['report']['filing_fee']['price'] = $provider->pricing_options->filing_fee;
            $TOTAL_PRICE += $saleSummary['report']['filing_fee']['price'];

            if($provider->pricing_options->custom1_text != "" and ($client->CremainsInfo->custom1==1) and ClientController::customerCustomPlanOptions($client->CremainsInfo->package_plan, $provider->pricing_options->custom1_included)) {
                $saleSummary['report']['custom1']['desc'] = '';
                $saleSummary['report']['custom1']['qnt'] = '1';
                $saleSummary['report']['custom1']['name'] = $provider->pricing_options->custom1_text;
                $saleSummary['report']['custom1']['price'] = $provider->pricing_options->custom1;
                $TOTAL_PRICE += $saleSummary['report']['custom1']['price'];
            }
            if($provider->pricing_options->custom2_text != "" and ($client->CremainsInfo->custom2==1) and ClientController::customerCustomPlanOptions($client->CremainsInfo->package_plan, $provider->pricing_options->custom2_included)) {
                $saleSummary['report']['custom2']['desc'] = '';
                $saleSummary['report']['custom2']['qnt'] = '1';
                $saleSummary['report']['custom2']['name'] = $provider->pricing_options->custom2_text;
                $saleSummary['report']['custom2']['price'] = $provider->pricing_options->custom2;
                $TOTAL_PRICE += $saleSummary['report']['custom2']['price'];
            }
            if($provider->pricing_options->custom3_text != "" and ($client->CremainsInfo->custom3==1) and ClientController::customerCustomPlanOptions($client->CremainsInfo->package_plan, $provider->pricing_options->custom3_included)) {
                $saleSummary['report']['custom3']['desc'] = '';
                $saleSummary['report']['custom3']['qnt'] = '1';
                $saleSummary['report']['custom3']['name'] = $provider->pricing_options->custom3_text;
                $saleSummary['report']['custom3']['price'] = $provider->pricing_options->custom3;
                $TOTAL_PRICE += $saleSummary['report']['custom3']['price'];
            }
            
            
            //URN COSTS
            $client_choosen_product = ClientProducts::where('provider_id',$provider->id)->where('client_id',$client->id)->first();
            if($client_choosen_product == null || count($client_choosen_product)<1)$this_product = 1;
            else $this_product = $client_choosen_product->product_id;            
             
            $client_product = ProviderProducts::where('provider_id',$provider->id)->where('product_id',$this_product)->first();
            if($client_product == null || count($client_product)<1)$client_product = Products::where('id',$this_product)->first();

            $saleSummary['report']['urn_product']['name'] = 'Urn';
            $saleSummary['report']['urn_product']['qnt'] = '1';
            $saleSummary['report']['urn_product']['desc'] = $client_product->name;
            $saleSummary['report']['urn_product']['price'] = $client_product->price;
            $TOTAL_PRICE += $saleSummary['report']['urn_product']['price'];
            
            

            $saleSummary['total'] = number_format((float)$TOTAL_PRICE, 2, '.', '');
            
            return $saleSummary;
        }
        
        //DECIDE IF WE SHOULD COUNT THIS CUSTOM OPTION TOWARDS THE SALE
	function customerCustomPlanOptions($package_plan, $custom_included){			
            if($package_plan == $custom_included)return true;
            elseif($custom_included == 3)return true;
            else return false;
	}   

    public function getCustomerPaymentSummary($client, $provider)
    {

        if(!is_array($client->sale_summary_r['report']))$client->sale_summary_r = ClientController::getSaleTotals($client, $provider);

        if(is_array($client->sale_summary_r['report'])){

            $html = '<table width="100%" ><tr><td style="border-bottom:1px solid #999;"><b>Name</b></td><td style="border-bottom:1px solid #999;"><b>Description</b></td><td style="border-bottom:1px solid #999;width:80px;"><b>Price</b></td></tr>';

            foreach($client->sale_summary_r['report'] as $key=>$value){
                $html .= '<tr><td>'.$value['name'].' </td><td>'.$value['desc'].' </td><td>'.$value['price'].' </td></tr>';
            }
            $html .= '<tr><td colspan="2" style="border-top:1px solid #999;" align=right><b>Total Summary</b>:&nbsp;</td><td style="border-top:1px solid #999;">$'.$client->sale_summary_r['total'].'</td></tr>';
            $html .= '</table>';
        }
        return $html;

    }
        
     
    /*
     * PDF FORM BUILDER USING DOMPDF
     * 
     * 
     */    
    public function getCustomerDocuments($client_id='', $provider_id='', $download_forms=''){
        if($client_id=='')$client_id = Input::get('client_id');
        if($provider_id=='')$provider_id = Input::get('provider_id');
        if($download_forms=='')$download_forms = Input::get('download_forms');

        return ClientController::postCustomerDocuments($client_id, $provider_id, $download_forms);
    }
    public function postCustomerDocuments($client_id='', $provider_id='', $download_forms=''){
        if($client_id=='')$client_id = Input::get('client_id');
        if($provider_id=='')$provider_id = Input::get('provider_id');
        
        if($client_id=='')$client_id = Session::get('client_id');
        if($provider_id=='')$provider_id = Session::get('provider_id');
        
        
        if(Input::get('download_forms')!=null)$download_forms = Input::get('download_forms');        
        if($download_forms == '')$download_forms = array('customer_form_2'=>'Hospital Release');
        if(!is_array($download_forms))$download_forms = array($download_forms=>$download_forms);
           
        $client = Client::find($client_id);
        $client = ClientController::fillOutClientTables($client);
        //$provider = FProvider::find($provider_id);
        $provider = ClientController::updateProvider($provider_id);
        $html = '';
        
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

            'sales_summary_sheet',
            
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
            'DeceasedInfo_dod',
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
            'DeceasedInfoPresentLoc_zip',
            
            'ClientProducts_name',
            'ClientProducts_price',
            'ClientProducts_description',
            'ClientProducts_note'
        );


        //dd($download_forms);
        foreach($download_forms as $key=>$file_name){
            
            $html .= $provider->$key;
            //dd($key.':'.$html);
            foreach($options as $val) {
                $key = substr($val, strpos($val, '_') + 1, strlen($val));
                $object = substr($val, 0, strpos($val, '_'));

                switch ($object) {
                    case 'client':
                        $class = $client;
                        break;
                    case 'provider':
                        $class = $provider;
                        break;
                    case 'DeceasedFamilyInfo':
                        $class = $client->DeceasedFamilyInfo;
                        break;
                    case 'DeceasedInfo':
                        $class = $client->DeceasedInfo;
                        break;
                    case 'CremainsInfo':
                        $class = $client->CremainsInfo;
                        break;
                    case 'DeceasedInfoPresentLoc':
                        $class = $client->DeceasedInfoPresentLoc;
                        break;
                    case 'ClientProducts':
                        $class = $client->ClientProducts;
                        $client_product = ProviderProducts::where('provider_id', $provider_id)->where('product_id', $class->product_id)->first();
                        if ($client_product == null) $client_product = Products::where('id', 1)->first();
                        $class->name = $client_product->name;
                        $class->description = $client_product->description;

                        break;
                    case 'sales':
                        $class = $client;
                        $class->summary_sheet = ClientController::getCustomerPaymentSummary($client, $provider);

                        break;

                    default:
                        break;
                }

                //has_pace_maker

                switch($class->$key){
                    case '0': $class->$key = 'No'; break;
                    case '1': $class->$key = 'Yes'; break;
                    default: break;
                }
                $html = str_replace('{{' . $val . '}}', $class->$key, $html);
                $html = str_replace('{{' . $val . '}}', '', $html);

            }
            $html .= '<p style="page-break-after:always;"></p>';

        }

        //echo '<pre>';dd($html);
        //download_forms
        //
        //<p><!-- pagebreak --></p> 
        $pdf = App::make('dompdf');
        $pdf->loadHTML($html);
        return $pdf->stream('CremationDocuments'.date('Y-m-d').'.pdf');
    }



    //FRESHBOOKS BILLING INTEGRATION FOR A PROVIDERS CLIENTS
    public function postAddBillingClient($provider_id='', $client_id='', $return_client_id=false)
    {
        if($client_id=='')$client_id = Input::get('client_id');
        if($provider_id=='')$provider_id = Input::get('provider_id');

        if($client_id=='')$client_id = Session::get('client_id');
        if($provider_id=='')$provider_id = Session::get('provider_id');

        $provider = FProvider::find($provider_id);
        $client = Client::find($client_id);
        $client_user = User::find($client->user_id);


        if($provider->freshbooks_clients_enabled == '1' and $provider->freshbooks_clients_people == '1' and $provider->freshbooks_api_url != '' and $provider->freshbooks_api_token != '') {

            $domain = str_replace('https://', '', $provider->freshbooks_api_url);
            $domain = substr($domain, 0, strpos($domain, '.freshbooks.com'));

            /* FRESHBOOKS API */
            //$domain = 'forcremationcom'; // https://your-subdomain.freshbooks.com/
            //$token = $provider->freshbooks_api_token; // your api token found in your account
            //Freshbooks\FreshBooksApi::init($domain, $token);


            // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
            $fb_client_info = array(
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'email' => $client->User->email,
                //'username' => $provider->email,
                'work_phone' => $client->phone,
                'fax' => $client->fax,
                'p_street1' => $client->address . ' ' . $client->apt,
                'p_city' => $client->city,
                'p_state' => $client->state,
                'p_country' => 'United States',
                'p_code' => $client->zip
            );
            $domain = 'forcremationcom'; // https://your-subdomain.freshbooks.com/
            $token = '95cad39d382f8bc4ae2d2a2a119e6559'; // your api token found in your account
            $fb = new Freshbooks\FreshBooksApi($domain, $token);

            if ($client->fb_client_id == '') {
                $fb->setMethod('client.create');
                $type = 'Created';
            }
            else {
                $fb->setMethod('client.update');
                $fb_client_info['client_id'] = $client->fb_client_id;
                $type = 'Updated';
            }

            $fb->post( array('client' => $fb_client_info ) );

            //dd($fb->getGeneratedXML()); // You can view what the XML looks like that we're about to send over the wire

            $fb->request();

            if($fb->success()) {
                Session::flash('success','Successfully '.$type.' Freshbooks entry');
                //dd($fb->getResponse());
                $res = $fb->getResponse();
                if($type == 'Created'){
                    $fb_client_id = $res['client_id'];
                    $client->fb_client_id = $fb_client_id;
                    $client->save();
                }

                //freshbooks_client_id
                //freshbooks_recurring_id
                //return $fb_client_id;
            } else {
                echo $fb->getError();
                Session::flash('error','Errors Creating Freshbooks Entry');

                var_dump($fb->getResponse());
            }


        }
        else Session::flash('Please Enter All Provider Freshbooks Information');

        if($return_client_id) return $client->fb_client_id;
        else return Redirect::action('ClientController@getSteps');

    }


    function postInvoiceClient($provider_id='', $client_id='', $return_invoice_id=false)
    {
            if($client_id=='')$client_id = Input::get('client_id');
            if($provider_id=='')$provider_id = Input::get('provider_id');

            if($client_id=='')$client_id = Session::get('client_id');
            if($provider_id=='')$provider_id = Session::get('provider_id');

            $provider = FProvider::find($provider_id);
            $provider = ClientController::updateProvider($provider_id);

            $client = Client::find($client_id);
            $clientData = ClientController::fillOutClientTables(Client::find($client_id));
            //$client_user = User::find($client->user_id);


            //$bill = Billing::first();


            if($client == null)return;

            if($client->fb_client_id=='' || $client->fb_client_id=='0'){
                $client->fb_client_id = ClientController::postAddBillingClient($provider_id, $client_id, true);
                //$provider = FProvider::find($provider->id);
            }


            if(!is_array($clientData->sale_summary_r['report']))$clientData->sale_summary_r = ClientController::getSaleTotals($clientData, $provider);

            if(is_array($clientData->sale_summary_r['report'])){
                $invoice['invoice']['client_id'] = $client->fb_client_id;
                $invoice['invoice']['lines']['line'] = array();

                foreach($clientData->sale_summary_r['report'] as $key=>$value){

                    array_push($invoice['invoice']['lines']['line'], array(
                                            'name'=>$value['name'],
                                            'description'=>$value['desc'],
                                            'unit_cost'=>$value['price'],
                                            'quantity'=>($value['qnt']==''?'1':$value['qnt']),
                                            'tax1_name'=>'',
                                            'tax2_name'=>'',
                                            'tax1_percent'=>'',
                                            'tax2_percent'=>''
                                        )


                    );

                }
                //$client->sale_summary_r['total']

            }
        //echo '<pre>';dd($line_items_r);


            if($client->fb_invoice_id=='0' || $client->fb_invoice_id=='')$create_new = true;
            else $create_new = false;



       //echo '<pre>';dd($invoice); // You can view what the XML looks like that we're about to send over the wire

            $domain = 'forcremationcom'; // https://your-subdomain.freshbooks.com/
            $token = '95cad39d382f8bc4ae2d2a2a119e6559'; // your api token found in your account
            $fb = new Freshbooks\FreshBooksApi($domain, $token);

            if($create_new)$fb->setMethod('invoice.create');
            else {
                $invoice['invoice']['invoice_id'] = $client->fb_invoice_id;
                $fb->setMethod('invoice.update');
            }


            // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
            $fb->post( $invoice );





            //echo '<pre>';dd($fb->getGeneratedXML()); // You can view what the XML looks like that we're about to send over the wire

            $fb->request();

            if($fb->success()) {
                Session::flash('success','Successfully Created Freshbooks Invoice');
                //dd($fb->getResponse());

                $res = $fb->getResponse();
                //dd($res);


                if($create_new){
                    $invoice_id = $res['invoice_id'];
                    $client->fb_invoice_id = $invoice_id;
                    $client->save();
                }

            } else {
                //echo $fb->getError();
                Session::flash('error','Errors Setting Freshbooks Recurring Entry, Has the Person or Invoice Been Deleted From Freshbooks?<br />'
                    . 'The Freshbook Integration Has been Reset for this client, the next time you change plans and save it will create a new setup in freshbooks for them. '
                    . 'Make sure to login to Freshbooks to double check everything is ok and no duplicates exist '.$fb->getError());

                $client->fb_client_id = '';
                $client->fb_recurring_id = '';
                $client->fb_invoice_id = '';
                //echo '<pre>';dd($client);
                $client->save();
                //dd($fb->getResponse());
            }

        if($return_invoice_id) return $client->fb_invoice_id;
        else return Redirect::action('ClientController@getSteps');

    }

    function postSendFormSigning($provider_id='', $client_id='', $return_redirect_url=false)
    {
        $forms_included = $com = '';


        if(Input::get('return_redirect_url') != '')$return_redirect_url = (bool)Input::get('return_redirect_url');
        if($client_id=='')$client_id = Input::get('client_id');
        if($provider_id=='')$provider_id = Input::get('provider_id');

        if($client_id=='')$client_id = Session::get('client_id');
        if($provider_id=='')$provider_id = Session::get('provider_id');

        $provider = FProvider::find($provider_id);
        $provider = ClientController::updateProvider($provider_id);

        $client = Client::find($client_id);
        $clientData = ClientController::fillOutClientTables(Client::find($client_id));
        //$client_user = User::find($client->user_id);

        $download_forms = Input::get('sign_forms');
        $doc_forms = ProviderController::getDocumentTypes();

        if(Input::get('sign_forms')!=null)$download_forms = Input::get('sign_forms');
        if($download_forms == '')$download_forms = array('customer_form_2'=>'Hospital Release');
        if(!is_array($download_forms))$download_forms = array($download_forms=>$download_forms);

        ## FORMS URL FOR RIGHTSIGNATURE TO DOWNLOAD FROM
        $forms_url = 'http://provider.forcremation.com/clients/customer-documents?provider_id='.$provider_id.'&client_id='.$client_id;

        if($download_forms != null)
        foreach($download_forms as $key=>$file_name){

            $forms_included .= $file_name.$com.' ';
            $com = ',';
            $forms_url .= '&download_forms['.$key.']='.$file_name;
        }
        #$forms_url = 'http://www.forcremation.com/images/test.pdf';

        if($client != null && $forms_included != ''){
            $rightsignature = new RightSignature();
            $rightsignature->debug = false;

            $doc_data['doc_name'] = $client->first_name.' '.$client->last_name;
            $doc_data['doc_url'] = urlencode($forms_url).'.pdf';
            $doc_data['doc_to_sign_name'] = $client->first_name.' '.$client->last_name;
            $doc_data['doc_to_sign_email'] = $client->User->email;
            $doc_data['doc_cc_name'] = $provider->business_name;
            $doc_data['doc_cc_email'] = $provider->email;
            $doc_data['doc_action'] = $return_redirect_url ? 'redirect' : 'send'; //send or redirect
            $doc_data['doc_client_id'] = $client_id;
            $doc_data['doc_forms_included'] = $forms_included;

            #$res = $rightsignature->testPost();
            #dd($res);
            #dd($doc_data);
            $data['right_docs'] = $rightsignature->sendDocuments($doc_data);

            #dd($data['right_docs']);
        }

        if($return_redirect_url) {

            $xml = simplexml_load_string($data['right_docs'], 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
            if ($xml != false) {
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
            }
            $data['right_docs'] = trim($data['right_docs']);

            dd('https://rightsignature.com/builder/new?rt='.$data['right_docs']);
            return Redirect::away("https://rightsignature.com/builder/new?rt=".$data['right_docs']);
        }
        else return Redirect::action('ClientController@getSteps', $data);

    }

}