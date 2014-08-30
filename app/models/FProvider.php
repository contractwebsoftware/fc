<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FProvider extends Eloquent
{
	protected $table = 'providers';
	protected $fillable = array();
	protected $guarded = array();
        protected $dates = ['deleted_at'];
        
        use SoftDeletingTrait;

    
	public function client(){
		return $this->hasMany('Client');
	}
	
	public function freshbooksBillingHistory()
	{
		return $this->hasMany('FreshbooksBillingHistory','provider_id');
	}
	
	public function freshbooksProvider()
	{
		return $this->hasOne('FreshbooksProvider','provider_id');
	}
	
	public function itemizedSaleList()
	{
		return $this->hasMany('ItemizedSaleList','provider_id');
	}
	
	public function providerBilling()
	{
		return $this->hasOne('ProviderBilling','provider_id');
	}
		
	public function providerPricingOptions()
	{
		return $this->hasOne('ProviderPricingOptions','provider_id');
	}
	
	public function ProviderZips()
	{
		return $this->hasMany('ProviderZips','provider_id');
	}
	
	public function user(){
		return $this->belongsTo('User', 'user_id');
	}
	
	
}