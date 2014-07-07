<?php

class ProviderBilling extends Eloquent {
	protected $table = 'provider_billing';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
