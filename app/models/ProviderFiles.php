<?php

class ProviderFiles extends Eloquent {
	protected $table = 'provider_files';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
