<?php

class ProviderZip extends Eloquent {
	protected $table = 'provider_zips';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
