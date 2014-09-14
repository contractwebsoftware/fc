@section('content')
<?php
    $data = Session::get('new_provider_data');
    if($data==null){
        $data = array('business_name'=>'',
            'address'=>'',
            'city'=>'',
            'state'=>'',
            'zip'=>'',
            'website'=>'',
            'phone'=>'',
            'fax'=>'',
            'provider_radius'=>'',
            'email'=>'',
            'password'=>'',
            'password_confirmation'=>''
            );
    }
?>
	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			<h2>Add New Provider</h2>
			<hr>
		</div>
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			{{ Form::open(['action'=>'AdminController@postNewProvider','class'=>'form-horizontal','role'=>'form']) }}
				<fieldset>
					<legend>Company Information</legend>
					<div class="form-group">
						<label  class="sr-only" for="business_name">Business Name</label>
						<div class="col-sm-12"><input type="text" placeholder="Business Name" name="business_name" id="business_name" class="form-control" value="{{$data['business_name']}}" /></div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="address">Company Address</label>
						<div class="col-sm-12"><textarea placeholder="Address" name="address" id="address" class="form-control" row="3" value="{{$data['address']}}" /></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-6">
					      <input type="text" class="form-control" id="city" placeholder="City" name="city" value="{{$data['city']}}" />
					    </div>
					    <div class="col-sm-3">
					          <input type="text" class="form-control" id="state" placeholder="State" name="state" value="{{$data['state']}}" />
					        </div>
					    <div class="col-sm-3">
					      <input type="text" class="form-control" id="zip" placeholder="Zip" name="zip" value="{{$data['zip']}}" />
					    </div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="website">Website</label>
						<div class="col-sm-12"><input type="text" placeholder="Website" name="website" id="website" class="form-control" value="{{$data['website']}}" /></div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="phone">Phone</label>
						<div class="col-sm-12"><input type="text" placeholder="Phone" name="phone" id="phone" class="form-control" value="{{$data['phone']}}" /></div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="fax">Fax</label>
						<div class="col-sm-12"><input type="text" placeholder="Fax" name="fax" id="fax" class="form-control" value="{{$data['fax']}}" /></div>
					</div>
					<div class="form-group">
						<label for="provider_radius" class="col-sm-12">Select Provider Serviceable Area from the Above Address</label>
						<div class="col-sm-12">
							<select name="provider_radius" id="provider_radius" class="form-control">
								<option value="5" {{ ($data['provider_radius']=='5'?'selected':'') }}>5 Miles</option>
								<option value="10" {{ ($data['provider_radius']=='10'?'selected':'') }}>10 Miles</option>
								<option value="15" {{ ($data['provider_radius']=='15'?'selected':'') }}>15 Miles</option>
								<option value="20" {{ ($data['provider_radius']=='20'?'selected':'') }}>20 Miles</option>
								<option value="30" {{ ($data['provider_radius']=='30'?'selected':'') }}>30 Miles</option>
								<option value="40" {{ ($data['provider_radius']=='40'?'selected':'') }}>40 Miles</option>
								<option value="50" {{ ($data['provider_radius']=='50'?'selected':'') }}>50 Miles</option>
							</select>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>User Information</legend>
					<div class="form-group">
						<label  class="sr-only col-sm-12" for="email">Email Address</label>
						<div class="col-sm-12"><input type="email" placeholder="Email Address" name="email" id="email" class="form-control" value="{{$data['email']}}" /></div>
					</div>
					<div class="form-group">
						<label  class="sr-only col-sm-12" for="password">Password</label>
						<div class="col-sm-12"><input type="password" placeholder="Password" name="password" id="password" class="form-control" value="{{$data['password']}}" /></div>
					</div>
					<div class="form-group">
						<label  class="sr-only col-sm-12" for="confirm-password">Confirm Password</label>
						<div class="col-sm-12"><input type="password" placeholder="Confirm Password" name="password_confirmation" id="confirm-password" class="form-control"  value="{{$data['password_confirmation']}}" /></div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<button type="submit" class="btn btn-primary btn-block">Add Provider</button>
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
		</div>
	</div>
@stop