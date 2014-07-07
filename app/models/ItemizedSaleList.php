<?php

class ItemizedSaleList extends Eloquent {
	protected $table = 'itemized_sale_list';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	public function provider(){
		return $this->belongsTo('FProvider');
	}
}
