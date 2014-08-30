<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Client extends Eloquent {
	protected $table = 'clients';
	protected $fillable = array();
	protected $guarded = array();
         
        use SoftDeletingTrait;

	public function provider(){
		return $this->belongsToMany('FProvider');
	}
	
	
	public function user(){
		return $this->belongsTo('User', 'user_id');
	}
}
