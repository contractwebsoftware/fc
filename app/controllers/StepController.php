<?php

class StepController extends BaseController {

	protected $layout = 'layouts.client';
	
	public function getCities(){
            //print_r($_GET);
            $city = DB::table('funeral_homes')->where('e_state', 'like', Input::get('state'))->orderBy('e_city', 'asc')->groupBy('e_city')->get();
            $json_r = array();
            foreach($city as $key=>$row){
                $json_r[$row->id] = $row->e_city;
            }
            return Response::json($json_r);
            
        }
        
        public function getZips(){
            $zip = DB::table('funeral_homes')->where('id', Input::get('city'))->first();
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
}