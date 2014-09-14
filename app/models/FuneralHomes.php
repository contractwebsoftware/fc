<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FuneralHomes extends Eloquent {
	protected $table = 'funeral_homes';
	protected $fillable = array();
	protected $guarded = array();
        use SoftDeletingTrait;

	
}
