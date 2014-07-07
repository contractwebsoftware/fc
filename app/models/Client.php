<?php

class Client extends Eloquent {
	protected $table = 'clients';
	protected $fillable = array();
	protected $guarded = array();
        
	public function provider(){
		return $this->belongsToMany('FProvider');
	}
	
	
}
