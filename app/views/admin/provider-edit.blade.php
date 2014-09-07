@section('content')

	<div class="row">
		<div class="col-xs-12">
			<h2>Edit Provider</h2>
			<hr>
		</div>
		<div class="col-xs-12">
			{{ Form::open(['action'=>'AdminController@postUpdateProvider','class'=>'form-horizontal','role'=>'form']) }}
				{{ Form::hidden("provider[id]",$provider->id) }}
				<fieldset>
					<legend>Company Information</legend>
                                        <div class="form-group">
						<label  class="col-sm-4" for="provider_status">Provider Status</label>
						<div class="col-sm-8">
                                                    <select name="provider[provider_status]" id="provider_status" class="form-control">
                                                        <option value="0" {{ ($provider->provider_status=='0') ? ' selected' : '' }}>UnApproved</option>
                                                        <option value="1" {{ ($provider->provider_status=='1') ? ' selected' : '' }}>Approved</option>
                                                        <option value="2" {{ ($provider->provider_status=='2') ? ' selected' : '' }}>Deleted</option>
                                                    </select>
						</div>
					</div>
            
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="provider_login">Provider Login</label>
                                            <div class="col-sm-4">
                                                <input type="text" placeholder="Login Email" name="provider_login" id="provider_login" class="form-control" value="{{ $fuser->email }}">
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" placeholder="Login Password" name="newpassword" class="form-control" value="">
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" placeholder="Confirm Password" name="newpassword_confirmation" class="form-control" value="">
                                            </div>
					</div>
                                        
					<div class="form-group">
						<label  class="col-sm-4" for="business_name">Provider Name</label>
						<div class="col-sm-8">
							<input type="text" placeholder="Business Name" name="provider[business_name]" id="provider" class="form-control" value="{{ $provider->business_name }}">
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="address">Company Address</label>
						<div class="col-sm-8"><textarea placeholder="Address" name="provider[address]" id="address" class="form-control" row="3">{{ $provider->address }}</textarea></div>
					</div>
					<div class="form-group">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="city" placeholder="City" name="provider[city]" value="{{ $provider->city }}">
                                            </div>
                                            <div class="col-sm-2">
                                                    <input type="text" class="form-control" id="state" placeholder="State" name="provider[state]" value="{{ $provider->state }}">
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" id="zip" placeholder="Zip" name="provider[zip]" value="{{ $provider->zip }}">
                                            </div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="website">Website</label>
						<div class="col-sm-8"><input type="text" placeholder="Website" name="provider[website]" id="website" class="form-control" value="{{ $provider->website }}"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="email">Email</label>
						<div class="col-sm-8"><input type="email" placeholder="Email" name="provider[email]" id="email" class="form-control" value="{{ $provider->email }}"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="phone">Phone</label>
						<div class="col-sm-8"><input type="text" placeholder="Phone" name="provider[phone]" id="phone" class="form-control" value="{{ $provider->phone }}"></div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4" for="fax">Fax</label>
						<div class="col-sm-8"><input type="text" placeholder="Fax" name="provider[fax]" id="fax" class="form-control" value="{{ $provider->fax }}"></div>
					</div>
                                        
					<div class="form-group">
						<label for="provider_radius" class="col-sm-12">Select Provider Serviceable Area from the Above Address</label>
						<div class="col-sm-12">
							<select name="provider[provider_radius]" id="provider_radius" class="form-control">
								<option value="5" {{ ($provider->provider_radius=='5') ? ' selected' : '' }}>5 Miles</option>
								<option value="10" {{ ($provider->provider_radius=='10') ? ' selected' : '' }}>10 Miles</option>
								<option value="15" {{ ($provider->provider_radius=='15') ? ' selected' : '' }}>15 Miles</option>
								<option value="20" {{ ($provider->provider_radius=='20') ? ' selected' : '' }}>20 Miles</option>
								<option value="30" {{ ($provider->provider_radius=='30') ? ' selected' : '' }}>30 Miles</option>
								<option value="40" {{ ($provider->provider_radius=='40') ? ' selected' : '' }}>40 Miles</option>
								<option value="50" {{ ($provider->provider_radius=='50') ? ' selected' : '' }}>50 Miles</option>
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
			{{ Form::open(['action'=>'AdminController@postUpdateFiles', 'class'=>'form-horizontal','files'=>true]) }}
			{{ Form::hidden("provider[id]",$provider->id) }}
			<?php
                            $fileNames = Array('pricing'=>"Pricing Sheet", 'clientform'=>"Client Forms", 
                                                'custom1'=>"Custom Document 1", 'custom2'=>"Custom Document 2", 'custom3'=>"Custom Document 3", 'custom4'=>"Custom Document 4", 'custom5'=>"Custom Document 5");
                        ?>
                        <fieldset>
                            <legend>Provider Files</legend>
                            <div class="form-group">
                                <label for="provider_files" class="col-xs-4">Upload Provider File</label>
                                <div class="col-xs-8">
                                       
                                    File Type: 
                                    <select name="provider_files_type" id="provider_files_type">
                                            <option value="">Select A Type of File</option>
                                            <option value="pricing"><?=$fileNames['pricing']?></option>
                                            <option value="clientform"><?=$fileNames['clientform']?></option>
                                            <option value="custom1"><?=$fileNames['custom1']?></option>
                                            <option value="custom2"><?=$fileNames['custom2']?></option>
                                            <option value="custom3"><?=$fileNames['custom3']?></option>
                                            <option value="custom4"><?=$fileNames['custom4']?></option>
                                            <option value="custom5"><?=$fileNames['custom5']?></option>
                                    </select>
                                    <input type="file" id="provider_files" name="provider_files_new" class="form-control">
                                        
                                </div>
                                <br style="float:none;clear:both;"/><br />&nbsp; &nbsp; <b>Current Files</b>:
                                <?php
                                    if($provider_files!=null)
                                    foreach($provider_files as $key=>$file){
                                        echo '<br />';
                                        echo '&nbsp; &nbsp; &nbsp; '.$fileNames[$file->file_type].': <a href="'.asset('/provider_files/'.$file->provider_id.'/'.$file->file_name).'" target="_blank">'.$file->file_name.'</a> &nbsp; ';   
                                        echo "<i>".link_to_action('AdminController@getRemoveFiles', "Delete", array('fileid'=>$file->id,'provider_id'=>$provider->id), $attributes = array('onclick'=>'return confirm("Are you sure you want to delete this file?")',"style"=>"color:red!important;"))."</i>";
                                        
                                    }
                                ?>
                            </div>
                            <div class="form-group">
                                    <div class="col-xs-12">
                                            <button type="submit" class="btn btn-primary btn-block" onclick="if($('#provider_files_type').val()==''){alert('Please Select A File Type');return false;}">Upload</button>
                                    </div>
                            </div>
                        </fieldset>
			{{ Form::close() }}
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">
			{{ Form::open(['action'=>'AdminController@postUpdateBilling', 'class'=>'form-horizontal','files'=>true]) }}
			{{ Form::hidden("provider[id]",$provider->id) }}

                        <fieldset>
                                <legend>Pricing Information</legend>

                                <div class="form-group">
                                        <label for="monthly_fee" class="col-xs-4">Monthly Fee</label>
                                        <div class="col-xs-8">
                                                <input type="text" id="monthly_fee" name="provider_billing[monthly_fee]" class="form-control" value="@if($provider->providerBilling){{$provider->providerBilling->monthly_fee}} @endif">
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="per_case_fee" class="col-xs-4">Per Lead Fee</label>
                                        <div class="col-xs-8">
                                                <input type="text" id="per_case_fee" name="provider_billing[per_case_fee]" class="form-control" value="@if($provider->providerBilling){{$provider->providerBilling->per_case_fee}} @endif">
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
	<!-- Start managing zip codes -->
	{{ Form::open(['action'=>'AdminController@postUpdateZip']) }}
	{{ Form::hidden("provider[id]",$provider->id) }}
        
	<fieldset>
		<legend>Zip Codes Assigned to Provider</legend>
		<div class="row">
                    <label for="provider_zip_code" class="col-xs-4">Currently Assigned Zip Codes</label><br style="float:none;clear:both;"/>
                        <script>
                            var addallchecked=true;
                            var removeallchecked=true;
                            function addcheckall(){
                                $('.addzips').prop('checked', addallchecked);
                                addallchecked = !addallchecked;
                            }
                            function removeall(){
                                $('.removezips').prop('checked', removeallchecked);
                                removeallchecked = !removeallchecked;
                            }
                       </script>
			<div class="col-xs-12 col-md-4">
                            <div class="table-responsive">
                                <br />
                               <a href="#" onclick="removeall();return false;"><b>Select All</b></a>
                                <table class="table table-condensed" style="margin:0;padding:0;">
                                    <thead>
                                    <tr>
                                        <td>Zip Codes</td>
                                        <td align="right">Remove</td>
                                    </tr>
                                    </thead>
                                </table>
					<div  style="height:370px;overflow-x:auto;border:1px solid #ccc;width:100%;margin-bottom:5px;">
					<table class="table  table-condensed" width="90%">
						
						<tbody>
                                                    <?php
                                                    
                                                        if($zips!=null){
                                                           //print_r($zips);
                                                            foreach($zips as $zip){
                                                                $zips_r[$zip->zip]=$zip->zip;
                                                                echo '<tr><td width="90%"><label for="zip-'.$zip->zip.'" style="cursor:pointer;">'.$zip->zip.'</label></td>'
                                                                        . '<td width="10%"><input type="checkbox" class="removezips" id="zip-'.$zip->zip.'" name="removezips['.$zip->zip.']" value="'.$zip->zip.'" /></td>'
                                                                        . '</tr>';
                                                            }
                                                        }
                                                    ?>
						</tbody>
					</table>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block" class="form-control">Remove</button>
				</div>
			</div>
			<div class="col-xs-12 col-md-8">
                               
				<div class="form-group">
                                    <label for="provider_zip_code" class="col-xs-4">Add Zip Codes</label><br style="float:none;clear:both;"/>
					<div class="col-xs-8">
                                            <i>You can enter multiple zips separated by commas</i><br />
                                            <input type="text" id="provider_zip_code" placeholder="Enter Zip Code Here" name="provider_zip_code" class="form-control">
					</div>
				</div>
                                <br style="float:none;clear:both;"/>
                           
                                <div class="form-group">
                                    <label for="provider_zip_code" class="col-xs-4">Zips in Providers' Radius</label><br style="float:none;clear:both;"/>
                                        &nbsp; &nbsp; <a href="#" onclick="addcheckall();return false;"><b>Select All</b></a>
					<div class="col-xs-8" style="height:300px;overflow-x:auto;border:1px solid #aaa;width:100%;margin:5px 17px;">
                                            <?php
                                                if($zip_info!=null){
                                                    foreach($zip_info as $zip){
                                                        if(!in_array($zip->zip,$zips_r))
                                                        echo '<br style="float:none;clear:both;"/><label for="addzip-'.$zip->zip.'" style="cursor:pointer;float:left;">'.$zip->zip.' </label>'
                                                                . '&nbsp; <input type="checkbox" class="addzips" id="addzip-'.$zip->zip.'" name="addzips['.$zip->zip.']" value="'.$zip->zip.'" /> '
                                                                . '&nbsp; ~ '.round($zip->distance,2).' miles';
                                                    }
                                                    
                                                }
                                            ?>
					</div>
				</div>
                            
                            
				<div class="form-group">
					<div class="col-xs-4"></div>
					<div class="col-xs-8">
						<button type="submit" class="btn btn-primary btn-block" class="form-control">Add</button>
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
			{{ Form::hidden("provider[id]",$provider->id) }}
                        
			<fieldset>
				<legend>Pricing</legend>
				<div class="form-group">
					<label for="basic_cremation" class="col-xs-12 col-md-6">Plan A Cremation Package</label>
					<div class="col-md-6">
						<input type="text" id="basic_cremation" name="pricing[basic_cremation]" value="{{ $pricing->basic_cremation }}" class="form-control">
					</div>
				</div>
                                
				<div class="form-group">
					<label for="package_a_desc" class="col-xs-12 col-md-6">Description</label>
					<div class="col-xs-12 col-md-6">
						<textarea rows="3" id="package_a_desc" name="pricing[package_a_desc]" class="form-control">{{ ($pricing->package_a_desc==''?'Basic Service Fee, care of your loved one in climatically controlled environment, obtaining Cremation Authorizations and filing the Death Certificate with State of California @ $690, Crematory fee, Cremation container and Basic urn @ $185.':$pricing->package_a_desc) }}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="premium_cremation" class="col-xs-12 col-md-6">Plan B Cremation Package</label>
					<div class="col-xs-12 col-md-6">
						<input type="text" id="premium_cremation" name="pricing[premium_cremation]" value="{{ $pricing->premium_cremation }}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="package_b_desc" class="col-xs-12 col-md-6">Description</label>
					<div class="col-xs-12 col-md-6">
						<textarea rows="3" id="package_b_desc" name="pricing[package_b_desc]" class="form-control">{{ ($pricing->package_b_desc==''?'Premium Package includes all services of Plan A plus an urn. Refer to the General Price List for our urn selection.':$pricing->package_b_desc) }}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="weight_lt_250" class="col-xs-12 col-md-6">Weight Under 250lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight_lt_250" name="pricing[weight_lt_250]" value="{{ $pricing->weight_lt_250 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="weight_lt_300" class="col-xs-12 col-md-6">Weight Between 25l-300lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight_lt_300" name="pricing[weight_lt_300]" value="{{ $pricing->weight_lt_300 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="weight_lt_350" class="col-xs-12 col-md-6">Weight Between 301-350lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight_lt_350" name="pricing[weight_lt_350]" value="{{ $pricing->weight_lt_350 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="weight_gt_350" class="col-xs-12 col-md-6">Weight Between greater than 351lbs</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="weight_gt_350" name="pricing[weight_gt_350]" value="{{ $pricing->weight_gt_350 }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="pacemaker" class="col-xs-12 col-md-6">Pacemaker Removal</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="pacemaker" name="pricing[pacemaker]" value="{{ $pricing->pacemaker }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="deathcert_wurn" class="col-xs-12 col-md-6">Ship death certificate(s) with urn</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="deathcert_wurn" name="pricing[deathcert_wurn]" value="{{ $pricing->deathcert_wurn }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="deathcert_cep" class="col-xs-12 col-md-6">Ship death certificate(s) separately</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="deathcert_cep" name="pricing[deathcert_cep]" value="{{ $pricing->deathcert_cep }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="deathcert_pickup" class="col-xs-12 col-md-6">Pick up death certificate(s) at providers office</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="deathcert_pickup" name="pricing[deathcert_pickup]" value="{{ $pricing->deathcert_pickup }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="deathcert_each" class="col-xs-12 col-md-6">Each Death Certificate</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="deathcert_each" name="pricing[deathcert_each]" value="{{ $pricing->deathcert_each }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="scatter_on_land" class="col-xs-12 col-md-6">Provider Scatter on Land</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="scatter_on_land" name="pricing[scatter_on_land]" value="{{ $pricing->scatter_on_land }}" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="scatter_at_sea" class="col-xs-12 col-md-6">Provider Scatter at Sea</label>
					<div class="col-xs-12 col-md-6">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" id="scatter_at_sea" name="pricing[scatter_at_sea]" value="{{ $pricing->scatter_at_sea }}" class="form-control">
						</div>
					</div>
				</div>
                                <br  /><br />
				<p>Custom Pricing Options: These are provided for custom options which are not available above. To add a new option, enter the description, dollar amount, and whether or not it is a required fee. Select whether to include the custom option in Plan A, Plan B, or both plans.</p>
				
                                <?php
                                for($x=1;$x<=3;$x++){
                                    $custom1_val = 'custom'.$x.'_text';
                                    $custom1_text = $pricing->$custom1_val;
                                    
                                    $custom1_val = 'custom'.$x;
                                    $custom1 = $pricing->$custom1_val;
                                    
                                    $custom1_val = 'custom'.$x.'_included';
                                    $custom1_included = $pricing->$custom1_val;
                                    
                                    $custom1_val = 'custom'.$x.'_req';
                                    $custom1_req = $pricing->$custom1_val;
                                    
                                    
                                    ?>
                                    <h4 style="font-size:16px;">Custom Pricing Option <?=$x?></h4>
                                    <div class="form-group" style="margin-bottom:0px;">

                                            <div class="col-md-6">
                                                <label for="custom<?=$x?>_text">Description:</label>
                                                <input type="text" placeholder="Enter Description" name="pricing[custom<?=$x?>_text]" id="custom<?=$x?>_text" class="form-control" value="{{ $custom1_text }}">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="custom<?=$x?>">Add</label>

                                                    <div class="input-group">

                                                            <span class="input-group-addon">$</span>
                                                            <input type="text" id="custom<?=$x?>" name="pricing[custom<?=$x?>]" value="{{ $custom1 }}" class="form-control">
                                                    </div>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <div class="col-md-6">
                                                    <label for="custom<?=$x?>_included">Included in Plan(s):</label>
                                                    <select name="pricing[custom<?=$x?>_included]" id="custom<?=$x?>_included" class="form-control">
                                                            <option value="1" {{ ($custom1_included=='1') ? ' selected' : '' }}>Plan A</option>
                                                            <option value="2" {{ ($custom1_included=='2') ? ' selected' : '' }}>Plan B</option>
                                                            <option value="3" {{ ($custom1_included=='3') ? ' selected' : '' }}>Both</option>
                                                    </select>
                                            </div>
                                            <div class="col-md-6">
                                                    <label for="custom<?=$x?>_req">Required</label>
                                                    <select name="pricing[custom<?=$x?>_req]" id="custom1_req" class="form-control">
                                                            <option value="N" {{ ($custom1_req=='N') ? ' selected' : '' }}>No</option>
                                                            <option value="Y" {{ ($custom1_req=='Y') ? ' selected' : '' }}>Yes</option>
                                                    </select>
                                            </div>
                                    </div>
                                <?php
                                }
                                ?>
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
            {{ $clients->links() }}
          
            <table class="">
                <thead>
                    <tr>
                        <!--<th><input type="checkbox" onclick="checkall();" id="checkallcb" style="float: left;" />
                            <label for="checkallcb" style="cursor:pointer;">Check All</label>
                        </th>-->
                        <th>Customer Name</th>
                        <th>Deceased Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Customer Email</th>
                        <th class="text-right">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $clients as $client )
                        <tr>
                                <!--<td ><input type="checkbox" class="clients_mass_action" name="edit_clients[{{$client->id}}]" value="{{$client->id}}" /></td>-->
                                <td >{{ $client->first_name.' '.$client->last_name }}</td>
                                <td >{{ $client->deceased_first_name.' '.$client->deceased_last_name }}</td>
                                <td >{{ $client->phone }}</td>
                                <td >{{ date('m/d/Y',strtotime($client->created_at)) }}</td>
                                <td >@if($client->user != null) {{ $client->user->email }} @endif</td>
                                <td class="text-right" >
                                    <div data-toggle="tooltip" data-html="true" class="tooltips" data-placement="bottom"  
                            title="<div style='text-align:left;'><b>Date Created</b>: {{ date("m/d/Y",strtotime($client->created_at)) }}<br /><b>Agreed To FTC</b>: {{$client->agreed_to_ftc?'Yes':'No'}}<br /><b>Confirmed Legal Auth</b>: {{$client->confirmed_legal_auth?'Yes':'No'}}<br /><b>Confirmed Correct Info</b>: {{$client->confirmed_correct_info?'Yes':'No'}}<br /> <b>Relationship</b>: {{$client->relationship}}</div>">
                                    <?php
                                        switch($client->status){
                                            case 0:echo 'Active';break;
                                            case 1:echo 'Completed';break;
                                            case 3:echo 'Deleted';break;
                                        }   
                                        if($client->preneed == "y")echo '/Pre-Need';
                                    ?>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ action('AdminController@getEditClient',$client->id) }}" class="btn btn-xs btn-default">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                    </a>&nbsp;
                                    <?php
                                    if($client->status == 3)echo '<a href="'.action('AdminController@getUnDeleteClient',$client->id).'" class="btn btn-xs btn-success" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> UnDelete</a>';
                                    else echo '<a href="'.action('AdminController@getDeleteClient',$client->id).'" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> </a>';
                                    ?>
                                </td>
                        </tr>
                    @endforeach
            </tbody>
            </table>
            {{ $clients->links() }}
          
         </div>
        </div>
            {{ $clients->links() }}
@stop