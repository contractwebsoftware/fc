<?php

/* FRESHBOOKS API */
$domain = 'forcremationcom'; // https://your-subdomain.freshbooks.com/
$token = '95cad39d382f8bc4ae2d2a2a119e6559'; // your api token found in your account
Freshbooks\FreshBooksApi::init($domain, $token);


class AdminController extends BaseController {
    protected $layout = 'layouts.admin';

    public function getProviders()
    {
        $per_page = 50;
        
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
        if(Sentry::getUser()->role=='provider') {
            $provider = FProvider::where('user_id',Sentry::getUser()->id)->first();
            return Redirect::action('AdminController@getEditProvider', array('id'=>$provider->id));
        }
        $q = Input::get('q');
        if(strlen($q)>=3)
        {
            //$users = DB::table('users')->where('email','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%')->lists('id');

            if(Input::get('include_deleted')==1)$providers = FProvider::where('email','like','%'.$q.'%')->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%')->withTrashed();
            else $providers = FProvider::where('email','like','%'.$q.'%')->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%');
            ////if($providers!=null)$providers = FProvider::where('email','like','%'.$q.'%');
            //else $providers = FProvider::with('user')->get();
            if($providers == null){
                if(Input::get('include_deleted')==1)$providers = FProvider::with('user')->withTrashed();
                else $providers = FProvider::with('user');
            }

        }
        else
        {
            if(Input::get('include_deleted')==1)$providers = FProvider::with('user')->orderBy('business_name', 'asc')->withTrashed();
            else $providers = FProvider::with('user');
        }
        $providers = $providers->orderBy('created_at', 'desc')->paginate($per_page);
        
        foreach($providers as $provider){
            $client_provider = DB::table('clients_providers')->where('provider_id', $provider->id)->get();
            $provider->client_count = count($client_provider);
        }
        
        $this->layout->content = View::make('admin.providers')->withProviders($providers);

    }

    public function getNewProvider()
    {
        $input = Input::all();
        $input['provider_plan_basic'] = ProviderPlans::where('id','1')->first();
        $input['provider_plan_premium'] = ProviderPlans::where('id','2')->first();
        
        $this->layout->content = View::make('admin.provider-new', $input);
    }

   
    public function postNewProvider()
    {
        Session::put('new_provider_data', Input::all());
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
                'plan_id' => Input::get('plan_id'),
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
                'email' => Input::get('email'),
                'address' => Input::get('address'),
                'city' => Input::get('city'),
                'state' => Input::get('state'),
                'zip' => Input::get('zip'),
                'website' => Input::get('website'),
                'phone' => Input::get('phone'),
                'fax' => Input::get('fax'),
                'plan_id' => Input::get('plan_id'),
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

            /*
            $bill = Billing::first();
            
            $billing = new ProviderBilling();
            $billing->fill( Array('provider_id'=>$provider->id) );
            $billing->monthly_fee = $bill->monthly_fee;
            $billing->per_case_fee = $bill->per_case_fee;
            $billing->save(); 
            */
            

            //CREATE FRESHBOOKS ENTRY
            //$client_id = AdminController::postAddBillingClient($provider->id);
            //if($client_id!='')AdminController::postAddRecurringBillingToClient($provider->id);
            //$provider = FProvider::create($input);
            //dd($provider);

            DB::table('users_groups')->insert(['user_id'=>$user->id,'group_id'=>2]);

            Session::put('new_provider_data',array());
            Session::flash('success','Provider has been added');
            return Redirect::action('AdminController@getEditProvider', array('id' => $provider->id));
        });
    }

    public function findZipsInRadius($miles, $ziplat, $ziplong){
            if($miles=='')$miles=5;
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
    public function getEditProvider($id, $current_tab='company_info', $custom_form_num='')
    {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
         
        $data['provider'] = FProvider::find($id);
        $data['fuser'] = User::find($data['provider']->user_id);
        if($data['fuser'] == null) $data['fuser'] = new User;
        $data['zips'] = ProviderZip::where('provider_id',$data['provider']->id)->get();
        $data['pricing'] = ProviderPricingOptions::where('provider_id',$data['provider']->id)->first();
        $data['provider_files'] = ProviderFiles::where('provider_id', $data['provider']->id)->get();
        $data['provider_products'] = ProviderProducts::where('provider_id', $data['provider']->id)->get();
        if(count($data['provider_products'])<1)$data['provider_products'] = Products::get();
        //dd($data['provider_products']);
        $data['provider_plan_basic'] = ProviderPlans::where('id','1')->first();
        $data['provider_plan_premium'] = ProviderPlans::where('id','2')->first();
        $data['custom_form_num'] = $custom_form_num;
        $data['current_tab'] = $current_tab;
        
        
        
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
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
        
        $input = Input::all();
        //dd($input['provider']['id']);
        $provider = FProvider::find($input['provider']['id']);
        //dd($provider);
        if($provider == null){
            $provider = new FProvider();  
        } 
        
        if(array_key_exists('freshbooks_clients_enabled',$input)){            
            if($input['freshbooks_clients_enabled']=='1')$provider->freshbooks_clients_enabled = 1;
            else $provider->freshbooks_clients_enabled = 0;
        }        
        else $provider->freshbooks_clients_enabled = 0; 
        
        if(array_key_exists('freshbooks_clients_invoice',$input)){            
            if($input['freshbooks_clients_invoice']=='1')$provider->freshbooks_clients_invoice = 1;
            else $provider->freshbooks_clients_invoice = 0;
        }        
        else $provider->freshbooks_clients_invoice = 0; 
        
        if(array_key_exists('freshbooks_clients_people',$input)){            
            if($input['freshbooks_clients_people']=='1')$provider->freshbooks_clients_people = 1;
            else $provider->freshbooks_clients_people = 0;
        }        
        else $provider->freshbooks_clients_people = 0; 
            
        if($provider->plan_id != $input['provider']['plan_id']){
            $provider->plan_id = $input['provider']['plan_id'];
            $provider->save();
            
            if(is_object(Session::get('provider')))$mail_data['provider'] = Session::get('provider');
            else {
                $provider_id = Session::get('provider_id')==''?1:Session::get('provider_id');
                $mail_data['provider'] = FProvider::find($provider_id);
            }
            Mail::send('emails.provider-change-plan', $mail_data, function($message) use($mail_data)
            {
                $message->subject('A Provider Has Upgrade Their ForCremation Plan');
                $message->to('forcremation@gmail.com');
            });
            
            //AdminController::postAddRecurringBillingToClient($input['provider']['id'], false);
        }
        
        
        //CREATE FRESHBOOKS ENTRY
        
        if($provider->provider_status != $input['provider']['provider_status'] && $input['provider']['provider_status']=='1')$create_billing = true;
        else $create_billing = false;
        
        if($provider->freshbooks_billing_cost != $input['provider']['freshbooks_billing_cost'])$update_billing = true;
        else $update_billing = false;
        
        
        
        $provider->fill($input['provider']);
        $provider->save();

        
        if($create_billing)
        {
            $client_id = AdminController::postAddBillingClient($provider->id);
            if($client_id!='')AdminController::postAddRecurringBillingToClient($provider->id, false);
        }
        if($update_billing){
            if($input['provider']['provider_status']=='1')AdminController::postAddRecurringBillingToClient($provider->id);
        }
        
        if($input['provider_login']!=""){
            $user = User::find($provider->user_id);
            if($user == null)$user = new User;
            $user->email = $input['provider_login']; 
            $user->activated = 1;
            //$user->role = "provider";
            $user->save();
            //dd($user);
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


        $provider = FProvider::find($input['provider']['id']);
        $provider->fill($input['provider']);
        $provider->save();


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
                    if($zip!=null)$zip->delete();
                }
            }
            Session::flash('success','Zips Updated Successfully');

            return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id'],'tab'=>'provider_zips'));
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
            return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id'],'tab'=>'provider_pricing'));
    }

    public function postUpdateProviderForms(){
        $input = Input::all();
        $provider = FProvider::find($input['provider']['id']);
        //dd($input);
        
        $provider->fill($input['provider']);
        if(!array_key_exists('change_form',$input)){
            $provider->save();
            Session::flash('success','Provider\'s Custome Documents has been updated');
        }
        
        if(array_key_exists('edit_custom_form',$input))$key = $input['edit_custom_form'];
        else $key = '1';
        
        
        return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id'],'tab'=>'customer_document_forms','custom_form_num'=>$key));    
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
        return Redirect::action('AdminController@getEditProvider', array('id' => $provider_id,'tab'=>'provider_files'));
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
        return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id'],'tab'=>'provider_files'));
    }
    
    
    public function postUpdateProviderUrns()
    {        
        $input = Input::all();
        $provider = FProvider::find($input['provider']['id']);
        
        //dd($input['product']);
        foreach($input['product'] as $id=>$product){
            //dd($product);
            $provider_products = ProviderProducts::where('provider_id', $provider->id)->where('id', $id)->first();
            //dd($provider_products);
            if($provider_products == null || count($provider_products)<1)$provider_products = new ProviderProducts();
            //dd($provider_products);
             
            $provider_products->provider_id = $provider->id;
            $provider_products->fill($product);
            $provider_products->save();
        }

        Session::flash('success','Provider\'s Custome Documents has been updated');
        return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id'],'tab'=>'provider_urns')); 
    }

    /*
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
            
            AdminController::postAddRecurringBillingToClient($input['provider']['id'], false);
                    
            Session::flash('success','Billing and Freshbooks have been updated');
            return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id']));
    }*/
    
    

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
        $admin_settings = AdminSetting::where('setting_type','provider_registration_letter')->first();
        $data['admin_settings']['provider_registration_letter'] = $admin_settings->setting_value;
        
        $admin_settings = AdminSetting::where('setting_type','provider_registration_message')->first();
        $data['admin_settings']['provider_registration_message'] = $admin_settings->setting_value;
        
        $this->layout->content = View::make('admin.setting', $data);
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
    public function postAdminSetting()
    {
        $input = Input::all();
        if(is_array($input['admin_settings'])){
            foreach($input['admin_settings'] as $key=>$value){
                $setting = AdminSetting::where('setting_type',$key)->first();
                $setting->setting_value = $value;
                $setting->save();
            }            
        }
        
        return Redirect::action('AdminController@getSetting');
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
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
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
        $data['clients'] = $clients;
        $data['provider'] = FProvider::where('user_id',Sentry::getUser()->id)->first();
        $data['providers'] = FProvider::orderBy('business_name','asc')->get();
        $this->layout->content = View::make('admin.customers',$data);

    }


    public function getEditClient($id)
    {
        Session::put('client_id',$id);

        return Redirect::action('ClientController@getSteps');

    }

    public function getNewclient()
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


        // Find the group using the group id
        $clientGroup = Sentry::findGroupById(3);
        $user->addGroup($clientGroup);

        $clientController = new ClientController();
        $client = $clientController->fillOutClientTables($client);

        return Response::json(array('client_id' => $client->id));
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




    /*
    * FUNERAL HOME MANAGEMENT
    * 
    * 
    */
    public function getFuneralhomes()
    {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
        if(Sentry::getUser()->role=='provider') {
            $provider = FProvider::where('user_id',Sentry::getUser()->id)->first();
            return Redirect::action('AdminController@getEditProvider', array('id'=>$provider->id));
        }
        $q = trim(Input::get('q'));
        $city = trim(Input::get('city'));
        $state = trim(Input::get('state'));


        $states_r['Alabama'] = 'AL';
        $states_r['Alaska'] = 'AK';
        $states_r['Arizona'] = 'AZ';
        $states_r['Arkansas'] = 'AR';
        $states_r['California'] = 'CA';
        $states_r['Colorado'] = 'CO';
        $states_r['Connecticut'] = 'CT';
        $states_r['Delaware'] = 'DE';
        $states_r['District of Columbia'] = 'DC';
        $states_r['Florida'] = 'FL';
        $states_r['Georgia'] = 'GA';
        $states_r['Hawaii'] = 'HI';
        $states_r['Idaho'] = 'ID';
        $states_r['Illinois'] = 'IL';
        $states_r['Indiana'] = 'IN';
        $states_r['Iowa'] = 'IA';
        $states_r['Kansas'] = 'KS';
        $states_r['Kentucky'] = 'KY';
        $states_r['Louisiana'] = 'LA';
        $states_r['Maine'] = 'ME';
        $states_r['Maryland'] = 'MD';
        $states_r['Massachusetts'] = 'MA';
        $states_r['Michigan'] = 'MI';
        $states_r['Minnesota'] = 'MN';
        $states_r['Mississippi'] = 'MS';
        $states_r['Missouri'] = 'MO';
        $states_r['Montana'] = 'MT';
        $states_r['Nebraska'] = 'NE';
        $states_r['Nevada'] = 'NV';
        $states_r['New Hampshire'] = 'NH';
        $states_r['New Jersey'] = 'NJ';
        $states_r['New Mexico'] = 'NM';
        $states_r['New York'] = 'NY';
        $states_r['North Carolina'] = 'NC';
        $states_r['North Dakota'] = 'ND';
        $states_r['Ohio'] = 'OH';
        $states_r['Oklahoma'] = 'OK';
        $states_r['Oregon'] = 'OR';
        $states_r['Pennsylvania'] = 'PA';
        $states_r['Rhode Island'] = 'RI';
        $states_r['South Carolina'] = 'SC';
        $states_r['South Dakota'] = 'SD';
        $states_r['Tennessee'] = 'TN';
        $states_r['Texas'] = 'TX';
        $states_r['Utah'] = 'UT';
        $states_r['Vermont'] = 'VT';
        $states_r['Virginia'] = 'VA';
        $states_r['Washington'] = 'WA';
        $states_r['West Virginia'] = 'WV';
        $states_r['Wisconsin'] = 'WI';
        $states_r['Wyoming'] = 'WY';

        
        if(strlen($q)>=2 or $city != '' or $state != '')
        {
            Session::put('fh_q', $q);
            Session::put('fh_state', $state);
            Session::put('fh_city', $city);

            $FuneralHomes = FuneralHomes::orWhere(function($query) use ($q) {
                $query->orWhere('biz_name', 'like', '%' . $q . '%')->orWhere('biz_email', 'like', '%' . $q . '%')
                    ->orWhere('biz_phone', 'like', '%' . $q . '%')->orWhere('e_postal', 'like', '%' . $q . '%')->orWhere('web_meta_title', 'like', '%' . $q . '%');
                });

            if($city != '')$FuneralHomes = $FuneralHomes->where('e_city','like','%'.$city.'%');
            if($state != ''){
                $FuneralHomes = $FuneralHomes->where(function($query) use ($state,$states_r) {
                    $query->orWhere('e_state', 'like', '%' . $state . '%');
                    if(strlen($state)>2){
                        @$this_state = $states_r[ucwords($state)];
                        $query->orWhere('e_state', 'like', '%' . $this_state . '%');
                    }

                });
            }

            if($FuneralHomes == null){
                $FuneralHomes = FuneralHomes::orderBy('biz_name', 'asc');
            }

        }
        else
        {
            $FuneralHomes = FuneralHomes::orderBy('biz_name', 'asc');
        }

        $data['funeral_homes'] = $FuneralHomes->orderBy('biz_name', 'asc')->withTrashed()->paginate(100);
        //echo '<pre>'; dd(DB::getQueryLog()); echo '</pre>';

        $this->layout->content = View::make('admin.funeralhomes', $data);		
    } 

    public function getNewFuneralhome()
    {
        //$input = Input::all();
        $data['funeralhome'] = new FuneralHomes();

        $this->layout->content = View::make('admin.funeralhome-edit', $data);
    }

    public function getEditFuneralhome($id)
    {
            $data['funeralhome'] = FuneralHomes::find($id);
            if($data['funeralhome']==null){
                $data['funeralhome'] = new FuneralHomes();
                $data['funeralhome']['id'] = -1;
            }
            $this->layout->content = View::make('admin.funeralhome-edit',$data);
    }


    public function  getDeleteFuneralhome($id){
        $funeralhome = FuneralHomes::find($id);    
        if($funeralhome!=null){
            $funeralhome->status = 2;
            $funeralhome->save();
            $funeralhome->delete();
        }
        Session::flash('success','Funeral Home Soft Deleted Successfully');
        return Redirect::action('AdminController@getFuneralhomes');
    }

    public function  getUnDeleteFuneralhome($id){
        $funeralhome = FuneralHomes::withTrashed()->find($id);
        if($funeralhome!=null){
            $funeralhome->status = 0;
            $funeralhome->save();
            $funeralhome->restore();
            Session::flash('success','Funeral Home UnDeleted Successfully');
        }
        return Redirect::action('AdminController@getFuneralhomes');
    }


    public function postUpdateFuneralhome()
    {
            $input = Input::all();
            //dd($input['provider']['id']);
            $funeralhome = FuneralHomes::find($input['funeralhome']['id']);
            //dd($provider);
            if($funeralhome == null){
                $funeralhome = new FuneralHomes();  
            } 
            $funeralhome->fill($input['funeralhome']);
            $funeralhome->save();


            //dd($provider);

            if($funeralhome->status=='2'){
                $funeralhome->delete();
                Session::flash('success','Funeral Home Soft Deleted Successfully');
                return Redirect::action('AdminController@getFuneralhomes');
            }

            Session::flash('success','Funeral Home\'s Data has been updated');
            return Redirect::action('AdminController@getEditFuneralhome', array('id' => $funeralhome->id));
            //return Redirect::action('AdminController@getEditProvider');

    }


    public function  postMassUpdateFuneralHomes(){
        $funeralhomes_r = Input::get('edit_funeralhomes');
        $mass_edit_type = Input::get('mass_edit_type');
        
        if(is_array($funeralhomes_r))
        foreach($funeralhomes_r as $key=>$funeralhome_id){
            if($mass_edit_type == 'delete')AdminController::getDeleteFuneralhome($funeralhome_id,false);
            if($mass_edit_type == 'undelete' || $mass_edit_type == 'active')AdminController::getUnDeleteFuneralhome($funeralhome_id,false);
        }

        Session::flash('success',count($funeralhomes_r).' Funeral Homes Updated Successfully');
        return Redirect::action('AdminController@getFuneralhomes');
    }
   
    
    
    
    
    /*
    * BILLING MANAGEMENT
    * 
    * FRESHBOOKS API
     * API URl: https://forcremationcom.freshbooks.com/api/2.1/xml-in
     * Token: 95cad39d382f8bc4ae2d2a2a119e6559
     * Docs: http://developers.freshbooks.com/docs/clients/
     * 
    */
    
    public function getBillingPage()
    {
        /*
        // Method names are the same as found on the freshbooks API
        $fb = new Freshbooks\FreshBooksApi('client.list');

        // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
        $fb->post(array(
            //'email' => 'some@email.com'
        ));

        $fb->request();

        if($fb->success()) {
            //echo 'successful! the full response is in an array below<pre>';
            //dd($fb->getResponse());
        } else {
            echo $fb->getError();
            //var_dump($fb->getResponse());
        }
        */
        $plan_basic = ProviderPlans::where('id', 1)->first();
        $plan_premium = ProviderPlans::where('id', 2)->first();
        
        $this->layout->content = View::make('admin.billing', ['plan_basic'=>$plan_basic,'plan_premium'=>$plan_premium]);
    }
    
    /*
    public function postUpdateDefaultBilling()
    {
            $input = Input::all();
            $rules = ['monthly_fee'=>'required'];
            $v = Validator::make($input,$rules);
            if($v->fails()) return Redirect::back()->withErrors($v);

            $bill = Billing::first();
            $bill->monthly_fee = $input['monthly_fee'];
            $bill->lead_fee = $input['lead_fee'];
            $bill->save();
            Session::flash('success','Billing Setting has been updated');
            return Redirect::action('AdminController@getBillingPage', ['plan_basic'=>$plan_basic,'plan_premium'=>$plan_premium]);
    } */
    
    public function postAddBillingClient($provider_id)
    {
       
        $provider = FProvider::find($provider_id);
        
        if($provider != null){
            $fb = new Freshbooks\FreshBooksApi('client.create');

            // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
            $fb->post(array('client'=>
                array(
                    'first_name' => $provider->business_name,
                    'last_name' => "",
                    'email' => $provider->email,
                     //'username' => $provider->email,
                    'work_phone'=>$provider->phone,
                    'fax'=>$provider->fax,
                    'p_street1'=>$provider->address,
                    'p_city'=>$provider->city,
                    'p_state'=>$provider->state,
                    'p_country'=>'United States',
                    'p_code'=>$provider->zip
                    )
                )
            );
            //dd($fb->getGeneratedXML()); // You can view what the XML looks like that we're about to send over the wire
            
            $fb->request();

            if($fb->success()) {
                Session::flash('success','Successful!ly created Freshbooks entry');
                //dd($fb->getResponse());
                $res = $fb->getResponse();
                $client_id = $res['client_id'];
                $provider->freshbooks_client_id = $client_id;
                $provider->save();
                //freshbooks_client_id
                //freshbooks_recurring_id
                return $client_id;
            } else {
                echo $fb->getError();
                Session::flash('error','Errors Creating Freshbooks Entry');

                var_dump($fb->getResponse());
            }
            
 
        } 
        else Session::flash('Issue Finding Provider To Create Freshbooks Entry');
                    

    }
    
    
    function postAddRecurringBillingToClient($provider_id, $create_new=true)
    {
        $provider = FProvider::find($provider_id);
        
        //$bill = Billing::first();
        
        
        if($provider == null)return;
        
        $billing_plan = ProviderPlans::where('id', $provider->plan_id)->first();
        if($billing_plan==null)$billing_plan = ProviderPlans::where('id',1)->first();
        
        if($provider->freshbooks_client_id=='' || $provider->freshbooks_client_id=='0'){
            $provider->freshbooks_client_id = AdminController::postAddBillingClient($provider_id);
            //$provider = FProvider::find($provider->id);
        }
        $billing_plan->price = $provider->freshbooks_billing_cost;
        
        if($provider->freshbooks_client_id!=''){
                
                if($provider->freshbooks_recurring_id=='0' || $provider->freshbooks_recurring_id=='')$create_new = true;
                else $create_new = false;
                
                if($create_new){
                    
                    $fb = new Freshbooks\FreshBooksApi('recurring.create');
                    // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
                    $fb->post(
                        array('recurring'=>
                            array(
                               'client_id' => $provider->freshbooks_client_id,
                               'date' => date("Y-m-d"),
                               'occurrences' => 0,
                               'frequency'=>'monthly',
                               //'terms'=>$bill->terms,
                               'lines'=> array(
                                   'line' => array(
                                        'name'=>'Monthly',
                                        'description'=>'Charge for the month of ::month::',
                                        'unit_cost'=>$billing_plan->price,
                                        'quantity'=>'1',
                                        'tax1_name'=>'',
                                        'tax2_name'=>'',
                                        'tax1_percent'=>'',
                                        'tax2_percent'=>''
                                    )
                                )
                            )
                        )
                    );
                }
                else {
                    //$providerBilling = ProviderBilling::where('provider_id', $provider_id)->first();
                   
                    $fb = new Freshbooks\FreshBooksApi('recurring.update');
                     // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
                    $fb->post(
                        array('recurring'=>
                            array(
                               'recurring_id' => $provider->freshbooks_recurring_id,
                               'client_id' => $provider->freshbooks_client_id,
                               'date' => date("Y-m-d"),
                               'occurrences' => 0,
                               'frequency'=>'monthly',
                               //'terms'=>$bill->terms,
                               'lines'=> array(
                                   'line' => array(
                                        'name'=>'Monthly',
                                        'description'=>'Charge for the month of ::month::',
                                        'unit_cost'=>$billing_plan->price,
                                        'quantity'=>'1',
                                        'tax1_name'=>'',
                                        'tax2_name'=>'',
                                        'tax1_percent'=>'',
                                        'tax2_percent'=>''
                                    )
                                )
                            )
                        )
                    );
                }
               
                //dd($fb->getGeneratedXML()); // You can view what the XML looks like that we're about to send over the wire

                $fb->request();

                if($fb->success()) {
                    Session::flash('success','Successful!ly Set Recurring Freshbooks entry');
                    //dd($fb->getResponse());
                    
                    $res = $fb->getResponse();
                    //dd($res);
                    if($create_new){
                        $recurring_id = $res['recurring_id'];

                        $provider->freshbooks_recurring_id = $recurring_id;
                        $provider->save();
                    }

                } else {
                    //echo $fb->getError();
                    Session::flash('error','Errors Setting Freshbooks Recurring Entry, Has the Person or Recurring Invoice Been Deleted From Freshbooks?<br />'
                            . 'The Freshbook Integration Has been Reset for this client, the next time you change plans and save it will create a new setup in freshbooks for them. '
                            . 'Make sure to login to Freshbooks to double check everything is ok and no duplicates exist '.$fb->getError());
                    
                    $provider->freshbooks_recurring_id = '';
                    $provider->freshbooks_client_id = '';
                    $provider->save();
                    //dd($fb->getResponse());
                }

        }
        
    }
    
    
    
    public function postUpdateProviderPlans()
    {
            $input = Input::all();
            $rules = ['plan_basic'=>'required', 'plan_premium'=>'required'];
            
            $v = Validator::make($input['provider_plans'],$rules);
            if($v->fails()) return Redirect::back()->withErrors($v);

            $plan_basic = ProviderPlans::where('id', 1)->first();
            $plan_basic->price = $input['provider_plans']['plan_basic'];
            $plan_basic->save();
            
            $plan_premium = ProviderPlans::where('id', 2)->first();
            $plan_premium->price = $input['provider_plans']['plan_premium'];
            $plan_premium->save();
            
            Session::flash('success','Plans have been updated');
            return Redirect::action('AdminController@getBillingPage', ['plan_basic'=>$plan_basic,'plan_premium'=>$plan_premium]);
    }
    
    



    
}





