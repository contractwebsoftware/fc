@section('content')
	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			<h2>Add New Provider</h2>
			<hr>
		</div>
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			{{ Form::open(['action'=>'AdminController@postStore','class'=>'form-horizontal','role'=>'form']) }}
				<fieldset>
					<legend>Company Information</legend>
					<div class="form-group">
						<label  class="sr-only" for="provider">Business Name</label>
						<div class="col-sm-12"><input type="text" placeholder="Business Name" name="provider" id="provider" class="form-control"></div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="address">Company Address</label>
						<div class="col-sm-12"><textarea placeholder="Address" name="address" id="address" class="form-control" row="3"></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-6">
					      <input type="text" class="form-control" id="city" placeholder="City" name="city">
					    </div>
					    <div class="col-sm-3">
					          <input type="text" class="form-control" id="state" placeholder="State" name="state">
					        </div>
					    <div class="col-sm-3">
					      <input type="text" class="form-control" id="zip" placeholder="Zip" name="zip">
					    </div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="website">Website</label>
						<div class="col-sm-12"><input type="text" placeholder="Website" name="website" id="website" class="form-control"></div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="phone">Phone</label>
						<div class="col-sm-12"><input type="text" placeholder="Phone" name="phone" id="phone" class="form-control"></div>
					</div>
					<div class="form-group">
						<label  class="sr-only" for="fax">Fax</label>
						<div class="col-sm-12"><input type="text" placeholder="Fax" name="fax" id="fax" class="form-control"></div>
					</div>
					<div class="form-group">
						<label for="serviceable-area" class="col-sm-12">Select Provider Serviceable Area from the Above Address</label>
						<div class="col-sm-12">
							<select name="service_radius" id="serviceable-area" class="form-control">
								<option value="5">5 Miles</option>
								<option value="10">10 Miles</option>
								<option value="15">15 Miles</option>
								<option value="20">20 Miles</option>
								<option value="30">30 Miles</option>
								<option value="40">40 Miles</option>
								<option value="50">50 Miles</option>
							</select>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>User Information</legend>
					<div class="form-group">
						<label  class="sr-only col-sm-12" for="email">Email Address</label>
						<div class="col-sm-12"><input type="email" placeholder="Email Address" name="email" id="email" class="form-control"></div>
					</div>
					<div class="form-group">
						<label  class="sr-only col-sm-12" for="password">Password</label>
						<div class="col-sm-12"><input type="password" placeholder="Password" name="password" id="password" class="form-control"></div>
					</div>
					<div class="form-group">
						<label  class="sr-only col-sm-12" for="confirm-password">Confirm Password</label>
						<div class="col-sm-12"><input type="password" placeholder="Confirm Password" name="password_confirmation" id="confirm-password" class="form-control"></div>
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