<?php

class Group extends Eloquent {
	protected $table = 'groups';
	protected $fillable = array();
	protected $guarded = array();

	public static $rules = array();
	protected $fillable = array('*');
}
