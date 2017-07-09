<?php

class StepController extends BaseController {

	protected $layout = 'layouts.client';
	
	public function getCities(){
            //print_r($_GET);
            //->groupBy('e_city')->orderBy('e_city')
            //$city = DB::table('funeral_homes')->where('e_state', 'like', Input::get('state'))->groupBy('e_city')->orderBy('e_city','asc')->get();
            $city = FuneralHomes::select('e_city','id')->where('e_state', 'like', Input::get('state'))->groupBy('e_city')->get();
            $json_r = array();

            $city = $city->sortBy(function($city)
            {
                return $city->e_city;
            });

            foreach($city as $key=>$row){
                $json_r[$row->e_city.'---'.$row->id] = $row->e_city;
            }
            
            //asort($json_r);
            /*
            foreach($city as $key=>$row){
                $json_r[$row] = $key;
            }*/
            
            // dd($json_r);
            return Response::json($json_r);
            
        }
        
        public function getZips(){
            $city_r = explode(Input::get('city'), '---');
            
            $zip = DB::table('funeral_homes')->where('id', $city_r[1])->first();
            //dd($zip);
            //$city = DB::table('zips')->where('city', Input::get('city'))->andWhere('state', $zip->state)->get();
            $cities = DB::table('funeral_homes')->where('e_city', $zip->e_city)->where('e_state', $zip->e_state)->orderBy('e_postal', 'asc')->get();
            
            //print_r(DB::getQueryLog());
            //dd($city);
            $json_r = array();
            foreach($cities as $key=>$row){
                $json_r[$row->e_postal] = $row->e_postal;
            }
            return Response::json($json_r);
        }


        public function getProvidersByState()
        {
            $json_r = null;
            //$state = State::where('name_shor', 'like', Input::get('state'))->first();

            //$providers = FProvider::where('state')->whereNull('deleted_at')->orderBy('business_name', 'asc')->get();
            $filename = public_path('provider_states_'.Input::get('state').'.txt');
            if(!file_exists($filename))$fh = fopen($filename, 'w') or die("Can't create file");
            //fclose($fh);

            $providers_json = file_get_contents($filename);
            if($providers_json != '' and Input::get('refresh')=='')return $providers_json;


            $providers = DB::select(DB::raw(" SELECT providers.id, providers.business_name, providers.city
                                            FROM providers, provider_zips, zips 
                                            WHERE providers.id = provider_zips.provider_id 
                                                    and providers.provider_status = 1
                                                    and providers.admin_provider = 0
                                                    and provider_zips.zip = zips.zip
                                                    and zips.state_abv like '".Input::get('state')."'
                                    "));
            //dd(DB::getQueryLog());
            //

            if(!$providers){
                $providers = FProvider::
                where('default_for_state', Input::get('state'))
                    ->where('provider_status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('business_name', 'asc')
                    ->get();
            }
            //dd($providers);

            foreach($providers as $key=>$row){
                $json_r['provider-'.$row->id] = $row->city.' - '.$row->business_name;
            }

            File::put($filename, json_encode($json_r));


            //dd($providers);



            //dd($json_r);
            return Response::json($json_r);


        }


        public function getProvidersByCity(){
            $city_r = explode('---', Input::get('city'));
            //dd($city_r);
            $zip = DB::table('funeral_homes')->where('id', $city_r[1])->first();
            //dd($zip);
            //$city = DB::table('zips')->where('city', Input::get('city'))->andWhere('state', $zip->state)->get();
            $funeral_homes = DB::table('funeral_homes')->where('e_city', $zip->e_city)->where('e_state', $zip->e_state)->whereNull('deleted_at')->orderBy('biz_name', 'asc')->get();

            $providers = DB::table('providers');
            $providers_with_zips = DB::table('provider_zips');


            $funeral_home_city_r = DB::table('zips')->where('city', $city_r[0])->where('state', $zip->e_state)->get();

            if(is_array($funeral_home_city_r))
            foreach($funeral_home_city_r as $this_fh){
                //print_r($this_fh);
                $providers = $providers->orWhere('zip', $this_fh->zip);
                $providers_with_zips = $providers_with_zips->orWhere('zip', $this_fh->zip);

            }



            $providers = $providers->whereNull('deleted_at')->orderBy('business_name', 'asc')->get();
            $providers_with_zips = $providers_with_zips->get();
            //dd($providers_with_zips);
            //dd(DB::getQueryLog());

            $json_r = array(''=>'Select A Provider');
            foreach($funeral_homes as $key=>$row){
                $json_r['funeralhome-'.$row->id] = $row->biz_name;
            }

            if($providers == null){
               # echo 'FAILED TO FIND'.$zip->e_state;
                $providers = DB::table('providers')->where('default_for_state', $zip->e_state)->whereNull('deleted_at')->orderBy('business_name', 'asc')->get();
                #dd($providers);
            }

            foreach($providers as $key=>$row){
                if($row->provider_status=='1' and $row->admin_provider=='0')$json_r['provider-'.$row->id] = $row->business_name;
            }

            foreach($providers_with_zips as $key=>$row){
                $this_provider = Fprovider::find($row->provider_id);
                if($this_provider != null)
                    if($this_provider->provider_status=='1')$json_r['provider-'.$this_provider->id] = $this_provider->business_name;
            }
            //asort($json_r);

            return Response::json($json_r);
        }
}