<?php

class FreshbooksBillingHistory extends Eloquent {
	protected $table = 'freshbooks_billing_history';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
