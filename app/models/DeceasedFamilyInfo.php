<?php

class DeceasedFamilyInfo extends Eloquent {
	
	protected $table = 'deceased_family_info';
	protected $fillable = array();
	protected $guarded = array();
	
	public function client(){
		return $this->belongsTo('Client');
	}
}
