<?php

class StepController extends BaseController {

	protected $layout = 'layouts.client';
	
	public function getCities(){
            //print_r($_GET);
            //->groupBy('e_city')->orderBy('e_city')
            $city = DB::table('funeral_homes')->where('e_state', 'like', Input::get('state'))->groupBy('e_city')->orderBy('e_city','asc')->get();
            $json_r = array();
            
            //asort($city);
            
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
            foreach($providers as $key=>$row){
                if($row->status=='1')$json_r['provider-'.$row->id] = $row->business_name;
            }
            foreach($providers_with_zips as $key=>$row){
                $this_provider = Fprovider::find($row->provider_id);
                if($this_provider != null)
                    if($this_provider->status=='1')$json_r['provider-'.$this_provider->id] = $this_provider->business_name;
            }
            //asort($json_r);

            return Response::json($json_r);
        }
}