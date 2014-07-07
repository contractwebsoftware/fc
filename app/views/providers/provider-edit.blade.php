@section('content')
	<div class="row">
		<div class="col-xs-12">
			<h2>Edit Provider</h2>
			<hr>
		</div>
		<div class="col-xs-12">
			{{ Form::open(['action'=>'AdminController@postUpdate','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
				{{ Form::hidden('provider_id',$provider->id) }}
				<fieldset>
					<legend>Company Information</legend>
					<div class="form-group">
						<label  class="col-sm-4" for="provider">Provider Name</label>
						<div class="col-sm-8">
							<input type="text" placeholder="Business Name" name="provider" id="provider" class="form-control" value="{{ $provider->provider }}">
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="address">Company Address</label>
						<div class="col-sm-8"><textarea placeholder="Address" name="address" id="address" class="form-control" row="3">{{ $provider->address }}</textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-4"></div>
						<div class="col-sm-4">
					      <input type="text" class="form-control" id="city" placeholder="City" name="city" value="{{ $provider->city }}">
					    </div>
					    <div class="col-sm-2">
					          <input type="text" class="form-control" id="state" placeholder="State" name="state" value="{{ $provider->state }}">
					        </div>
					    <div class="col-sm-2">
					      <input type="text" class="form-control" id="zip" placeholder="Zip" name="zip" value="{{ $provider->zip }}">
					    </div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="website">Website</label>
						<div class="col-sm-8"><input type="text" placeholder="Website" name="website" id="website" class="form-control"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="email">Email</label>
						<div class="col-sm-8"><input type="email" placeholder="Email" name="email" id="email" class="form-control" value="{{ Sentry::getUser()->email }}"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="phone">Phone</label>
						<div class="col-sm-8"><input type="text" placeholder="Phone" name="phone" id="phone" class="form-control" value="{{ $provider->phone }}"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="fax">Fax</label>
						<div class="col-sm-8"><input type="text" placeholder="Fax" name="fax" id="fax" class="form-control" value="{{ $provider->fax }}"></div>
					</div>
					<div class="form-group">
						<label for="serviceable-area" class="col-sm-12">Select Provider Serviceable Area from the Above Address</label>
						<div class="col-sm-12">
							<select name="service_radius" id="serviceable-area" class="form-control">
								<option value="5"{{ ($provider->service_radius=='5') ? ' selected' : '' }}>5 Miles</option>
								<option value="10"{{ ($provider->service_radius=='10') ? ' selected' : '' }}>10 Miles</option>
								<option value="15"{{ ($provider->service_radius=='15') ? ' selected' : '' }}>15 Miles</option>
								<option value="20"{{ ($provider->service_radius=='20') ? ' selected' : '' }}>20 Miles</option>
								<option value="30"{{ ($provider->service_radius=='30') ? ' selected' : '' }}>30 Miles</option>
								<option value="40"{{ ($provider->service_radius=='40') ? ' selected' : '' }}>40 Miles</option>
								<option value="50"{{ ($provider->service_radius=='50') ? ' selected' : '' }}>50 Miles</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="pricing-sheet" class="col-xs-4">Upload Providers Pricing Sheet Here (PDF Only)</label>
						<div class="col-xs-8">
							<input type="file" id="pricing-sheet" name="pricing_sheet" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<button type="submit" class="btn btn-primary btn-block">Update</button>
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
		</div>
	</div>
	<hr>
@stop