<?php

class DeceasedInfoPresentLoc extends Eloquent {
	protected $table = 'deceased_info_present_loc';
	protected $fillable = array();
	protected $guarded = array();
	
	public function client(){
		return $this->belongsTo('Client');
	}
}
