<?php

class FreshbooksProvider extends Eloquent {
	protected $table = 'freshbooks_provider';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
