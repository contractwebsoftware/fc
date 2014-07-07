<?php

class ProviderPricingOptions extends Eloquent {
	protected $table = 'provider_pricing_options';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
