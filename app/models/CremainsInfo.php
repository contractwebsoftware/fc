<?php

class CremainsInfo extends Eloquent {
	protected $table = 'cremains_info';
	protected $fillable = array();
	protected $guarded = array();
	
	public function client(){
		return $this->belongsTo('Client');
	}
}
