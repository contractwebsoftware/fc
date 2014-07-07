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

	public function getEditProvider($id)
	{
		$data['provider'] = FProvider::find($id);
		$data['fuser'] = User::find($data['provider']->user_id);
		$data['zips'] = ProviderZip::where('provider_id',$data['provider']->id)->get();
		$data['pricing'] = (object) [
			'plan_a_total'=>875,
			'plan_a_detail'=>'Basic Service Fee, care of your loved one in climatically controlled environment, obtaining Cremation Authorizations and filing the Death Certificate with State of California @ $690, Crematory fee, Cremation container and Basic urn @ $185.',
			'plan_b_total'=>1175,
			'plan_b_detail'=>'Premium Package includes all services of Plan A plus an urn. Refer to the General Price List for our urn selection.',
			'weight_under_250'=>0,
			'weight_251_300'=>100,
			'weight_301_350'=>275,
			'weight_above_351'=>350,
			'pacemaker_removal'=>150,
			'certificate_with_urn'=>125,
			'certificate_without_urn'=>25,
			'pick_cert_office'=>0,
			'each_death_certificate'=>21,
			'scatter_on_land'=>125,
			'scatter_at_sea'=>125,
			'custom_pricing_1_text'=>'',
			'custom_pricing_1_value'=>'',
			'custom_pricing_1_include'=>'',
			'custom_pricing_1_required'=>'',
			'custom_pricing_2_text'=>'',
			'custom_pricing_2_value'=>'',
			'custom_pricing_2_include'=>'',
			'custom_pricing_2_required'=>'',
			'custom_pricing_3_text'=>'',
			'custom_pricing_3_value'=>'',
			'custom_pricing_3_include'=>'',
			'custom_pricing_3_required'=>'',
		];
		$this->layout->content = View::make('admin.provider-edit',$data);
	}

	public function postUpdate()
	{
		$input = Input::all();
		// find the provider
		$provider = FProvider::find($input['provider_id']);
	//	$provider->provider = $input['provider'];
		$provider->save();
		Session::flash('success','Provider\'s Data has been updated');
		return Redirect::action('AdminController@getProviders');
	}
	
	public function postUpdateZip(){
		Session::flash('success','Provider\'s Data has been updated');
		return Redirect::action('AdminController@getProviders');
	}

	public function getBillingPage()
	{
		$this->layout->content = View::make('admin.billing',['billing'=>Billing::first()]);
	}

	public function postUpdateBilling()
	{
		$input = Input::all();
		$rules = ['monthly_fee'=>'required'];
		$v = Validator::make($input,$rules);
		if($v->fails()) return Redirect::back()->withErrors($v);

		$bill = Billing::find(1);
		$bill->monthly_fee = $input['monthly_fee'];
		$bill->lead_fee = $input['lead_fee'];
		$bill->save();
		Session::flash('success','Billing Setting has been updated');
		return Redirect::action('AdminController@getBillingPage');
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