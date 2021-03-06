<?php

/* FRESHBOOKS API */
#$domain = 'forcremationcom'; // https://your-subdomain.freshbooks.com/
#$token = '95cad39d382f8bc4ae2d2a2a119e6559'; // your api token found in your account
#Freshbooks\FreshBooksApi::init($domain, $token);



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

            if(Input::get('include_deleted')==1)$providers = FProvider::where('email','like','%'.$q.'%')->where('admin_provider',0)->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%')->withTrashed();
            else $providers = FProvider::where('email','like','%'.$q.'%')->where('admin_provider',0)->orWhere('zip','like','%'.$q.'%')->orWhere('city','like','%'.$q.'%')->orWhere('business_name','like','%'.$q.'%');
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
        $providers = $providers->where('admin_provider',0)->orderBy('created_at', 'desc')->paginate($per_page);

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


    public function getRedirectCallback($test='y')
    {
        //dd(Input::all());
        Log::info("Callback called: ".implode(Input::all()));
        dd('test');
    }


    public function getEditProvider($id, $current_tab='company_info', $custom_form_num='')
    {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');

        if(!is_numeric($id))$id = Input::get('id');

        if($current_tab == '' || Input::get('current_tab')!='')$current_tab = Input::get('current_tab');

        $data['provider'] = FProvider::find($id);
        #dd($id );
        $data['fuser'] = User::find($data['provider']->user_id);
        if($data['fuser'] == null) $data['fuser'] = new User;
        $data['zips'] = ProviderZip::where('provider_id',$data['provider']->id)->orderBy('zip')->get();
        $data['states'] = State::orderBy('name_long')->get();
        $data['pricing'] = ProviderPricingOptions::where('provider_id',$data['provider']->id)->first();
        $data['provider_files'] = ProviderFiles::where('provider_id', $data['provider']->id)->where('file_type','not like','provider_logo')->where('file_type','not like','provider_slide_%')->get();
        $provider_homepage_files = ProviderFiles::where('provider_id', $data['provider']->id)->where(function($query)
                {
                    $query->where('file_type','like','provider_logo')
                          ->orWhere('file_type','like','provider_slide_%');
                })->get();

        $data['provider_logo'] = ProviderFiles::where('provider_id', $data['provider']->id)->where('file_type','like','provider_logo')->first();
        $data['provider_products'] = ProviderProducts::where('provider_id', $data['provider']->id)->get();
        if(count($data['provider_products'])<1)$data['provider_products'] = Products::get();
        //dd($data['provider_products']);
        $data['provider_plan_basic'] = ProviderPlans::where('id','1')->first();
        $data['provider_plan_premium'] = ProviderPlans::where('id','2')->first();
        $data['custom_form_num'] = $custom_form_num;
        $data['current_tab'] = $current_tab;


        $data['signature_docs'] =  AdminController::getListProviderSignatureDocs($id);

        $this_zip = Zip::where('zip',$data['provider']->zip)->first();
        if($this_zip!=null)$data['zip_info'] = AdminController::findZipsInRadius($data['provider']->provider_radius, $this_zip->latitude, $this_zip->longitude);
        else $data['zip_info'] = null;

        if($provider_homepage_files != null and count($provider_homepage_files)>0) {
            foreach ($provider_homepage_files as $key => $file) {
                $data['provider_homepage_files'][$file->file_type] = $file;
            }
        }
        else $data['provider_homepage_files'] = array();
        //echo '<pre>';dd($data);
        //$data['clients'] = AdminController::getProviderCustomers($id);
        $data['invoiced_clients'] = AdminController::getProviderCustomers($id, true);

        //dd($data['clients']);

        $this->layout->content = View::make('admin.provider-edit',$data);
    }

    public function getZipsByLocation()
    {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');

        $zip = Input::get('zip');
        $radius = Input::get('radius');

        $this_zip = Zip::where('zip',$zip)->first();

        if($this_zip!=null)$zips_r = AdminController::findZipsInRadius($radius, $this_zip->latitude, $this_zip->longitude);
        else $zips_r = null;

        if($zips_r!=null){
            foreach($zips_r as $key=>$zip){
                $zip->distance = round($zip->distance,2);
                $zips_r[$key] = $zip;
            }
        }

        return $zips_r;
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
            Session::flash('success','Provider\'s Customer Documents has been updated');
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
        $tab = Request::segment(5)!='' ? Request::segment(5):'provider_files';
        Session::flash('success','File Removed Successfully');
        return Redirect::action('AdminController@getEditProvider', array('id' => $provider_id,'tab'=>$tab));
    }

    public function postUpdateFiles()
    {
        $input = Input::all();

        //PROVIDER FILES
        if (array_key_exists('provider_files_new', $input)) {
            //dd($input['provider_files']);
            $file = Input::file('provider_files_new');
            //dd($file);
            $destinationPath = public_path() . "/provider_files/" . $input['provider']['id'];
            $file->move($destinationPath, $file->getClientOriginalName());

            $name = $file->getClientOriginalName();

            $provider_file = null;
            if($input['provider_files_type'] != 'client_printable')$provider_file = ProviderFiles::where('provider_id', $input['provider']['id'])->where('file_type', $input['provider_files_type'])->first();
            if ($provider_file == null || $provider_file == "") $provider_file = new ProviderFiles();
            $provider_file->provider_id = $input['provider']['id'];
            $provider_file->file_name = $name;
            $provider_file->file_type = $input['provider_files_type'];
            $provider_file->save();
        }

        //PROVIDER LOGO
        if (array_key_exists('provider_logo', $input)) {
            //dd($input['provider_files']);
            $file = Input::file('provider_logo');
            //dd($file);
            $destinationPath = public_path() . "/provider_files/" . $input['provider']['id'];
            $file->move($destinationPath, 'logo.png');

            //$name = $file->getClientOriginalName();

            $provider_file = ProviderFiles::where('provider_id', $input['provider']['id'])->where('file_type', 'provider_logo')->first();
            if ($provider_file == null || $provider_file == "") $provider_file = new ProviderFiles();
            $provider_file->provider_id = $input['provider']['id'];
            $provider_file->file_name = 'logo.png';
            $provider_file->file_type = 'provider_logo';
            $provider_file->save();
        }


        //SLIDESHOW IMAGES
        for ($x = 1; $x <= 3; $x++){

            if (array_key_exists('provider_slide_'.$x, $input)){
                $file = Input::file('provider_slide_'.$x);
                if($file != null) {
                    $destinationPath = public_path() . "/provider_files/" . $input['provider']['id'];
                    $file_name = 'provider_slide_' . $x.'.'.$file->getClientOriginalExtension();
                    $file->move($destinationPath, $file_name);

                    $provider_file = ProviderFiles::where('provider_id', $input['provider']['id'])->where('file_type', 'provider_slide_' . $x)->first();
                    if ($provider_file == null || $provider_file == "") $provider_file = new ProviderFiles();
                    $provider_file->provider_id = $input['provider']['id'];
                    $provider_file->file_name = $file_name;
                    $provider_file->file_type = 'provider_slide_' . $x;
                    $provider_file->save();
                }
            }

        }
        $tab = $input['tab'];

        Session::flash('success','Provider Files Added Successfully');
        return Redirect::action('AdminController@getEditProvider', array('id' => $input['provider']['id'],'tab'=>$tab));
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

        Session::flash('success','Provider\'s Customer Documents has been updated');
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
    public function getProviderCustomers($provider_id, $invoiced_only=false)
    {
        Session::put('client_providers_id',$provider_id);
        $per_page = 1000;
        $clients = Client::with('user')
                ->join('clients_providers', 'clients_providers.client_id', '=', 'clients.id');


        if($invoiced_only)$clients->whereRaw("clients.fb_invoice_id !=''");

        if((Sentry::getUser()->role=='provider' && Session::get('logged_in_provider_id')!='') || Input::get('provider_id')!=''){
            $this_provider_id = Input::get('provider_id') != '' ? Input::get('provider_id') : Session::get('logged_in_provider_id');
            $clients->where('clients_providers.provider_id', $this_provider_id);
        }




        $clients->orderBy('clients.created_at', 'desc');
        $clients = $clients->paginate($per_page);
        $clients->setBaseUrl('provider_clients');

        //$clients = $clients->get();

        foreach($clients as $client){
            //$provider_id = Session::get('provider_id');
            //if($provider_id!='')$client->provider = DB::table('clients_providers')->where('client_id', $client->id)->where('provider_id', $provider_id)->first();
            //else $client->provider = DB::table('clients_providers')->where('client_id', $client->id)->first();
            //if($client->client_provider_id != null)$client->provider = FProvider::find($client->client_provider_id);


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
        /*
        $q = Input::get('q');
        if(strlen($q)>=3)
        {
            $clients = Client::select('clients.*')
                    ->join('deceased_info', function($join)
                    {
                        $join->on('clients.id', '=', 'deceased_info.client_id');
                    })
                    ->where('clients.zip','like','%'.$q.'%')->orWhere('clients.phone','like','%'.$q.'%')->orWhere('clients.state','like','%'.$q.'%')
                    ->orWhere('clients.address','like','%'.$q.'%')->orWhere('legal_name','like','%'.$q.'%')
                    ->orWhere('clients.zip','like','%'.$q.'%')->orWhere('clients.city','like','%'.$q.'%')
                    ->orWhere('clients.first_name','like','%'.$q.'%')->orWhere('clients.last_name','like','%'.$q.'%')
                    ->orWhere('clients.created_at','like','%'.$q.'%')
                    ->orWhere('deceased_info.first_name','like','%'.$q.'%')->orWhere('deceased_info.last_name','like','%'.$q.'%')
                    ->orWhere('deceased_info.city','like','%'.$q.'%')->orWhere('deceased_info.state','like','%'.$q.'%')
                    ->orWhere('deceased_info.address','like','%'.$q.'%')->orWhere('deceased_info.phone','like','%'.$q.'%')
                    ->orWhere('deceased_info.zip','like','%'.$q.'%');


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

            //$clients = $clients->whereRaw("clients_providers.client_id = clients.id and clients_providers.provider_id='".Session::get('logged_in_provider_id')."'");
            $clients->whereExists(function($query)
            {
                $query->select(DB::raw(1))
                      ->from('clients_providers')
                      ->whereRaw("clients_providers.client_id = clients.id and clients_providers.provider_id='".Session::get('logged_in_provider_id')."'");
            });
        }
        $clients = $clients->orderBy('clients.created_at', 'desc')->get(); //->paginate($per_page);
        //$queries = DB::getQueryLog();
        //print_r( end($queries)); 


        foreach($clients as $client){
            $provider_id = Session::get('provider_id');
            if($provider_id!='')$client->provider = DB::table('clients_providers')->where('client_id', $client->id)->where('provider_id', $provider_id)->first();
            else $client->provider = DB::table('clients_providers')->where('client_id', $client->id)->first();

            $this_clients_provider_id = DB::table('clients_providers')->where('client_id', $client->id)->first();
            //dd($this_clients_provider_id);
            if($this_clients_provider_id != null)$client->FProvider = FProvider::find($this_clients_provider_id->provider_id);
            //else dd($client->FProvider );
            $client_DeceasedInfo = DeceasedInfo::where('client_id', $client->id)->first();
            if($client_DeceasedInfo!=null){
                $client->deceased_first_name = $client_DeceasedInfo->first_name;
                $client->deceased_last_name = $client_DeceasedInfo->last_name;
                if($client_DeceasedInfo->cremation_reason == "planning_for_future")$client->preneed = "y";
                else $client->preneed = "n";
            }
            else $client->preneed = "n";

        }
        $data['clients'] = $clients;*/
        $data['provider'] = FProvider::where('user_id',Sentry::getUser()->id)->first();
        $data['providers'] = FProvider::where('deleted_at','=',NULL)->where('admin_provider',0)->orderBy('business_name','asc')->get();
        $this->layout->content = View::make('admin.customers',$data);

    }


    public function getCustomerList()
    {
        if(!Sentry::getUser())return Redirect::action('UserController@getLogout');
        $per_page = 25;
        $return_data = null;
        $order['column'] = '4';
        $order['dir'] = 'desc';
        $total_count = 0;

        if(Input::get('length')){
            $per_page = Input::get('length');
        }
        if(Input::get('order')){
            $order_r = Input::get('order');
            if($order_r[0]['column'] != '0') {
                $order['column'] = $order_r[0]['column'];
                $order['dir'] = $order_r[0]['dir'];
            }
        }

        $cols_r = Array('0'=>'',
                            '1'=>'client_first_name',
                            '2'=>'deceased_first_name',
                            '3'=>'clients.phone',
                            '4'=>'client_created',
                            '5'=>'client_created',
                            '6'=>'users.email',
                            '7'=>'clients.status',
                            '8'=>'clients.first_name'
            );


        $search_r = Input::get('search');
        $q = $search_r['value'];

        if(strlen($q)>=3)
        {
            $clients = Client::select(DB::raw('clients.*, clients.created_at as client_created, 
                                                clients.first_name as client_first_name, clients.last_name as client_last_name,                                                 
                                                deceased_info.first_name as deceased_first_name, 
                                                deceased_info.last_name as deceased_last_name, 
                                                users.email,
                                                clients_providers.provider_id as client_provider_id'))
                ->join('deceased_info', 'clients.id', '=', 'deceased_info.client_id')
                ->join('users', 'clients.user_id', '=', 'users.id')
                ->join('clients_providers', 'clients_providers.client_id', '=', 'clients.id')

                ->where('clients.phone','like','%'.$q.'%')->orWhere('clients.state','like','%'.$q.'%')
                ->orWhere('clients.address','like','%'.$q.'%')->orWhere('legal_name','like','%'.$q.'%')
                ->orWhere('clients.zip','like','%'.$q.'%')->orWhere('clients.city','like','%'.$q.'%')
                ->orWhere('clients.first_name','like','%'.$q.'%')->orWhere('clients.last_name','like','%'.$q.'%')
                ->orWhere('clients.created_at','like','%'.$q.'%')
                ->orWhere('deceased_info.first_name','like','%'.$q.'%')->orWhere('deceased_info.last_name','like','%'.$q.'%')
                ->orWhere('deceased_info.city','like','%'.$q.'%')->orWhere('deceased_info.state','like','%'.$q.'%')
                ->orWhere('deceased_info.address','like','%'.$q.'%')->orWhere('deceased_info.phone','like','%'.$q.'%')
                ->orWhere('deceased_info.zip','like','%'.$q.'%');
                //->orWhere('cremains_info.shipto_email','like','%'.$q.'%')
                //->orWhere('cremains_info.shipto_first_name','like','%'.$q.'%');

            $clients_count = Client::select(DB::raw('count(*) as count'))
                ->join('deceased_info', 'clients.id', '=', 'deceased_info.client_id')
                ->join('users', 'clients.user_id', '=', 'users.id')
                ->join('clients_providers', 'clients_providers.client_id', '=', 'clients.id')

                ->where('clients.phone','like','%'.$q.'%')->orWhere('clients.state','like','%'.$q.'%')
                ->orWhere('clients.address','like','%'.$q.'%')->orWhere('legal_name','like','%'.$q.'%')
                ->orWhere('clients.zip','like','%'.$q.'%')->orWhere('clients.city','like','%'.$q.'%')
                ->orWhere('clients.first_name','like','%'.$q.'%')->orWhere('clients.last_name','like','%'.$q.'%')
                ->orWhere('clients.created_at','like','%'.$q.'%')
                ->orWhere('deceased_info.first_name','like','%'.$q.'%')->orWhere('deceased_info.last_name','like','%'.$q.'%')
                ->orWhere('deceased_info.city','like','%'.$q.'%')->orWhere('deceased_info.state','like','%'.$q.'%')
                ->orWhere('deceased_info.address','like','%'.$q.'%')->orWhere('deceased_info.phone','like','%'.$q.'%')
                ->orWhere('deceased_info.zip','like','%'.$q.'%');


            if($clients == null)$clients = Client::with('user');

        }

        else {
            //$clients = Client::with('user');
            $clients = Client::select(DB::raw('clients.*, clients.created_at as client_created, 
                                                clients.first_name as client_first_name, clients.last_name as client_last_name,                                                 
                                                deceased_info.first_name as deceased_first_name, 
                                                deceased_info.last_name as deceased_last_name, 
                                                users.email,
                                                clients_providers.provider_id as client_provider_id'))
                ->join('deceased_info', 'clients.id', '=', 'deceased_info.client_id')
                ->join('users', 'clients.user_id', '=', 'users.id')
                ->join('clients_providers', 'clients_providers.client_id', '=', 'clients.id');

                //->join('cremains_info', 'cremains_info.client_id', '=', 'clients.id');

            $clients_count = Client::select(DB::raw('count(*) as count'))
                                    ->join('deceased_info', 'clients.id', '=', 'deceased_info.client_id')
                                    ->join('clients_providers', 'clients_providers.client_id', '=', 'clients.id');

            if(Input::get('status')!="")
            {
                if(Input::get('status')==0 || Input::get('status')==1){
                    $clients->where('clients.status','like',Input::get('status'));
                    $clients_count->where('clients.status','like',Input::get('status'));
                }
                elseif(Input::get('status')==2){
                    $clients->where('clients.status','like','%')->withTrashed();
                    $clients_count->where('clients.status','like','%')->withTrashed();
                }
                elseif(Input::get('status')==3){
                    $clients->where('clients.status','like',3)->withTrashed();
                    $clients_count->where('clients.status','like',3)->withTrashed();
                }
            }
            if(Input::get('preneed')=="1") {
                $clients->where('deceased_info.cremation_reason','=','planning_for_future');
                $clients_count->where('deceased_info.cremation_reason','=','planning_for_future');
            }

        }

        if((Sentry::getUser()->role=='provider' && Session::get('logged_in_provider_id')!='') || Input::get('provider_id')!=''){
            $this_provider_id = Input::get('provider_id') != '' ? Input::get('provider_id') : Session::get('logged_in_provider_id');
            $clients->where('clients_providers.provider_id', $this_provider_id);
            $clients_count->where('clients_providers.provider_id', $this_provider_id);
        }

        $clients_count_o = $clients_count->first();
        $total_count = $clients_count_o->count;
        $clients = $clients->orderBy($cols_r[$order['column']], $order['dir'] )->paginate($per_page);
        //if(Input::get('preneed') == '1')$total_count = 0;

        $return_data = ["draw"=> Input::get('draw'), "recordsTotal"=> $total_count,  "recordsFiltered"=> $total_count, 'data'=>Array()];



        #DB::connection()->enableQueryLog();
        #$queries = DB::getQueryLog();
        #dd($queries);
        #dd($clients);
        $counter = 0;
        foreach($clients as $client){
            $provider_id = Session::get('provider_id');
            //if($provider_id!='')$client->provider = DB::table('clients_providers')->where('client_id', $client->id)->where('provider_id', $provider_id)->first();
            //else $client->provider = DB::table('clients_providers')->where('client_id', $client->id)->first();

            //$client->client_provider_id = DB::table('clients_providers')->where('client_id', $client->id)->first();
            //dd($this_clients_provider_id);
            if($client->client_provider_id != null)$client->FProvider = FProvider::find($client->client_provider_id);
            //else dd($client->FProvider );
            //$client_DeceasedInfo = $client->deceasedInfo;

            if($client->cremation_reason!=null){
                //$client->deceased_first_name = $client->DeceasedInfo_first_name;
                //$client->deceased_last_name = $client_DeceasedInfo->last_name;
                if($client->cremation_reason == "planning_for_future")$client->preneed = "y";
                else $client->preneed = "n";
            }
            else $client->preneed = "n";

                $status = '';
                switch($client->status){
                    case 0:$status =  '<span class="pull-right">Active';break;
                    case 1:$status =  '<span class="pull-right">Completed';break;
                    case 3:$status =  '<span class="pull-right">Deleted';break;
                }

                $client_DeceasedInfo = DeceasedInfo::where('client_id', $client->id)->first();
                if($client_DeceasedInfo!=null){
                    if($client_DeceasedInfo->cremation_reason == "planning_for_future")$client->preneed = "y";
                }

                if($client->preneed == "y")$status .=  '/Pre-Need';


                $status .=  '</span>';

                    if($client->status == 3)$action = '<a href="'.action('AdminController@getUnDeleteClient',$client->id).'" class="btn btn-xs btn-success pull-right" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> UnDelete</a>';
                    else $action = '<a href="'.action('AdminController@getDeleteClient',$client->id).'" class="btn btn-xs btn-danger pull-right" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> </a>';

                $action .= ' <a href="'. action('AdminController@getEditClient',$client->id) .'?no-frame=n" class="btn btn-xs btn-default pull-right" style="margin-right:10px;">
                                <span class="glyphicon glyphicon-pencil"></span> 
                                </a>';

                $client_name = $client->client_first_name.' '.$client->client_last_name;
                $decease_name = $client->deceased_first_name.' '.$client->deceased_last_name;

                $row_data = Array('<input type="checkbox" class="clients_mass_action" name="edit_clients['.$client->id.']" value="'.$client->id.'" />',
                                ($client_name!='' ? $client_name: ' '),
                                ($decease_name!='' ? $decease_name: ' '),
                                ($client->phone!='' ? $client->phone: ' '),
                                date('m/d/Y',strtotime($client->client_created)),
                                ($client->FProvider != null ? '<a href="'. action('AdminController@getEditProvider',$client->FProvider->id).'"> '.$client->FProvider->business_name.' </a>' : ''),
                                ($client->user != null ? $client->user->email : ''),
                                "$status",
                                "$action"
                            );


            $counter++;

            array_push($return_data['data'], $row_data);


        }


       return  $return_data;

    }



    public function getEditClient($id, $goto_section='')
    {
        Session::put('client_id',$id);

        return Redirect::action('ClientController@getSteps',$goto_section);

    }

    public function getNewclient()
    {
        //dd('creating user');
        //first_name
        //last_name
        //email
        //password
        //provider_id
        $first_name = "unregistered";
        $last_name = "unregistered";
        $password = "unregistered";

        if(Input::get('first_name')!='')$first_name = Input::get('first_name');
        if(Input::get('last_name')!='')$last_name = Input::get('last_name');
        if(Input::get('password')!='')$password = Input::get('password');
        if(Input::get('first_name')!='')$first_name = Input::get('first_name');
        if(Input::get('provider_id')!='')$provider_id = Input::get('provider_id');

        if(Input::get('email')!=''){
            $check = User::where('email',Input::get('email'))->first();
            if($check != null)return Response::json(['message'=>'A Client With That Email Exists Already']);
        }

        $client_input = Array(
            'first_name' => $first_name,
            'last_name' => $last_name
        );


        $client = new Client();
        $client->fill($client_input);
        $client->save();


        $client_provider = DB::table('clients_providers')->where('client_id', $client->id)->first();
        if(count($client_provider)>0){
            DB::table('clients_providers')->where('client_id', $client->id)->update(array('provider_id'=>$provider_id));
        }
        else DB::table('clients_providers')->insert(array('provider_id'=>$provider_id, 'client_id'=>$client->id));




        //dd( "creatd new user:".$client->id);

        $login = "unregistered".$client->id."@user.com";
        if(Input::get('email')!='')$login = Input::get('email');


        $user = Sentry::createUser(array(
            'email'     => $login,
            'password'  => $password,
            'role'  => 'client',
            'activated' => true
        ));

        $client->user_id = $user->id;
        $client->save();

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
            $token = $provider->freshbooks_api_token; // your api token found in your account
            $domain = $provider->freshbooks_api_url; // https://your-subdomain.freshbooks.com/
            $domain = str_replace('/','', str_replace('api/2.1/xml-in','', str_replace('.freshbooks.com','', str_replace('http://','', str_replace('https://','',$domain)))));
            $fb = new Freshbooks\FreshBooksApi($domain, $token);
            $fb->setMethod('client.create');

            // For complete list of arguments see FreshBooks docs at http://developers.freshbooks.com
            $fb->post(array('client'=>
                array(
                    'organization' => $provider->business_name,
                    'first_name' => $provider->contact_first_name,
                    'last_name' => $provider->contact_last_name,
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
                    $domain = $provider->freshbooks_api_url; // https://your-subdomain.freshbooks.com/
                    $token = $provider->freshbooks_api_token; // your api token found in your account
                    $domain = str_replace('/','', str_replace('api/2.1/xml-in','', str_replace('.freshbooks.com','', str_replace('http://','', str_replace('https://','',$domain)))));

                    $fb = new Freshbooks\FreshBooksApi($domain, $token);
                    $fb->setMethod('recurring.create');

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
                    $domain = $provider->freshbooks_api_url; // https://your-subdomain.freshbooks.com/
                    $token = $provider->freshbooks_api_token; // your api token found in your account
                    $domain = str_replace('/','', str_replace('api/2.1/xml-in','', str_replace('.freshbooks.com','', str_replace('http://','', str_replace('https://','',$domain)))));

                    $fb = new Freshbooks\FreshBooksApi($domain, $token);
                    $fb->setMethod('recurring.update');

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
                    Session::flash('success','Successfully Set Recurring Freshbooks entry');
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


    function getListProviderSignatureDocs($pid='', $cid='')
    {
        $tags = array();
        $rightsignature = new RightSignature();
        $rightsignature->debug = false;

        $docs = $rightsignature->getDocuments($cid,$pid);

        $xml = simplexml_load_string($docs, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
        if ($xml != false) {
            $json = json_encode($xml);
            $docs = json_decode($json,TRUE);;
        }
        #echo '<pre>';dd($docs);
        if(is_array($docs))
        if(@array_key_exists('documents',$docs))
        if(@array_key_exists('document', $docs['documents'])) {
            #echo '<pre>';dd($docs['documents']['document']);
            if (!array_key_exists('0', $docs['documents']['document'])) {
                $temp_doc = $docs['documents']['document'];
                $docs['documents']['document'] = array();
                $docs['documents']['document'][0] = $temp_doc;
            }
           # else dd('test');

            foreach ($docs['documents']['document'] as $key => $doc) {
                if(is_array($doc)) {
                    if (array_key_exists('tags', $doc)) {
                        if(is_array($doc['tags']))$tags = $doc['tags'];
                        else $tags = explode(',', $doc['tags']);
                        $cid = $doc_types = $doc_com = '';
                        foreach ($tags as $tag) {
                            if (strpos($tag, 'cid:') !== false) $cid = substr($tag, 4, strlen($tag));
                            if (strpos($tag, 'customer_form_') !== false) {
                                $doc_types .= $doc_com.substr($tag, strpos($tag,':')+1, strlen($tag));
                                $doc_com=', ';
                            }
                        }
                        if ($cid != '') $docs['documents']['document'][$key]['client'] = Client::find($cid);
                        else $docs['documents']['document'][$key]['client'] = new Client();
                        $docs['documents']['document'][$key]['doc_types'] = $doc_types;

                        if (!array_key_exists('signed-pdf-url', $docs['documents']['document'][$key]) || count($docs['documents']['document'][$key]['signed-pdf-url']) < 1) $docs['documents']['document'][$key]['signed-pdf-url'] = '';
                        if (!array_key_exists('pdf-url', $docs['documents']['document'][$key]) || count($docs['documents']['document'][$key]['pdf-url']) < 1) $docs['documents']['document'][$key]['pdf-url'] = '';
                    }
                    else {
                        #unset($docs['documents']['document'][$key]);
                    }
                }
                else {
                    #unset($docs['documents']['document'][$key]);
                }

            }
        }
        else $docs['documents']['document'] = array();

        #echo '<pre>';dd($docs['documents']['document'][0]);
        return $docs;
    }

    function getSignedDoc($doc_guid='')
    {

        if($doc_guid == '')$doc_guid = Input::get('doc_guid');
        $rightsignature = new RightSignature();
        $rightsignature->debug = false;

        $doc = $rightsignature->getSignDocument($doc_guid);
        $xml = simplexml_load_string($doc, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
        if ($xml != false) {
            $json = json_encode($xml);
            $doc = json_decode($json,TRUE);
        }
        #echo '<pre>';dd($doc);
        $recipient_details = '<h3>Recipients</h3><table><tr><td>Name</td><td>Role</td><td>Status</td><td>Email</td></tr>';
        if(is_array($doc)){
            if (!array_key_exists('0', $doc['recipients']['recipient'])) {
                $temp_doc = $doc['recipients']['recipient'];
                $doc['recipients']['recipient'] = array();
                $doc['recipients']['recipient'][0] = $temp_doc;
            }
            foreach($doc['recipients']['recipient'] as $recipient) {

                $recipient_details .= '<tr>';
                $recipient_details .= '<td>' . $recipient['name'] . '</td>';
                $recipient_details .= '<td>' . $recipient['role-id'] . '</td>';
                $recipient_details .= '<td>' . $recipient['state'] . '</td>';
                $recipient_details .= '<td><a href="mailto:' . $recipient['email'] . '">' . $recipient['email'] . '</a></td>';
                $recipient_details .= '</tr>';
            }
        }

        $recipient_details .= '</table>';

        $history = '<br /><h3>History</h3><div style="height:300px;overflow-y: scroll;border:1px solid #999;padding:5px 15px">';
        $history .= '<table style="width:100%"><thead><th style="width:95px;">Date</th><th>Message</th></thead>';

        if(is_array($doc)) {
            if (!array_key_exists('0', $doc['audit-trails']['audit-trail'])) {
                $temp_doc = $doc['audit-trails']['audit-trail'];
                $doc['audit-trails']['audit-trail'] = array();
                $doc['audit-trails']['audit-trail'][0] = $temp_doc;
            }
            foreach ($doc['audit-trails']['audit-trail'] as $trail) {
                if (array_key_exists('timestamp', $trail)) {
                    $history .= '<tr>';
                    $history .= '<td>' . date('m/d/Y h:i a', strtotime($trail['timestamp'] . ' - 7 hours')) . '</td>';
                    $history .= '<td valign=top>' . $trail['message'] . '</td>';
                    $history .= '</tr>';
                }

            }
        }
        $history .= '</table></div>';

        $recipient_details .= $history;

        return $recipient_details;
    }

    /**
     * @param string $doc_guid
     * Resend the right signature document
     */
    function getResendSignatureDocument($doc_guid = ''){
        //https://rightsignature.com/api/documents/ABCDEFGHIJKLMN0P/send_reminders.xml

        if($doc_guid == '')$doc_guid = Input::get('doc_guid');
        $rightsignature = new RightSignature();
        $rightsignature->debug = true;
		
        $doc = $rightsignature->sendReminder($doc_guid);

		/*
        $xml = simplexml_load_string($doc, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
        if ($xml != false) {
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            //dd($array);
        }
        */


        return 'Document Resent Successfully';
    }

   
}







