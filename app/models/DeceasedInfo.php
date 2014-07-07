<?php

class DeceasedInfo extends Eloquent {
	protected $table = 'deceased_info';
	protected $fillable = array();
	protected $guarded = array();
	
	public function client(){
		return $this->belongsTo('Client');
	}
}
