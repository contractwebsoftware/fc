<?php
class AdminController extends BaseController {
	protected $layout = 'layouts.admin';

	public function getProviders()
	{
            if(Sentry::getUser()->role=='provider') return Redirect::action('AdminController@getEditProvider', array('id'=>Sentry::getUser()->id));
            $q = Input::get('q');
            if(strlen($q)>=3)
            {
                    //$users = DB::table('users')->where('email','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%')->lists('id');

                    if(Input::get('include_deleted')==1)$providers = FProvider::where('email','like','%'.$q.'%')->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%')->withTrashed()->orderBy('business_name', 'asc')->get();
                    else $providers = FProvider::where('email','like','%'.$q.'%')->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%')->orderBy('business_name', 'asc')->get();
                    ////if($providers!=null)$providers = FProvider::where('email','like','%'.$q.'%')->get();
                    //else $providers = FProvider::with('user')->get();
                    if($providers == null){
                        if(Input::get('include_deleted')==1)$providers = FProvider::with('user')->orderBy('business_name', 'asc')->withTrashed()->get();
                        else $providers = FProvider::with('user')->orderBy('business_name', 'asc')->get();
                    }

            }
            else
            {
                if(Input::get('include_deleted')==1)$providers = FProvider::with('user')->orderBy('business_name', 'asc')->withTrashed()->get();
                else $providers = FProvider::with('user')->orderBy('business_name', 'asc')->get();
            }
            foreach($providers as $provider){
                $client_provider = DB::table('clients_providers')->where('provider_id', $provider->id)->get();
                $provider->client_count = count($client_provider);
            }
            $this->layout->content = View::make('admin.providers')->withProviders($providers);
		
	}

	public function getNewProvider()
	{
             $input = Input::all();
               
	     $this->layout->content = View::make('admin.provider-new', $input);
	}

	public function postStore()
	{
                    

            $input = [
                    'business_name' => Input::get('business_name'),
                    'address' => Input::get('address'),
                    'city' => Input::get('city'),
                    'state' => Input::get('state'),
                    'zip' => Input::get('zip'),
                    'website' => Input::get('website'),
                    'phone' => Input::get('phone'),
                    'fax' => Input::get('fax'),
                    'provider_radius' => Input::get('provider_radius'),
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

            $users = User::where('email',Input::get('email'))->first();
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
                    'business_name' => Input::get('business_name'),
                    'address' => Input::get('address'),
                    'city' => Input::get('city'),
                    'state' => Input::get('state'),
                    'zip' => Input::get('zip'),
                    'website' => Input::get('website'),
                    'phone' => Input::get('phone'),
                    'fax' => Input::get('fax'),
                    'provider_radius' => Input::get('provider_radius'),
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

                $billing = new ProviderBilling();
                $billing->fill( Array('provider_id'=>$provider->id) );
                $billing->save(); 

                
                
                // $provider = FProvider::create($input);
                 //dd($provider);

                DB::table('users_groups')->insert(['user_id'=>$user->id,'group_id'=>2]);
                
                Session::flash('success','Provider has been added');
                return Redirect::action('AdminController@getEditProvider', array('id' => $provider->id));
            });
	}

        public function findZipsInRadius($miles, $ziplat, $ziplong){
            
                $zips = DB::select( DB::raw("SELECT zip, (
                              3959 * acos (
                                cos ( radians($ziplat) )
                                * cos( radians( latitude ) )
                                * cos( radians( longitude ) - radians($ziplong) )
                                + sin ( radians($ziplat) )
                                * sin( radians( latitude ) )
                              )
                            ) AS distance
                            FROM zips
                            HAVING distance < $miles
                            ORDER BY zip asc
                        "));
                return $zips;
                
        }
	public function getEditProvider($id)
	{
		$data['provider'] = FProvider::find($id);
		$data['fuser'] = User::find($data['provider']->user_id);
		if($data['fuser'] == null) $data['fuser'] = new User;
                $data['zips'] = ProviderZip::where('provider_id',$data['provider']->id)->get();
		$data['pricing'] = ProviderPricingOptions::where('provider_id',$data['provider']->id)->first();
                $data['provider_files'] = ProviderFiles::where('provider_id', $data['provider']->id)->get();
                
                
                $this_zip = Zip::where('zip',$data['provider']->zip)->first();
                if($this_zip!=null)$data['zip_info'] = AdminController::findZipsInRadius($data['provider']->provider_radius, $this_zip->latitude, $this_zip->longitude);
                else $data['zip_info'] = null;
                
                $data['clients'] = AdminController::getProviderCustomers($id);
                //dd($data['clients']);
                
                $this->layout->content = View::make('admin.provider-edit',$data);
	}
        
        
        public function  getDeleteProvider($id){
            $provider = FProvider::find($id);    
            if($provider!=null){
                $provider->provider_status = 2;
                $provider->save();
                $provider->delete();
            }
            Session::flash('success','Provider Soft Deleted Successfully');
            return Redirect::action('AdminController@getProviders');
        }
        
        public function  getUnDeleteProvider($id){
            $provider = FProvider::withTrashed()->find($id);
            if($provider!=null){
                $provider->provider_status = 0;
                $provider->save();
                $provider->restore();
                Session::flash('success','Provider UnDeleted Successfully');
            }
            return Redirect::action('AdminController@getProviders');
        }
        

	public function postUpdateProvider()
	{
		$input = Input::all();
                //dd($input['provider']['id']);
		$provider = FProvider::find($input['provider']['id']);
                //dd($provider);
                if($provider == null){
                    $provider = new FProvider();  
                } 
                $provider->fill($input['provider']);
                $provider->save();
                
                if($input['provider_login']!=""){
                    $user = User::find($provider->user_id);
                    if($user == null)$user = new User;
                    $user->email = $input['provider_login']; 
                    $user->activated = 1;
                    $user->role = "provider";
                    $user->save();
                    
                    $provider->user_id = $user->id;
                    $provider->save();
                } 
                if($input['newpassword']!="" && $input['newpassword_confirmation']!="" && $user!=null){
                    $newpass = ['newpassword'=>$input['newpassword'],'newpassword_confirmation'=>$input['newpassword_confirmation']] ;
                    $rules = ['newpassword'=>'required|confirmed'] ;
                    // Let's validate
                    $v = Validator::make($newpass,$rules);
                    if($v->fails())
                            return Redirect::back()->withErrors($v);
                    
                    
                    // Okay all validation passed, we change the password
                    try {
                        $sentry_user = Sentry::findUserById($user->id);
                        $sentry_user->password = $input['newpassword'];
                        $sentry_user->save();
                        Session::flash('success','Password Updated');
                    } catch (Exception $e) {
                        Session::flash('error','Password Update Failed');
                    }   
                }
                
                       
                
                //dd($provider);
                
		if($provider->provider_status=='2'){
                    $provider->delete();
                    Session::flash('success','Provider Soft Deleted Successfully');
                    return Redirect::action('AdminController@getProviders');
                }
                
                Session::flash('success','Provider\'s Data has been updated');
                return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id']));
		//return Redirect::action('AdminController@getEditProvider');
                
	}
	
	public function postUpdateZip(){
                $input = Input::all();
               
                //MASS ADD FROM READIUS
                if(array_key_exists('addzips',$input)){
                    foreach($input['addzips'] as $this_zip){
                        if($this_zip!=''){
                          $zip = ProviderZip::where('provider_id',$input['provider']['id'])->where('zip',$this_zip)->first();
                          if($zip==null)$zip = new ProviderZip();
                          $zip->zip = $this_zip;   
                          $zip->provider_id = $input['provider']['id'];
                          $zip->save();
                        }
                    }

                }

                //ADD FROM MANUAL INPUT
                if($input['provider_zip_code']!=''){
                    
                    
                    if(array_key_exists('provider_zip_code',$input))
                    if(strpos($input['provider_zip_code'],',')!==false){
                      $zips_r = explode(',', $input['provider_zip_code']);  
                      foreach($zips_r as $this_zip){
                          if($this_zip!=''){
                            $zip = ProviderZip::where('provider_id',$input['provider']['id'])->where('zip',$this_zip)->first();
                            if($zip==null)$zip = new ProviderZip();
                            $zip->zip = $this_zip;   
                            $zip->provider_id = $input['provider']['id'];
                            $zip->save();
                          }
                      }
                    }
                    else {
                        $zip = ProviderZip::where('provider_id',$input['provider']['id'])->where('zip',$input['provider_zip_code'])->first();
                        if($zip==null)$zip = new ProviderZip();
                        $zip->zip = $input['provider_zip_code'];   
                        $zip->provider_id = $input['provider']['id'];
                        $zip->save();
                    }
                    
                }
                elseif(array_key_exists('removezips',$input)){
                    foreach($input['removezips'] as $key=>$value){
                        $zip = ProviderZip::where('provider_id',$input['provider']['id'])->where('zip',$value)->first();
                        $zip->delete();
                    }
                }
		Session::flash('success','Zips Updated Successfully');
                
                return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id']));
	}
        
        public function postUpdatePricing(){
		Session::flash('success','Provider\'s Data has been updated');
                
                $input = Input::all();
		$pricing_options = ProviderPricingOptions::where('provider_id',$input['provider']['id'])->first();
                if($pricing_options==null)$pricing_options = new ProviderPricingOptions();
                $pricing_options->fill($input['pricing']);   
		$pricing_options->provider_id = $input['provider']['id'];
                //dd($pricing_options);
                $pricing_options->save();
                
		Session::flash('success','Provider\'s Pricing Data has been updated');
                return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id']));
	}
        
        
	public function getBillingPage()
	{
		$this->layout->content = View::make('admin.billing',['billing'=>Billing::first()]);
	}

        public function getRemoveFiles()
        {
            $fileid = Request::segment(3);
            $provider_id = Request::segment(4);
            
            if($fileid!=''){
                $file = ProviderFiles::where('provider_id',$provider_id)->where('id',$fileid)->first();
                $file->delete();
            }
            Session::flash('success','File Removed Successfully');
            return Redirect::action('AdminController@getEditProvider', array('id' => $provider_id));
        }
        
        public function postUpdateFiles()
        {
		$input = Input::all();
		
                if (array_key_exists('provider_files_new',$input))
                {
                    //dd($input['provider_files']);
                    $file = Input::file('provider_files_new');
                    //dd($file);
                    $destinationPath = public_path()."/provider_files/".$input['provider']['id'];
                    $file->move($destinationPath, $file->getClientOriginalName());
                    $name = $file->getClientOriginalName();
                    
                    $provider_file = ProviderFiles::where('provider_id', $input['provider']['id'])->where('file_type', $input['provider_files_type'])->first();
                    if($provider_file==null || $provider_file=="")$provider_file = new ProviderFiles();
                    $provider_file->provider_id = $input['provider']['id'];
                    $provider_file->file_name = $name;
                    $provider_file->file_type = $input['provider_files_type'];
                    $provider_file->save();
                }
                
		Session::flash('success','Provider Files Added Successfully');
		return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id']));
	}
        
	public function postUpdateBilling()
	{
		$input = Input::all();
		$rules = ['monthly_fee'=>'required'];
		$v = Validator::make($input['provider_billing'],$rules);
		if($v->fails()) return Redirect::back()->withErrors($v);

		$bill = ProviderBilling::where('provider_id', $input['provider']['id'])->first();
		$bill->monthly_fee = $input['provider_billing']['monthly_fee'];
		$bill->per_case_fee = $input['provider_billing']['per_case_fee'];
		$bill->save();
		Session::flash('success','Billing Setting has been updated');
		return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id']));
	}

	public function getAllUser()
	{
		// ambil provider
		$providers = FProvider::with('user')->get();
		// ambil admin
		$admins = User::whereRole('admin')->get();
		$this->layout->content = View::make('admin.alluser',['providers'=>$providers,'admins'=>$admins,'i'=>0,'j'=>0]);
		// ambil customer
		// belum ada datanya ini
	}

	public function getSetting()
	{
		$this->layout->content = View::make('admin.setting');
	}

	public function postSetting()
	{
		// Input & rules
		$input = ['oldpassword'=>Input::get('oldpassword'),'newpassword'=>Input::get('newpassword'),'newpassword_confirmation'=>Input::get('newpassword_confirmation')] ;
		$rules = ['oldpassword'=>'required','newpassword'=>'required|confirmed'] ;
		// Let's validate
		$v = Validator::make($input,$rules);
		if($v->fails())
			return Redirect::back()->withErrors($v);
		// If validation success let's process this, first, we check the password
		if(!Hash::check($input['oldpassword'],Sentry::getUser()->password)) {
			Session::flash('error','Incorrect Old Password');
			return Redirect::back();
		}
		// Okay all validation passed, we change the password
		try {
                    $sentry_user = Sentry::findUserById(Sentry::getUser()->id);
                    $sentry_user->password = Input::get('newpassword');
                    $sentry_user->save();
                    Session::flash('success','Password Updated');
                } catch (Exception $e) {
                    Session::flash('error','Password Update Failed');
                }
		return Redirect::back();
	}
	public function getCreateUser() 
	{
		try
		{
		    // Create the user
		    $user = Sentry::createUser(array(
		        'email'     => 'bendavol@gmail.com',
		        'password'  => 'test',
		        'activated' => true
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
        public function getProviderCustomers($provider_id)
	{
            Session::put('client_providers_id',$provider_id);
            $per_page = 50;
            $clients = Client::with('user');
            $clients->whereExists(function($query)
                {
                    $provider_id = Session::get('client_providers_id');
                    $query->select(DB::raw(1))
                          ->from('clients_providers')
                          ->whereRaw("clients_providers.client_id = clients.id and clients_providers.provider_id='".$provider_id."'");
                    //dd("clients_providers.client_id = clients.id and clients_providers.provider_id='".$provider_id."'");
                });
            
            $clients->orderBy('created_at', 'desc');
            $clients = $clients->paginate($per_page);
            foreach($clients as $client){
                $provider_id = Session::get('provider_id');
                if($provider_id!='')$client->provider = DB::table('clients_providers')->where('client_id', $client->id)->where('provider_id', $provider_id)->first();
                else $client->provider = DB::table('clients_providers')->where('client_id', $client->id)->first();


                $client_DeceasedInfo = DeceasedInfo::where('client_id', $client->id)->first();
                if($client_DeceasedInfo!=null){ 
                    $client->deceased_first_name = $client_DeceasedInfo->first_name;
                    $client->deceased_last_name = $client_DeceasedInfo->last_name;
                    if($client_DeceasedInfo->cremation_reason == "planning_for_future")$client->preneed = "y";
                    else $client->preneed = "n";
                }
                else $client->preneed = "n";
                
            }
            return $clients;
        }
        
        
        /*
         * CLIENTS MANAGEMENT
         * 
         * 
         */
        
	public function getCustomers()
	{
            $per_page = 50;
            if(Input::get('per')){
                $per_page = Input::get('per');
            }

            $q = Input::get('q');
            if(strlen($q)>=3)
            {
                $clients = Client::where('zip','like','%'.$q.'%')->orWhere('phone','like','%'.$q.'%')->orWhere('state','like','%'.$q.'%')
                        ->orWhere('address','like','%'.$q.'%')->orWhere('legal_name','like','%'.$q.'%')
                        ->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')
                        ->orWhere('first_name','like','%'.$q.'%')->orWhere('last_name','like','%'.$q.'%')
                        ->orWhere('created_at','like','%'.$q.'%');
                if($clients == null)$clients = Client::with('user');
            }
            elseif(Input::get('status')!="")
            {
                if(Input::get('status')==0 || Input::get('status')==1)$clients = Client::where('status','like',Input::get('status'));
                elseif(Input::get('status')==2)$clients = Client::where('status','like','%')->withTrashed(); 
                elseif(Input::get('status')==3)$clients = Client::where('status','like',3)->withTrashed();  
            }
            else {
                $clients = Client::with('user');
            }
            if(Sentry::getUser()->role=='provider' && Session::get('logged_in_provider_id')!=''){
                //$clients = $clients->leftJoin('clients_providers', 'clients_providers.client_id','=', 'clients.id');
                /*$clients->leftJoin('clients_providers', function($join)
                    {
                        $join->on('clients_providers.client_id', '=', 'clients.id')->andOn('clients_providers.provider_id','=',Session::get('logged_in_provider_id'));
                    })*/
                //$clients = $clients->whereRaw("clients_providers.client_id = clients.id and clients_providers.provider_id='".Session::get('logged_in_provider_id')."'");
                $clients->whereExists(function($query)
                {
                    $query->select(DB::raw(1))
                          ->from('clients_providers')
                          ->whereRaw("clients_providers.client_id = clients.id and clients_providers.provider_id='".Session::get('logged_in_provider_id')."'");
                });
            }
            $clients = $clients->orderBy('created_at', 'desc')->paginate($per_page);
            //$queries = DB::getQueryLog();
            //print_r( end($queries)); 
            
            
            foreach($clients as $client){
                $provider_id = Session::get('provider_id');
                if($provider_id!='')$client->provider = DB::table('clients_providers')->where('client_id', $client->id)->where('provider_id', $provider_id)->first();
                else $client->provider = DB::table('clients_providers')->where('client_id', $client->id)->first();


                $client_DeceasedInfo = DeceasedInfo::where('client_id', $client->id)->first();
                if($client_DeceasedInfo!=null){ 
                    $client->deceased_first_name = $client_DeceasedInfo->first_name;
                    $client->deceased_last_name = $client_DeceasedInfo->last_name;
                    if($client_DeceasedInfo->cremation_reason == "planning_for_future")$client->preneed = "y";
                    else $client->preneed = "n";
                }
                else $client->preneed = "n";
                
            }
            $this->layout->content = View::make('admin.customers')->withClients($clients);
		
	}
        
        public function getEditClient($id)
	{
            Session::put('client_id',$id);
            
            return Redirect::action('ClientController@getSteps');
                
	}
        
        
        public function  getDeleteClient($id, $return = true){
            $client = Client::withTrashed()->find($id);    
             
            if($client!=null){
                $client->status = 3; //Active=0 Completed=1 Deleted=3 
                $client->save();
                $client->delete();
            }
            if($return){
                Session::flash('success','Client Soft Deleted Successfully');
                return Redirect::action('AdminController@getCustomers');
            }
        }
        
        public function  getUnDeleteClient($id, $return = true){
            $client = Client::withTrashed()->find($id);
           
            if($client!=null){
                $client->status = 0;
                $client->save();
                $client->restore();
                
            }
            if($return){
                Session::flash('success','Client UnDeleted Successfully');
                return Redirect::action('AdminController@getCustomers');
            }
        }
        
        
        public function  getCompleteClient($id, $return = true){
            $client = Client::withTrashed()->find($id);
           
            if($client!=null){
                $client->status = 1;
                $client->save();
                $client->restore();
                
            }
            if($return){
                Session::flash('success','Client Set As Completed Successfully');
                return Redirect::action('AdminController@getCustomers');
            }
        }
        
        public function  getPreneedClient($id, $return = true){
            $client = Client::withTrashed()->find($id);
           
            if($client!=null){
                
                $client_DeceasedInfo = DeceasedInfo::where('client_id', $client->id)->first();
                if($client_DeceasedInfo!=null){ 
                
                    $client_DeceasedInfo->cremation_reason = "planning_for_future";
                    $client_DeceasedInfo->save();
                    
                    $client->status = 0;
                    $client->save();
                    $client->restore();
                    if($return)Session::flash('success','Client Set As PreNeed Successfully');
               
                }
                
            }
            if($return){
                //Session::flash('success','Client Set As PreNeed Successfully');
                return Redirect::action('AdminController@getCustomers');
            }
        }
        
        public function  postMassUpdateClients(){
            $clients_r = Input::get('edit_clients');
            $mass_edit_type = Input::get('mass_edit_type');
            //dd($mass_edit_type);
            if(is_array($clients_r))
            foreach($clients_r as $key=>$client_id){
                if($mass_edit_type == 'delete')AdminController::getDeleteClient($client_id,false);
                if($mass_edit_type == 'undelete' || $mass_edit_type == 'active')AdminController::getUnDeleteClient($client_id,false);
                if($mass_edit_type == 'completed')AdminController::getCompleteClient($client_id,false);
                if($mass_edit_type == 'preneed')AdminController::getPreneedClient($client_id,false);
            }
            
            Session::flash('success',count($clients_r).' Clients Updated Successfully');
            return Redirect::action('AdminController@getCustomers');
        }
        
        
        
}





