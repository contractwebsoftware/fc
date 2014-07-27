<?php
class ProviderController extends BaseController {

	protected $layout = 'layouts.provider';
	public function getCustomers()
	{
		$this->layout->content = View::make('providers.customers');
	}

	public function getInformation()
	{
		$provider = FProvider::where('user_id',Sentry::getUser()->id)->first();
		$this->layout->content = View::make('providers.provider-edit',['provider'=>$provider]);
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
	
	public function postLogin()
	{
		// take the input
		$input = ['email'=>Input::get('email'),'password'=>Input::get('password')];
		$rules = ['email'=>'required|email','password'=>'required'];

		// validate the input
		$v = Validator::make($input,$rules);
		if( $v->fails() ) return Redirect::back()->withErrors($v);

		// Process the data and redirect to the main page
		return Redirect::action('ProviderController@getIndex');
	}

	public function postUpdate(){
		Session::flash('success','Provider\'s Data has been updated');
		return Redirect::action('AdminController@getProviders');
	}
        
        
        public function postUpdatePricing(){
		Session::flash('success','Provider\'s Data has been updated');
                $provider = FProvider::where('user_id',Sentry::getUser()->id)->first();
		
                
		return Redirect::action('ProviderController@getInformation');
	}
        

}