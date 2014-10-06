<?php
class ClientProducts extends Eloquent
{
    protected $table = 'client_products';
    protected $fillable = array();
    protected $guarded = array();
    public function client(){
            return $this->belongsTo('Client');
    }
}