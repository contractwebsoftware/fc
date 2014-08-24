<?php
class AdminController extends BaseController {
	protected $layout = 'layouts.admin';

	public function getCustomers()
	{
		$this->layout->content = View::make('admin.customers');
	}

	public function getProviders()
	{
		$q = Input::get('q');
		if(strlen($q)>3)
		{
			$users = DB::table('users')->where('email','like','%'.$q.'%')->lists('id');
			$providers = FProvider::whereIn('user_id',$users)->get();
		}
		else
		{
			$providers = FProvider::with('user')->get();
		}
		$this->layout->content = View::make('admin.providers')->withProviders($providers);
	}

	public function getNewProvider()
	{
		$this->layout->content = View::make('admin.provider-new');
	}

	public function postStore()
	{
		$input = [
			'provider' => Input::get('provider'),
			'address' => Input::get('address'),
			'city' => Input::get('city'),
			'state' => Input::get('state'),
			'zip' => Input::get('zip'),
			'website' => Input::get('website'),
			'phone' => Input::get('phone'),
			'fax' => Input::get('fax'),
			'service_radius' => Input::get('service_radius'),
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation')
		];
		$rules = [
			'provider' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'zip' => 'required',
			'website' => 'required|url',
			'phone' => 'required',
			'fax' => 'required',
			'service_radius' => 'required',
			'email' => 'required|email',
			'email' => 'required',
			'password' => 'required|confirmed',
		];

		// validate the input
		$v = Validator::make($input,$rules);
		if( $v->fails() ) return Redirect::back()->withErrors($v);

		// run the transaction
		DB::transaction(function()
		{
			// Create User
		    $user = Sentry::createUser(array(
                        'email'       => Input::get('email'),
                        'password'    => Input::get('password'),
                        'activated'   => true,
                    ));

		    $input = [
		    	'provider' => Input::get('provider'),
		    	'address' => Input::get('address'),
		    	'city' => Input::get('city'),
		    	'state' => Input::get('state'),
		    	'zip' => Input::get('zip'),
		    	'website' => Input::get('website'),
		    	'phone' => Input::get('phone'),
		    	'fax' => Input::get('fax'),
		    	'service_radius' => Input::get('service_radius'),
		    	'user_id' => $user->id
		    ];
		    // dd($input);
		    DB::table('providers')->insert($input);
		    // $provider = FProvider::create($input);
		    // dd($provider);

		    DB::table('users_groups')->insert(['user_id'=>$user->id,'group_id'=>2]);
		});
		Session::flash('success','Provider has been added');
		return Redirect::action('AdminController@getProviders');
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
		$data['zips'] = ProviderZip::where('provider_id',$data['provider']->id)->get();
		$data['pricing'] = ProviderPricingOptions::where('provider_id',$data['provider']->id)->first();
                $data['provider_files'] = ProviderFiles::where('provider_id', $data['provider']->id)->get();
                
                
                $this_zip = Zip::where('zip',$data['provider']->zip)->first();
                if($this_zip!=null)$data['zip_info'] = AdminController::findZipsInRadius($data['provider']->provider_radius, $this_zip->latitude, $this_zip->longitude);
                
		//$this->layout->content = View::make('admin.provider-edit',$data);
                $this->layout->content = View::make('admin.provider-edit',$data);
                //return View::make('admin.provider-edit',$data);
	}

	public function postUpdate()
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
		$pricing_options = ProviderPricingOptions::find($input['provider']['id']);
                $pricing_options->fill($input['pricing']);   
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
			$data = User::find(Sentry::getUser()->id);
			$data->password = Hash::make($input['newpassword']);
			$data->save();
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

}