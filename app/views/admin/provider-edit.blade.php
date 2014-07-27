@section('content')
	<div class="row">
		<div class="col-xs-12">
			<h2>Edit Provider</h2>
			<hr>
		</div>
		<div class="col-xs-12">
			{{ Form::open(['action'=>'ProviderController@postUpdate','class'=>'form-horizontal','role'=>'form']) }}
				{{ Form::hidden('provider_id',$provider->id) }}
				<fieldset>
					<legend>Company Information</legend>
					<div class="form-group">
						<label  class="col-sm-4" for="provider">Provider Name</label>
						<div class="col-sm-8">
							<input type="text" placeholder="Business Name" name="provider" id="provider" class="form-control" value="{{ $provider->business_name }}">
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
						<div class="col-sm-8"><input type="text" placeholder="Website" name="website" id="website" class="form-control" value="{{ $provider->website }}"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="email">Email</label>
						<div class="col-sm-8"><input type="email" placeholder="Email" name="email" id="email" class="form-control" value="{{ $provider->email }}"></div>
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
						<div class="col-xs-12">
							<button type="submit" class="btn btn-primary btn-block">Update</button>
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">
			{{ Form::open(['action'=>'AdminController@postUpdate', 'class'=>'form-horizontal','file'=>true]) }}
			{{ Form::hidden('provider_id',$provider->id) }}
			
				<fieldset>
					<legend>Pricing Information</legend>
					<div class="form-group">
						<label for="pricing-sheet" class="col-xs-4">Upload Providers Pricing Sheet Here (PDF Only)</label>
						<div class="col-xs-8">
							<input type="file" id="pricing-sheet" name="pricing_sheet" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="monthly-fee" class="col-xs-4">Monthly Fee</label>
						<div class="col-xs-8">
							<input type="text" id="monthly-fee" name="monthly_fee" class="form-control" value="@if($provider->providerBilling){{$provider->providerBilling->monthly_fee}} @endif">
						</div>
					</div>
					<div class="form-group">
						<label for="per-lead-fee" class="col-xs-4">Per Lead Fee</label>
						<div class="col-xs-8">
							<input type="text" id="per-case-fee" name="per_case_fee" class="form-control" value="@if($provider->providerBilling){{$provider->providerBilling->per_case_fee}} @endif">
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
		</div>
	</div>
	<hr>
	<!-- Start managing zip codes -->
	{{ Form::open(['action'=>'AdminController@postUpdateZip']) }}
	{{ Form::hidden('provider_id',$provider->id) }}
	<fieldset>
		<legend>Zip Codes Assigned to Provider</legend>
		<div class="row">
			<div class="col-xs-12 col-md-4">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed">
						<thead>
							<tr>
								<td>Zip Codes</td>
							</tr>
						</thead>
						<tbody>
							@foreach($zips as $zip)
							<tr>
								<td>{{ $zip->zip }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-xs-12 col-md-8">
				<div class="form-group">
					<label for="monthly-fee" class="col-xs-4">Enter Additional Zip Codes</label>
					<div class="col-xs-8">
						<input type="text" id="monthly-fee" placeholder="Enter Zip Code Here" name="provider_zip_code" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-4"></div>
					<div class="col-xs-8">
						<button type="submit" class="btn btn-primary btn-block" class="form-control">ADD</button>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
	{{ Form::close() }}
	<hr>
	<div class="row">
		<div class="col-xs-12">
			{{ Form::open(['action'=>'AdminController@postUpdatePricing', 'class'=>'form-horizontal']) }}
			{{ Form::hidden('provider_id',$provider->id) }}
			<fieldset>
				<legend>Pricing</legend>
				<div class="form-group">
					<label for="plan-a-price" class="col-xs-12 col-md-6">Plan A Cremation Package</label>
					<div class="col-xs-12 col-md-6">
						<input type="text" id="plan-a-price" name="plan_a_total" value="{{ $pricing->plan_a_total }}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="plan-a-detail" class="col-xs-12 col-md-6">Description</label>
					<div class="col-xs-12 col-md-6">
						<textarea rows="3" id="plan-a-detail" name="plan_a_detail" class="form-control">{{ $pricing->plan_a_detail }}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="plan-b-price" class="col-xs-12 col-md-6">Plan B Cremation Package</label>
					<div class="col-xs-12 col-md-6">
						<input type="text" id="plan-b-price" name="plan_b_total" value="{{ $pricing->plan_b_total }}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="plan-b-detail" class="col-xs-12 col-md-6">Description</label>
					<div class="col-xs-12 col-md-6">
						<textarea rows="3" id="plan-b-detail" name="plan_b_detail" class="form-control">{{ $pricing->plan_b_detail }}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="weight-under-250" class="col-xs-12 col-md-6">Weight Under 250lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight-under-250" name="weight_under_250" value="{{ $pricing->weight_under_250 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="weight-251-300" class="col-xs-12 col-md-6">Weight Between 25l-300lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight-251-300" name="weight_251_300" value="{{ $pricing->weight_251_300 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="weight-301-350" class="col-xs-12 col-md-6">Weight Between 301-350lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight-301-350" name="weight_301_350" value="{{ $pricing->weight_301_350 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="weight-above-351" class="col-xs-12 col-md-6">Weight Between greater than 351lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight-above-351" name="weight_above_351" value="{{ $pricing->weight_above_351 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="pacemaker-removal" class="col-xs-12 col-md-6">Pacemaker Removal</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="pacemaker-removal" name="pacemaker_removal" value="{{ $pricing->pacemaker_removal }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="certificate_with_urn" class="col-xs-12 col-md-6">Ship death certifate(s) with urn</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="certificate_with_urn" name="certificate_with_urn" value="{{ $pricing->certificate_with_urn }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="certificate_without_urn" class="col-xs-12 col-md-6">Ship death certifate(s) separately</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="certificate_without_urn" name="certificate_without_urn" value="{{ $pricing->certificate_without_urn }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="pick_cert_office" class="col-xs-12 col-md-6">Pick up death certificate(s) at providers office</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="pick_cert_office" name="pick_cert_office" value="{{ $pricing->pick_cert_office }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="each_death_certificate" class="col-xs-12 col-md-6">Each Death Certificate</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="each_death_certificate" name="each_death_certificate" value="{{ $pricing->each_death_certificate }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="scatter_on_land" class="col-xs-12 col-md-6">Provider Scatter on Land</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="scatter_on_land" name="scatter_on_land" value="{{ $pricing->scatter_on_land }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="scatter_at_sea" class="col-xs-12 col-md-6">Provider Scatter at Sea</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="scatter_at_sea" name="scatter_at_sea" value="{{ $pricing->scatter_at_sea }}" class="form-control">
						</div>
					</div>
				</div>
				<p>Custom Pricing Options: These are provided for custom options which are not available above. To add a new option, enter the description, dollar amount, and whether or not it is a required fee. Select whether to include the custom option in Plan A, Plan B, or both plans.</p>
				<div class="form-group">
					<label for="custom_pricing_1" class="col-xs-12">
						Custom Pricing Option 1
					</label>
					<label for="custom_pricing_1" class="col-xs-12 col-md-6">
						<input type="text" placeholder="Enter Description" name="custom_pricing_1_text" class="form-control">
					</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="custom_pricing_1" name="custom_pricing_1_value" value="{{ $pricing->custom_pricing_1_value }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12 col-md-3">
						<label for="custom_pricing_1_include">Included in Plan(s):</label>
					</div>
					<div class="col-xs-12 col-md-3">
						<select name="custom_pricing_1_include" id="custom_pricing_1_include" class="form-control">
							<option value="1">Plan A</option>
							<option value="2">Plan B</option>
							<option value="3">Both</option>
						</select>
					</div>
					<div class="col-xs-12 col-md-3">
						<label for="custom_pricing_1_required">Required</label>
					</div>
					<div class="col-xs-12 col-md-3">
						<select name="custom_pricing_1_required" id="custom_pricing_1_required" class="form-control">
							<option value="No">No</option>
							<option value="Yes">Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="custom_pricing_2" class="col-xs-12">
						Custom Pricing Option 2
					</label>
					<label for="custom_pricing_2" class="col-xs-12 col-md-6">
						<input type="text" placeholder="Enter Description" name="custom_pricing_2_text" class="form-control">
					</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="custom_pricing_2" name="custom_pricing_2_value" value="{{ $pricing->custom_pricing_2_value }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12 col-md-3">
						<label for="custom_pricing_2_include">Included in Plan(s):</label>
					</div>
					<div class="col-xs-12 col-md-3">
						<select name="custom_pricing_2_include" id="custom_pricing_2_include" class="form-control">
							<option value="1">Plan A</option>
							<option value="2">Plan B</option>
							<option value="3">Both</option>
						</select>
					</div>
					<div class="col-xs-12 col-md-3">
						<label for="custom_pricing_2_required">Required</label>
					</div>
					<div class="col-xs-12 col-md-3">
						<select name="custom_pricing_2_required" id="custom_pricing_2_required" class="form-control">
							<option value="No">No</option>
							<option value="Yes">Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="custom_pricing_3" class="col-xs-12">
						Custom Pricing Option 3
					</label>
					<label for="custom_pricing_3" class="col-xs-12 col-md-6">
						<input type="text" placeholder="Enter Description" name="custom_pricing_3_text" class="form-control">
					</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="custom_pricing_3" name="custom_pricing_3_value" value="{{ $pricing->custom_pricing_3_value }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12 col-md-3">
						<label for="custom_pricing_3_include">Included in Plan(s):</label>
					</div>
					<div class="col-xs-12 col-md-3">
						<select name="custom_pricing_3_include" id="custom_pricing_3_include" class="form-control">
							<option value="1">Plan A</option>
							<option value="2">Plan B</option>
							<option value="3">Both</option>
						</select>
					</div>
					<div class="col-xs-12 col-md-3">
						<label for="custom_pricing_3_required">Required</label>
					</div>
					<div class="col-xs-12 col-md-3">
						<select name="custom_pricing_3_required" id="custom_pricing_3_required" class="form-control">
							<option value="No">No</option>
							<option value="Yes">Yes</option>
						</select>
					</div>
				</div>
			</fieldset>
			{{ Form::close() }}
		</div>
	</div>
@stop