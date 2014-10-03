@section('content')
	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			<p><strong>Note:</strong> These are site-wide default settings for provider pricing. Any time a new provider is approved, they will default to this pricing structure. Individual provider pricing can be adjusted on the provider page.</p>
			<?php
                        /*
                        {{ Form::open(['action'=>'AdminController@postUpdateDefaultBilling','class'=>'form-horizontal','role'=>'form']) }}
				<div class="form-group">
					<label for="monthly_fee">Default Monthly Fee</label>
					<input type="text" class="form-control" value="{{ $billing->monthly_fee }}" id="monthly_fee" name="monthly_fee">
				</div>
				<div class="form-group">
					<label for="lead_fee">Default Per Lead Fee</label>
					<input type="text" class="form-control" value="{{ $billing->lead_fee }}" id="lead_fee" name="lead_fee">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-block btn-primary">Update</button>
				</div>
			{{ Form::close() }}
                        */?>
                        
                        {{ Form::open(['action'=>'AdminController@postUpdateProviderPlans','class'=>'form-horizontal','role'=>'form']) }}
				<div class="form-group">
					<label for="plan_basic">Basic Plan</label>
					<input type="text" class="form-control" value="{{ $plan_basic->price }}" id="plan_basic" name="provider_plans[plan_basic]">
				</div>
				<div class="form-group">
					<label for="plan_premium">Premium Plan</label>
					<input type="text" class="form-control" value="{{ $plan_premium->price }}" id="plan_premium" name="provider_plans[plan_premium]">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-block btn-primary">Update</button>
				</div>
			{{ Form::close() }}
                        
			<!--
                        <hr>
			<p class="h2">Last Lead Transmission to Freshbooks</p>
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-condensed">
					<thead>
						<tr>
							<th>Provider</th>
							<th>City</th>
							<th>State</th>
							<th>Freshbooks ID</th>
							<th>Leads Billed</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>-->
		</div>
	</div>
@stop