<?php

class Products extends Eloquent {
	protected $table = 'products';
	protected $fillable = array();
	protected $guarded = array();
	
	public static $rules = array();
	
}
