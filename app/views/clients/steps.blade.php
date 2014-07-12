@extends('layouts.client')
@section('content')
<div class="col-sm-10">
 
@if(Session::get('step')==1)
   
{{ Form::open(['action'=>'ClientController@postSteps2','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
        
        <fieldset>
            <div class="row">
                <div class="col-md-6"><h3 class="pull-left">Cremation Plans</h3></div>
                <div class="col-md-6"><a href="#" onclick="$('#select_location').slideToggle()" class="pull-right">Select a different location?</a></div>
            </div>
            <div id="select_location" style="display:none;" class="row pull-left">
                <div class="col-md-4">
                    <select id="state" name="state" class="pull-right">
                        <option value="">--</option>
                        <?php
                        //print_r($states);
                        foreach($states as $key=>$row){
                            echo '<option value="'.$row->name_shor.'">'.$row->name_long.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="city" name="city">
                        <option value="">--</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="zip" name="zip">
                        <option value="">--</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary" id="select_zip">Select Location</button>
                </div>
                <br style="float:none;clear:both;" /><Br />
            </div>
            
            <div class="row form-group">
                <div class="col-sm-12">
                    Please select the status:<br />
                    <input name="deceased_info[medical_donation]" type="checkbox" value="1" {{ ($client->DeceasedInfo->medical_donation=="1"?'checked':'') }}> Medical Donation 
                        <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Selecting a status helps define for the provider the type of help needed, it can always be changed at any time select which one best fits your situation at this time.">?</a><br />                    
                    <font style="color:red;font-weight:bold;">If the Death has occurred, Please CALL US now, so we can begin to help.</font><br />
                    <select name="deceased_info[cremation_reason]" class="form-control">
                        <option value="planning_for_future" {{ ($client->DeceasedInfo->cremation_reason=="planning_for_future"?'selected':'') }}>Planning for Future</option>
                        <option value="a_death_is_pending" {{ ($client->DeceasedInfo->cremation_reason=="a_death_is_pending"?'selected':'') }}>A death is pending</option>
                        <option value="a_death_has_occurred" {{ ($client->DeceasedInfo->cremation_reason=="a_death_has_occurred"?'selected':'') }}>A death has occurred</option>
                    </select>                    
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12">
                    <strong>Cremation Package A: ${{$provider->pricing_options->basic_cremation}}</strong> <br />
                    {{$provider->pricing_options->package_a_desc!=''?$provider->pricing_options->package_a_desc:'Basic Service Fee, care of your loved one in climatically controlled environment, obtaining Cremation Authorizations and filing the Death Certificate with State of California @ $585, Cash Advance of disposition permit $12.00, Crematory fee, Cremation container and Basic urn @ $190.' }}  <br /><br />

                    <strong>Cremation Package B: ${{$provider->pricing_options->premium_cremation}}</strong> <br />
                    {{$provider->pricing_options->package_b_desc!=''?$provider->pricing_options->package_b_desc:'Premium Package includes all services of Plan A plus an urn. Refer to the General Price List for our urn selection.' }}  <br /><br />

                    <strong><u>Pick A Plan</u></strong><br />
                    Please select a package from the drop down below:<br />
                    <select name="cremains_info[package_plan]" class="form-control">
                        <option value="1" {{ ($client->CremainsInfo->package_plan=="1"?'selected':'') }}>Plan A</option>
                        <option value="2" {{ ($client->CremainsInfo->package_plan=="2"?'selected':'') }}>Plan B</option>
                    </select><br />
                    <!--
                    <strong>Plan A Description</strong>:<br />
                    Basic cremation services, care of your loved one in climatically controlled environment, local transportation, obtaining signatures and filing the death certificate, securing permits from the state, ordering certified copies of the death certificate, preparation for shipping of the urn with cremains inside the U.S. (Shipping for additional cost) Basic urn provided or you may chose an urn from our catalog for an additional cost.<br /><br />

                    <strong>Plan B Description</strong>:<br />
                    Same as plan A, but choose one of the Urns from our list, included in the cost.<br /><br />
                    -->
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>
        
        <script src="{{ asset('js/jquery.chained.remote.min.js') }}"></script>
	<script>
            $("#city").remoteChained("#state", "{{action('StepController@getCities')}}");
            $("#zip").remoteChained("#city", "{{action('StepController@getZips')}}");
            $("#select_zip").click(function(){
                //alert('going to ?set_zip='+$("#zip").val());
                window.location.href="{{action('ClientController@getSaveZip')}}?set_zip="+$("#zip").val();
                return false;
            });
        </script>
    {{ Form::close() }}


@elseif(Session::get('step')==2)
    {{ Form::open(['action'=>'ClientController@postSteps3','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset>
            <h3>Certificate Information <p></p></h3>
            <div class="row form-group">
                <div class="col-sm-4"><input name="deceased_info[first_name]" type="text" placeholder="First Name" value="{{$client->DeceasedInfo->first_name}}" /></div>
                <div class="col-sm-4"><input name="deceased_info[middle_name]" type="text" placeholder="Middle" value="{{$client->DeceasedInfo->middle_name}}"  /></div>
                <div class="col-sm-4"><input name="deceased_info[last_name]" type="text" placeholder="Last Name" value="{{$client->DeceasedInfo->last_name}}" /></div>
            </div>
            <div class="row form-group">   
                <div class="col-sm-12"><input name="deceased_info[aka]" type="text" placeholder="Also Known As (First/Middle/Last)" value="{{$client->DeceasedInfo->aka}}"/></div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-12"><input name="deceased_info[ssn]" type="text" placeholder="Social Security Number (SSN) - Optional" value="{{$client->DeceasedInfo->ssn}}"/></div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-12"><input name="deceased_info[address]" type="text" placeholder="Street Address" value="{{$client->DeceasedInfo->address}}"/></div>
            </div>
            <div class="row form-group">     
                <div class="col-sm-12"><input name="deceased_info[apt]" type="text" placeholder="Apartment/Suite" value="{{$client->DeceasedInfo->apt}}"/></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4"><input name="deceased_info[city]" type="text" placeholder="City" value="{{$client->DeceasedInfo->city}}"/></div>
                <div class="col-sm-4"><input name="deceased_info[state]" type="text" placeholder="State" value="{{$client->DeceasedInfo->state}}"/></div>
                <div class="col-sm-4"><input name="deceased_info[zip]" type="text" placeholder="ZIP" value="{{$client->DeceasedInfo->zip}}"/></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-6"><input name="deceased_info[county]" type="text" placeholder="County of Residence" value="{{$client->DeceasedInfo->county}}"/></div>
                <div class="col-sm-6"><input name="deceased_info[yrs_in_county]" type="text" placeholder="Yrs In County" value="{{$client->DeceasedInfo->yrs_in_county}}"/></div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-6"><input name="deceased_info[dob]" id="dob" type="text" placeholder="Date of Birth YYYY-MM-DD" value="{{$client->DeceasedInfo->dob}}" /></div>
                <div class="col-sm-6">
                    <select name="deceased_info[gender]" class="form-control">
                        <option selected="selected" value="">Gender</option>
                        <option value="Male" {{ ($client->DeceasedInfo->gender=="Male"?'selected':'') }}>Male</option>
                        <option value="Female" {{ ($client->CremainsInfo->gender=="Female"?'selected':'') }}>Female</option>
                    </select>
                </div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-12"><input name="deceased_info[birth_city_state]" type="text" placeholder="City and State of Birth" value="{{$client->DeceasedInfo->birth_city_state}}"/></div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-6">
                    <select name="deceased_info[marriage_status]" class="form-control">
                        <option value="">Marital Status</option>
                        <option value="Never Married" {{ ($client->DeceasedInfo->marriage_status=="Never Married"?'selected':'') }}>Never Married</option>
                        <option value="Married" {{ ($client->DeceasedInfo->marriage_status=="Married"?'selected':'') }}>Married</option>
                        <option value="CA Registered Dom. Partner" {{ ($client->DeceasedInfo->marriage_status=="CA Registered Dom. Partner"?'selected':'') }}>CA Registered Dom. Partner</option>
                        <option value="Divorced" {{ ($client->DeceasedInfo->marriage_status=="Divorced"?'selected':'') }}>Divorced</option>
                        <option value="Widowed" {{ ($client->DeceasedInfo->marriage_status=="Widowed"?'selected':'') }}>Widowed</option>
                        <option value="Unknown" {{ ($client->DeceasedInfo->marriage_status=="Unknown"?'selected':'') }}>Unknown</option>
                    </select>
                </div>
                <div class="col-sm-6"><input name="deceased_info[phone]" type="text" placeholder="Phone Number"  value="{{$client->DeceasedInfo->phone}}"/></div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-12">
                    <select name="deceased_info[race]" class="form-control">
                        <option value="">Race</option>
                        <option value="Hispanic or Latino" {{ ($client->DeceasedInfo->race=="Hispanic or Latino"?'selected':'') }}>Hispanic or Latino</option>
                        <option value="American Indian or AK Native" {{ ($client->DeceasedInfo->race=="American Indian or AK Native"?'selected':'') }}>American Indian or AK Native</option>
                        <option value="Asian" {{ ($client->DeceasedInfo->race=="Asian"?'selected':'') }}>Asian</option>
                        <option value="Black or African American" {{ ($client->DeceasedInfo->race=="Black or African American"?'selected':'') }}>Black or African American</option>
                        <option value="Native Hawaiian or other Pac. Islander" {{ ($client->DeceasedInfo->race=="Native Hawaiian or other Pac. Islander"?'selected':'') }}>Native Hawaiian or other Pac. Islander</option>
                        <option value="White or European" {{ ($client->DeceasedInfo->race=="White or European"?'selected':'') }}>White or European</option>
                    </select>
                </div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-6">
                    Items with potential additional fees<br />
                    <select name="deceased_info[weight]" class="form-control">
                         <option value="">Weight</option>
                         <option value="weight_lt_250" {{ ($client->DeceasedInfo->weight=="weight_lt_250"?'selected':'') }} >Weight less than 250lbs - Add ${{$provider->pricing_options->weight_lt_250}}</option>
                         <option value="weight_lt_300" {{ ($client->DeceasedInfo->weight=="weight_lt_300"?'selected':'') }} >Weight 251-300lbs - Add ${{$provider->pricing_options->weight_lt_300}}</option>
                         <option value="weight_lt_350" {{ ($client->DeceasedInfo->weight=="weight_lt_350"?'selected':'') }} >Weight 301-350lbs - Add ${{$provider->pricing_options->weight_lt_350}}</option>
                         <option value="weight_gt_350" {{ ($client->DeceasedInfo->weight=="weight_gt_350"?'selected':'') }} >Weight 351lbs - Add ${{$provider->pricing_options->weight_gt_350}}</option>
                     </select>
                </div>                
                <div class="col-sm-6"><br />
                    <select name="deceased_info[has_pace_maker]" class="form-control">
                        <option value="">Does deceased have a pacemaker?</option>
                        <option value="0" {{ ($client->DeceasedInfo->has_pace_maker=="0"?'selected':'') }}>Deceased DOES NOT have a pacemaker</option>
                        <option value="1" {{ ($client->DeceasedInfo->has_pace_maker=="1"?'selected':'') }}>Deceased DOES have a pacemaker- Add ${{$provider->pricing_options->pacemaker}}</option>
                    </select>
                </div>                        
            </div>
            <br />
                    
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset> 
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
    
        <script>
            $("#dob").datepicker( {
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });
        </script>
    {{ Form::close() }}


@elseif(Session::get('step')==3)
    {{ Form::open(['action'=>'ClientController@postSteps4','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset>
            <h3>Life Data <p>Additional information about the deceased</p></h3>
            
            <div class="row form-group">
                <div class="col-sm-6">     
                    School Information<Br />
                    <select name="deceased_info[schooling_level]" class="form-control">
                            <option value="">(Select One)</option>
                            <option value="No formal schooling" {{ ($client->DeceasedInfo->schooling_level=="No formal schooling"?'selected':'') }}>No formal schooling</option>
                            <option value="Elementary School" {{ ($client->DeceasedInfo->schooling_level=="Elementary School"?'selected':'') }}>Elementary School</option>
                            <option value="Middle School" {{ ($client->DeceasedInfo->schooling_level=="Middle School"?'selected':'') }}>Middle School</option>
                            <option value="High School" {{ ($client->DeceasedInfo->schooling_level=="High School"?'selected':'') }}>High School</option>
                            <option value="Some college, no degree" {{ ($client->DeceasedInfo->schooling_level=="Some college, no degree"?'selected':'') }}>Some college, no degree</option>
                            <option value="Associates Degree" {{ ($client->DeceasedInfo->schooling_level=="Associates Degree"?'selected':'') }}>Associates Degree</option>
                            <option value="Bachelors Degree" {{ ($client->DeceasedInfo->schooling_level=="Bachelors Degree"?'selected':'') }}>Bachelors Degree</option>
                            <option value="Masters Degree" {{ ($client->DeceasedInfo->schooling_level=="Masters Degree"?'selected':'') }}>Masters Degree</option>
                            <option value="Doctorate" {{ ($client->DeceasedInfo->schooling_level=="Doctorate"?'selected':'') }}>Doctorate</option>
                            <option value="Unknown" {{ ($client->DeceasedInfo->schooling_level=="Unknown"?'selected':'') }}>Unknown</option>
                    </select>
                </div>
                <div class="col-sm-6"> 
                    Military<Br />
                    <select name="deceased_info[military]" class="form-control">
                            <option value="">(Select One)</option>
                            <option value="Yes" {{ ($client->DeceasedInfo->military=="Yes"?'selected':'') }}>Yes</option>
                            <option value="No" {{ ($client->DeceasedInfo->military=="No"?'selected':'') }}>No</option>
                            <option value="Unknown" {{ ($client->DeceasedInfo->military=="Unknown"?'selected':'') }}>Unknown</option>
                    </select>
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-12"><input name="deceased_info[occupation]" value="{{$client->DeceasedInfo->occupation}}" type="text" placeholder="Usual occupation for most of life" ></div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-6"><input name="deceased_info[business_type]" value="{{$client->DeceasedInfo->business_type}}" type="text" placeholder="Business Type or Industry"></div>                   
                <div class="col-sm-6"><input name="deceased_info[years_in_occupation]" value="{{$client->DeceasedInfo->years_in_occupation}}" type="text" placeholder="Yrs Occupation" /></div> 
            </div>
            
            
            <strong>Family Information</strong><br />
            <i>Surviving spouse/SRDP</i><br />
            
            <div class="row form-group">        
                <div class="col-sm-5"><input name="deceased_family_info[srdp_first_name]" value="{{$client->DeceasedFamilyInfo->srdp_first_name}}" type="text" placeholder="First Name"></div>
                <div class="col-sm-2"><input name="deceased_family_info[srdp_middle_name]" value="{{$client->DeceasedFamilyInfo->srdp_middle_name}}" type="text" placeholder="Middle" ></div>
                <div class="col-sm-5"><input name="deceased_family_info[srdp_last_name]" value="{{$client->DeceasedFamilyInfo->srdp_last_name}}" type="text" placeholder="Maiden Name" ></div>
            </div>

            <i>Father</i><br />
            
            <div class="row form-group">        
                <div class="col-sm-5"><input name="deceased_family_info[fthr_first_name]" value="{{$client->DeceasedFamilyInfo->fthr_first_name}}" type="text" placeholder="First Name" ></div>
                <div class="col-sm-2"><input name="deceased_family_info[fthr_middle_name]" value="{{$client->DeceasedFamilyInfo->fthr_middle_name}}" type="text" placeholder="Middle" ></div>
                <div class="col-sm-5"><input name="deceased_family_info[fthr_last_name]" value="{{$client->DeceasedFamilyInfo->fthr_last_name}}" type="text" placeholder="Last Name" ></div>
            </div>

            <div class="row form-group">
                <div class="col-sm-12"><input name="deceased_family_info[fthr_birth_city]" value="{{$client->DeceasedFamilyInfo->fthr_birth_city}}" type="text" placeholder="Father's birth state" ></div>
            </div>
                        
            <i>Mother</i><br />
            
            <div class="row form-group">        
                <div class="col-sm-5"><input name="deceased_family_info[mthr_first_name]" value="{{$client->DeceasedFamilyInfo->mthr_first_name}}" type="text" placeholder="First Name" ></div>
                <div class="col-sm-2"><input name="deceased_family_info[mthr_middle_name]" value="{{$client->DeceasedFamilyInfo->mthr_middle_name}}" type="text" placeholder="Middle" ></div>
                <div class="col-sm-5"><input name="deceased_family_info[mthr_last_name]" value="{{$client->DeceasedFamilyInfo->mthr_last_name}}" type="text" placeholder="Maiden Name" ></div>
            </div>
            
            <div class="row form-group">
                    <div class="col-sm-12"><input name="deceased_family_info[mthr_birth_city]" value="{{$client->DeceasedFamilyInfo->mthr_birth_city}}" type="text" placeholder="Mothers's birth state" ></div>
            </div>
                  
               
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>        
    {{ Form::close() }}

@elseif(Session::get('step')==4)
    {{ Form::open(['action'=>'ClientController@postSteps5','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset>
            <h3>Location of Pending Death <p>Please enter the location of the pending death or deceased.</p></h3>
            Location of Deceased:<br>
            <div class="row form-group">
                <div class="col-sm-5">
                    <select name="deceased_info_present_loc[type_of_location]" class="form-control pull-left">  
                        <option value="">   Type  of  Location  </option>  
                        <option value="Hospital" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Hospital"?'selected':'') }}> Hospital  </option> 
                        <option value="Coroner" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Coroner"?'selected':'') }}> Coroner  </option> 
                        <option value="Assisted  Living" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Assisted  Living"?'selected':'') }}> Assisted  Living </option>
                        <option value="Residence" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Residence"?'selected':'') }}> Residence </option> 
                        <option value="Other" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Other"?'selected':'') }}> Other </option> 
                    </select> &nbsp; 
                    <a href="#" data-toggle="tooltip" style="position:absolute;top:0px;right:0px;" data-placement="bottom" class="tooltips pull-right" title="Although all field are required it may be information unknown at the present time. This information will eventually be needed to complete the Death Certificate. Your provider will help you with this information.">?</a>
                    <br class="clear" />
                </div>                
                <div class="col-sm-7">
                    <input name="deceased_info_present_loc[location]" value="{{$client->DeceasedInfoPresentLoc->location}}" type="text" placeholder="Location Name" />
                </div>
            </div>
            <div class="row form-group">    
                <div class="col-sm-12">
                    <Br />
                    <strong>Address of deceased location:</strong><br />
                    <strong style="color: #FF0000">All fields are required</strong>
                    <input name="deceased_info_present_loc[address]" value="{{$client->DeceasedInfoPresentLoc->address}}" type="text" placeholder="Address">
                </div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-4">
                    <input name="deceased_info_present_loc[city]" value="{{$client->DeceasedInfoPresentLoc->city}}" type="text" placeholder="City">
                </div>
                <div class="col-sm-4">
                    <input name="deceased_info_present_loc[state]" value="{{$client->DeceasedInfoPresentLoc->state}}" type="text" placeholder="State">
                </div>
                <div class="col-sm-4">
                    <input name="deceased_info_present_loc[zip]" value="{{$client->DeceasedInfoPresentLoc->zip}}" type="text" placeholder="ZIP">
                </div>
            </div>
    
            
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>        
    {{ Form::close() }}

@elseif(Session::get('step')==5)
    {{ Form::open(['action'=>'ClientController@postSteps6','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset class="step-line-of-authority">
            <h3>Line of Authority 
                <p>Are you the right person to authorize cremation 
                    <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Some states require more than one person at the same level of authority to sign the cremation authorization. Additional signatures will be obtained by your provider through email signature systems.">?</a>
                </p>
            </h3>
            <div class="row form-group">
                <div class="col-sm-12">
                    <input type="radio" id="relationship0" name="client[relationship]" value="" {{ ($client->relationship==""?'checked="checked"':'') }} >
                        <label for="relationship0" class="">(Select One)</label>
                    <input type="radio" id="relationship1" name="client[relationship]" value="Making arrangements for myself" {{ ($client->relationship=="Making arrangements for myself"?'checked="checked"':'') }} >
                        <label for="relationship1">Making arrangements for myself</label>
                    <input type="radio" id="relationship2" name="client[relationship]" value="Agent or durable power of attorney" {{ ($client->relationship=="Agent or durable power of attorney"?'checked="checked"':'') }}>
                        <label for="relationship2">Agent or durable power of attorney</label>
                    <input type="radio" id="relationship3" name="client[relationship]" value="Spouse (legally married)" {{ ($client->relationship=="Spouse (legally married)"?'checked="checked"':'') }}>
                        <label for="relationship3">Spouse (legally married)</label>
                    <input type="radio" id="relationship4" name="client[relationship]" value="Majority of surviving children" {{ ($client->relationship=="Majority of surviving children"?'checked="checked"':'') }}>
                        <label for="relationship4">Majority of surviving children</label>
                    <input type="radio" id="relationship5" name="client[relationship]" value="Surviving parents" {{ ($client->relationship=="Surviving parents"?'checked="checked"':'') }}>
                        <label for="relationship5">Surviving parents</label>
                    <input type="radio" id="relationship6" name="client[relationship]" value="Majority of surviving brothers and sisters" {{ ($client->relationship=="Majority of surviving brothers and sisters"?'checked="checked"':'') }}>
                        <label for="relationship6">Majority of surviving brothers and sisters</label>
                    <input type="radio" id="relationship7" name="client[relationship]" value="Majority of surviving nieces and nephews" {{ ($client->relationship=="Majority of surviving nieces and nephews"?'checked="checked"':'') }}>
                        <label for="relationship7">Majority of surviving nieces and nephews</label>
                    <input type="radio" id="relationship8" name="client[relationship]" value="Majority of surviving next of kin" {{ ($client->relationship=="Majority of surviving next of kin"?'checked="checked"':'') }}>
                        <label for="relationship8">Majority of surviving next of kin</label>
                    <input type="radio" id="relationship9" name="client[relationship]" value="None of these, but have legal right to authorize" {{ ($client->relationship=="None of these, but have legal right to authorize"?'checked="checked"':'') }}>
                        <label for="relationship9">None of these, but have legal right to authorize</label>
                    <input type="radio" id="relationship10" name="client[relationship]" value="None of these - just helping a friend" {{ ($client->relationship=="None of these - just helping a friend"?'checked="checked"':'') }}>
                        <label for="relationship10">None of these - just helping a friend</label>	
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>        
    {{ Form::close() }}

@elseif(Session::get('step')==6)
    {{ Form::open(['action'=>'ClientController@postSteps7','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset class="step-authorized-person">
            <div class="row form-group">
                <div class="col-sm-12">
                    <h3>Authorized Person <p>Person authorized to make the arrangements &nbsp; <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Some states refer to this person as the informant. This is the person providing the information for the Death Certificate and the one authorized to request any changes needed.">?</a></p></h3>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-5"><input type="text" name="client[first_name]" id="client_first_name" value="{{($client->first_name=="unregistered"?'':$client->first_name)}}" placeholder="First Name" ></div>
                <div class="col-sm-2"><input type="text" name="client[middle_name]" id="client_middle_name" value="{{$client->middle_name}}" placeholder="Middle" ></div>       
                <div class="col-sm-5"><input type="text" name="client[last_name]" id="client_last_name" value="{{($client->last_name=="unregistered"?'':$client->last_name)}}" placeholder="Last Name" ></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-9"><input type="text" name="client[address]" id="client_address" value="{{$client->address}}" placeholder="Street Address" ></div>
                <div class="col-sm-3"><input type="text" name="client[apt]" id="client_apt" value="{{$client->apt}}" placeholder="Apartment/Suite" ></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12"><input type="text" name="client[phone]" id="client_phone" value="{{$client->phone}}" placeholder="Phone Number" ></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-5"><input type="text" name="client[city]" id="client_city" value="{{$client->city}}" placeholder="City" ></div>
                <div class="col-sm-2"><input type="text" name="client[state]" id="client_state" value="{{$client->state}}" placeholder="State" ></div>       
                <div class="col-sm-5"><input type="text" name="client[zip]" id="client_zip" value="{{$client->zip}}" placeholder="ZIP" ></div>
            </div> 
            <div class="row form-group">
                <div class="col-sm-12">Your plans for the cremated remains &nbsp; <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Plans for the cremains are not always requested by the state, if a permit is issued by the state for the cremains then this information is most often requested.">?</a></div>                
            </div>
            <div class="row form-group">
                <div class="col-sm-12">
                    <select name="cremains_info[cremain_plan]" class="form-control"> 
                    <option value="burial" {{ ($client->CremainsInfo->cremain_plan=="burial"?'selected':'') }}> Burial </option> 
                    <option value="kept_at_residence" {{ ($client->CremainsInfo->cremain_plan=="kept_at_residence"?'selected':'') }}> Kept at residence </option> 
                    <option value="scatter_on_land" {{ ($client->CremainsInfo->cremain_plan=="scatter_on_land"?'selected':'') }}> We scatter on land- Add ${{$provider->pricing_options->scatter_on_land}} </option> 
                    <option value="you_scatter_on_land" {{ ($client->CremainsInfo->cremain_plan=="you_scatter_on_land"?'selected':'') }}> You scatters on land </option> 
                    <option value="scatter_at_sea" {{ ($client->CremainsInfo->cremain_plan=="scatter_at_sea"?'selected':'') }}> We scatter at sea- Add ${{$provider->pricing_options->scatter_at_sea}} </option> 
                    <option value="you_scatter_on_sea" {{ ($client->CremainsInfo->cremain_plan=="you_scatter_on_sea"?'selected':'') }}> You scatter at sea </option> 
                </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12"><br />
                    Name and Address of person(s) who will keep the cremated remains at their residence, or county and state of ocean water if scatter at sea:<br>
                    <input type="checkbox" id="keeper_of_cremains">
                    <label for="keeper_of_cremains">Use address above</label><br>
                    <textarea name="cremains_info[keeper_of_cremains]" rows="2" cols="20" id="keeper_of_cremains_field">{{$client->CremainsInfo->keeper_of_cremains}}</textarea>
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset> 
        <script>
            function updateAddr(){
                var html = $('#client_first_name').val() +" "+ $('#client_middle_name').val() +" "+ $('#client_last_name').val() + "\n"+
                        $('#client_address').val() +" "+ $('#client_city').val() +", "+ $('#client_state').val() +" "+ $('#client_zip').val() + "\n" +
                        "Apt: "+$('#client_apt').val() +", phone: "+ $('#client_phone').val();
                if($('#keeper_of_cremains').prop('checked'))$('#keeper_of_cremains_field').val(html);
            }
    
            $(function(){
                $('#keeper_of_cremains').click(function(){ updateAddr(); });
                $('.step-authorized-person').on('change', function(){ updateAddr() });
                $('.step-authorized-person').on('keyup', function(){ updateAddr() });
            });
        </script>
    {{ Form::close() }}

@elseif(Session::get('step')==7)
    {{ Form::open(['action'=>'ClientController@postSteps8','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset>
            <h3>Certified Death Certificates <p>Please tell us how you want to receive the death certificates</p></h3>
            <div class="row form-group">
                <div class="col-sm-12">
                    How many copies of the death certificate do you need? &nbsp; - &nbsp; 
                    Each Certificate: <span id="MainContent_FormView9_FormView19_deathcertCEPLabel" style="font-weight:bold;">${{$provider->pricing_options->deathcert_each}}</span><br>
                    <input name="cremains_info[number_of_certs]" type="text" value="{{$client->CremainsInfo->number_of_certs}}" />
                    
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-9">
                    What would you like us to do with the certificates?<br />
                    <select name="cremains_info[cert_plan]" class="form-control">
                        <option value="deathcert_wurn" {{ ($client->CremainsInfo->cert_plan=="deathcert_wurn"?'selected':'') }}>Mail certificate(s) with urn- Add ${{$provider->pricing_options->deathcert_wurn}}</option>
                        <option value="deathcert_cep" {{ ($client->CremainsInfo->cert_plan=="deathcert_cep"?'selected':'') }}>Mail certificate(s) separately- Add ${{$provider->pricing_options->deathcert_cep}}</option>
                        <option value="deathcert_pickup" {{ ($client->CremainsInfo->cert_plan=="deathcert_pickup"?'selected':'') }}>Pick up certificate(s) at our office- Add ${{$provider->pricing_options->deathcert_pickup}}</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips pull-right" title="If the certificates are not going to be shipped with the urn then the shipping page is not required.">?</a>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12"><br>
                    <a id="MainContent_LinkButton2" href='' >How many certified copies do I need?</a><br>
                    <i><span class="auto-style9">Window not opening? Try disabling your popup blocker</span></i><br>
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>        
    {{ Form::close() }}

@elseif(Session::get('step')==8)
    {{ Form::open(['action'=>'ClientController@postSteps9','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        <fieldset>
            <h3>Shipping Information <p>Shipping Information for the cremains</p></h3>
            <div class="row form-group">
                <div class="col-sm-5"><input type="text" placeholder="First Name" name="cremains_info[shipto_first_name]" value="{{$client->CremainsInfo->shipto_first_name}}" ></div>
                <div class="col-sm-2"><input type="text" placeholder="Middle" name="cremains_info[shipto_middle_name]" value="{{$client->CremainsInfo->shipto_middle_name}}" ></div>       
                <div class="col-sm-5"><input type="text" placeholder="Last Name" name="cremains_info[shipto_last_name]" value="{{$client->CremainsInfo->shipto_last_name}}" ></div>
            </div> 
            <div class="row form-group">    
                <div class="col-sm-7"><input type="text" placeholder="Street Address" name="cremains_info[shipto_address]" value="{{$client->CremainsInfo->shipto_address}}" ></div>
                <div class="col-sm-5"><input type="text" placeholder="Apartment/Suite" name="cremains_info[shipto_apt]" value="{{$client->CremainsInfo->shipto_apt}}" ></div>
            </div> 
            <div class="row form-group">         
                <div class="col-sm-5"><input type="text" placeholder="City" name="cremains_info[shipto_city]" value="{{$client->CremainsInfo->shipto_city}}" ></div>
                <div class="col-sm-2"><input type="text" placeholder="State" name="cremains_info[shipto_state]" value="{{$client->CremainsInfo->shipto_state}}" ></div>       
                <div class="col-sm-5"><input type="text" placeholder="ZIP" name="cremains_info[shipto_zip]" value="{{$client->CremainsInfo->shipto_zip}}" ></div>
            </div>  
            <div class="row form-group">          
                <div class="col-sm-6"><input type="text" placeholder="Phone Number" name="cremains_info[shipto_phone]" value="{{$client->CremainsInfo->shipto_phone}}" ></div>   
                <div class="col-sm-6"><input type="text" placeholder="Email Address" name="cremains_info[shipto_email]" value="{{$client->CremainsInfo->shipto_email}}" > </div>    
            </div> 
            <div class="row form-group">         
                <div class="col-sm-12"><span id="shippingtext" style="FONT-SIZE: small; FONT-WEIGHT: bold; text-align: center">We can only ship by registered U.S. mail. UPS, Fedex, or other providers will NOT ship remains.</span></div>
            </div>
            
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>        
    {{ Form::close() }}

@elseif(Session::get('step')==9)
    {{ Form::open(['action'=>'ClientController@postSteps10','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
        
        <fieldset>
            
            <h3>FTC Disclosures <p>Legal Authorizations</p></h3>
            <div class="row form-group">
                <div class="col-sm-12" style="padding-left:30px;padding-right:0px;">
                    <a href="#" onclick="$('#ftc_disclosures').slideToggle();return false;">Read FTC Disclosures</a><br />
                    <div id="ftc_disclosures" style="height:300px;overflow-y:scroll;background-color:#fff;display:none;border:1px solid #999;">
			<table border="1" style="border: thin solid #C0C0C0"><tbody><tr><th align="center" class="auto-style3" scope="col">Item #</th><th class="auto-style2" scope="col">Description</th><th class="auto-style2" scope="col">Explanation</th></tr><tr><td align="center" class="auto-style3">1</td><td class="auto-style2">Purchaser received a printed General Price List prior to discussing or upon beginning discussion of, the prices of funeral goods or funeral services, the overall type of funeral disposition, or the specific funeral goods or funeral services offered by the funeral home.</td><td class="auto-style2">You were able to view and print a price list prior to choosing the services. This is to assure that no pressure was applied to have you choose a specific option.</td></tr><tr><td align="center" class="auto-style3">2</td><td class="auto-style2">Purchaser was advised that State law does not require embalming except in certain special cases. If embalming was provided for a fee, it was done with purchaser's approval or the permission of someone authorized to give approval. Purchaser was not told that embalming is required for direct cremation, immediate burial, or a closed casket funeral without viewing or visitation when refrigeration is available and when state or local law does not require embalming.</td><td class="auto-style2">This is about embalming, which is a service that we don't provide. It is to assure that no one talked you into an embalming service.</td></tr><tr><td align="center" class="auto-style3">3</td><td class="auto-style2">A prepaid benefits contract for the deceased, if provided, was applicable to the funeral or cremation service provided.</td><td class="auto-style2">Some people purchase prepaid funeral contracts. If you had one, and provided the details to us, this says we abided by it.</td></tr><tr><td align="center" class="auto-style3">4</td><td class="auto-style2">Applicable Only to Merchandise: Purchaser was advised that the only warranties, expressed or implied, extended in connection with any funeral goods sold with the funeral service were the express written warranties, if any, extended by the manufacturers thereof. No express warranties, and no warranties of merchantability or fitness for a particular purpose, were extended by the funeral home to purchaser with respect to those funeral goods.</td><td class="auto-style2">This says that we didn't make any promises about the Urn for fitness of use. Only the manufacturer provides a warranty for the Urn.</td></tr><tr><td align="center" class="auto-style3">5</td><td class="auto-style2">Valuables placed with the deceased: Purchaser was told that anything of sentimental or monetary value placed on the body would receive special care, but that the funeral home or mortuary accepts no responsibility for such items.</td><td class="auto-style2">This applies to burial (even though we only offer cremations, we still have to have it on the form). It says that if there were any watches or anything of sentimental or monetary value with the deceased, that we accept no responsibility for them.</td></tr><tr><td align="center" class="auto-style3">6</td><td class="auto-style2">I agree to indemnify and hold the Crematory harmless from any and all claims or damages, including damage to the retort(s) or injuries suffered by the Crematory’s employees, which arise from my failure to timely notify the Crematory of any mechanical or radioactive implants in the body of the Decedent.</td><td class="auto-style2">This applies to pace makers or other electronic devices. It says that if you did not inform us of a pacemaker or other device, that damages can result at the crematory, and you may be held responsible.</td></tr><tr><td align="center" class="auto-style3">7</td><td class="auto-style2">Items such as personal mementos, jewelry, dental gold and silver, prostheses and other foreign materials placed in the cremation chamber with the Decedent will either be destroyed or rendered unrecognizable. If any such items are recovered from the cremation chamber I authorize the Crematory to dispose of them.</td><td class="auto-style2">This applies to jewelry or personal items that may be with the deceased. It says that the crematory can dispose of them.</td></tr><tr><td align="center" class="auto-style3">8</td><td class="auto-style2">The Cremation Process. I acknowledge the following: The human body burns with the container, or other material in the cremation chamber. Some bone fragments are not combustible at the incineration temperature and, as a result, remain in the cremation chamber. During the cremation, the contents of the chamber may be moved to facilitate incineration. The chamber is composed of ceramic or other material which disintegrates slightly during each cremation and the product of that disintegration is commingled with the cremated remains. Nearly all of the contents of the cremation chamber, consisting of the cremated remains, disintegrated chamber material, and small amounts of residue from previous cremations, are removed together and crushed, pulverized, or ground to facilitate interment or scattering. Some residue remains in the cracks and uneven places of the chamber. Periodically, the accumulation of this residue is removed and interred in a dedicated cemetery property, or scattered at sea.</td><td class="auto-style2">This applies to the cremation process itself. Periodically the cremation chamber is cleaned and some small amount of ashes may remain. These are disposed of at a cemetary or scattered at sea.</td></tr><tr><td align="center" class="auto-style3">9</td><td class="auto-style2">Time of Cremation. The cremation will take place after all required permits are obtained, this completed and signed Authorization is received by the Crematory, and after any scheduled funeral ceremony at which the decedent’s body is to be present has been concluded. The Crematory will perform the cremation according to its schedule (unless a specific date and time is requested), and at it’s discretion, without obtaining any further authorizations or instructions, unless the right of the person signing this document to authorize the cremation is contested by someone. In that event the Crematory may delay the cremation while it determines whether and how to proceed. The normal cremation process may take a minimum of 8 working days to a possible 18 days.</td><td class="auto-style2">This applies to the cremation process. The crematory will cremate based on their schedule without obtaining any further authorizations other than those provided in the forms. This process can take from 8 to 18 working days.</td></tr><tr><td align="center" class="auto-style3">10</td><td class="auto-style2">Casket or Urn provided by family: Purchaser understands if they provided alternative container for burial or cremation, the mortuary is relieved of any liability in the event such container is found not to be of adequate strength or structural quality for intended use.</td><td class="auto-style2">If you provided your own urn, this relieves us of any liability if it is not strong enough or does not hold up to shipping.</td></tr><tr><td align="center" class="auto-style3">11</td><td class="auto-style2">I authorize the Crematory to release the cremated remains back to the Funeral Home to take the action I’ve indicated with respect to the cremated remains. For your convenience, we offer a minimum fiberboard urn to hold the cremated remains.</td><td class="auto-style2">This tells the crematory that they can return the cremated remains to us and we will be responsible for following your instructions for shipping the remains to you, or scattering or burial if you have asked us to do so.</td></tr><tr><td align="center" class="auto-style3">12</td><td class="auto-style2">I understand that if the remains are not picked up within twenty (20) days after the cremation, the Funeral Home may deliver the remains to a licensed cemetery for final disposition in a manner which may make the remains non-recoverable.</td><td class="auto-style2">This says that if you don't make arrangements for us to ship the cremated remains or pick them up, that after 20 days, we may properly dispose of them at a cemetary or by scattering.</td></tr><tr><td align="center" class="auto-style3">13</td><td class="auto-style2">Remains will be mailed via U.S. Postal Service, registered with return receipt requested. I understand that the Funeral Home is acting solely as my agent in mailing the remains, and I agree that the Funeral Home shall not be liable if the remains are lost or damaged while in the custody of the U.S. Postal Service.</td><td class="auto-style2">This authorizes us to ship the cremated remains to you via US Postal service and that we are not held liable if they do not deliver them.</td></tr><tr><td align="center" class="auto-style3">14</td><td class="auto-style2">I understand that the Funeral Home is acting solely as my agent as an accommodation to me in arranging for the scattering of the remains. I agree that the Funeral Home shall not be liable for any failure to properly scatter the remains.</td><td class="auto-style2">If you requested us to scatter the remains for you, this authorizes us to do so and says we are not held liable if someone feels it was not properly done.</td></tr><tr><td align="center" class="auto-style3">15</td><td class="auto-style2">Obligation of Crematory; Limitation on Damages. The obligation of the Crematory shall be limited to the cremation of the Decedent and the disposition of the cremated remains as directed herein. I agree to release and hold the Crematory, its affiliated companies and their employees and agents harmless from any and all loss, damages, liability or causes of action (including attorneys’ fees and costs of litigation) in connection with the cremation and disposition of the cremated remains as authorized herein, or the failure to properly identify the Decedent or to take possession of or make arrangements for the permanent disposition of the cremated remains. No warranties, express or implied, are made by the Crematory and damages shall be limited to the refund of the fee paid for the cremation.</td><td class="auto-style2">This says you are authorizing the crematory to cremate the remains. You hold them harmless if a mistake is made and that any damage charges are limited to the amount they were paid for this service.</td></tr><tr><td align="center" class="auto-style3">16</td><td class="auto-style2">Estimated costs: Certain charges may be estimated and if the difference between such estimates and such actual charges is less than $ 10.00, no refund to you or billing us for the difference will be made.</td><td class="auto-style2">This says that we have provided an estimated cost and if there is a difference of $10 either owed to us or to you, that we will call it good without extra billing for the difference.</td></tr></tbody></table><br>
                    </div>
                    By clicking submit, you agree that you have read and understand the disclosures listed above.
                </div>
            </div>
            
            <br />
            <h3>Payment Summary <p>Itemization of goods and services <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="If you need to change a choice that affects the summary then return to the page and change the selection your summary will update.">?</a></p></h3>

            <div class="row ">
                <div class="col-sm-12" style="background-color:#fff;">            
                    <table style="width:100%;empty-cells:show;">
                        <th>Description</th><th >Price</th>
                        <?php
                            if(is_array($client->sale_summary_r['report']))
                            foreach($client->sale_summary_r['report'] as $key=>$value){
                                echo '<tr><td>'.$value['desc'].'</td><td>'.$value['price'].'</td></tr>';
                            }
                        ?>                       
                            
                    </table>
                <div class="col-sm-12 pull-right" style="text-align:right;font-weight:bold;font-size:16px;">Total Summary: $<?=$client->sale_summary_r['total']?></div>
                <br class="clear"/><br class="clear"/>
                </div>
                
            </div>
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset>        
    {{ Form::close() }}              

@elseif(Session::get('step')==10)
    {{ Form::open(['action'=>'ClientController@postSteps11','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('step',Session::get('step')) }}
     
        <fieldset class="step-review-print">
            <h3>Review/Print Forms <p>You're almost done!</p></h3>
            <div class="row form-group {{($errors->first('Confirmed Legal Authorization Checkbox')!=''?'has-error has-feedback':'')}}">
                <div class="col-sm-12">
                    <input id="confirmed_legal_auth" type="checkbox" value="1" name="client[confirmed_legal_auth]" {{($client->confirmed_legal_auth=="1"?'checked=checked':'')}}>
                    <label for="confirmed_legal_auth">I confirm that the legally authorized person has provided these instructions</label><br>
                </div>
            </div>
            <div class="row form-group {{($errors->first('Your Name')!=''?'has-error has-feedback':'')}}">
                <div class="col-sm-12">
                    Type name here: <input type="text" class="form-control" id="confirmed_legal_auth_name" name="client[confirmed_legal_auth_name]" value="{{$client->confirmed_legal_auth_name}}"><br>
                    <input id="confirmed_correct_info" type="checkbox" value="1" name="client[confirmed_correct_info]" {{($client->confirmed_correct_info=="1"?'checked=checked':'')}}>
                    <label for="confirmed_correct_info" >I confirm that the information provided is correct</label><br>
                </div>
            </div>
            <div class="row form-group {{($errors->first('Your Initials')!=''?'has-error has-feedback':'')}}">
                <div class="col-sm-12">
                    Enter initials here: <input type="text" class="form-control" id="confirmed_correct_info_initial" name="client[confirmed_correct_info_initial]" value="{{$client->confirmed_correct_info_initial}}" />

                    <button class="btn btn-primary" id="download_forms">Download Forms</button>
                    <div id="loading_gif" style="display:none;">
                    <br>
                    <img src="/images/ajax-loader.gif"> <b>Downloading Forms... please wait</b>
                    </div>

                </div>
            </div>
            <Br /><Br />
            <div class="row form-group">
                <div class="col-sm-6"><button type="button" name="back" class="step_back_btn">Back</button><br class="clear" /></div>
                <div class="col-sm-6"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
            </div>
        </fieldset> 
        <script>
            function validate(){
                if(!$('#confirmed_legal_auth').prop('checked')){
                    alert('Please Mark The Confirm Legal Authorization Checkbox'); 
                    return false;
                }
                if($('#confirmed_legal_auth_name').val()==""){
                    alert('Please Enter Your Name'); 
                    return false;
                }
                if(!$('#confirmed_correct_info').prop('checked')){
                    alert('Please Mark The Confirm Information Is Correct Checkbox'); 
                    return false;
                }
                if($('#confirmed_correct_info_initial').val()==""){
                    alert('Please Enter Your Initials'); 
                    return false;
                }
            }
            $(function(){
                $('form').on('submit', function(){
                    return validate();
                });
            });
        </script>
    {{ Form::close() }}

@elseif(Session::get('step')==11)
 
        <fieldset>
            <h3>Thank You</h3>
            <div class="row form-group">
                <div class="col-sm-12">
                    	Thank you for using our service!<br /><br />

                        I understand your loss, and I hope that this simple process has made it easier as you pass through this very difficult time.<br /><br />

                        Funeral and Cremation Svc. of Orange Co. FD1567 <br />
                        Phone: 714-667-7991<br />
                        Fax: 714-639-8862<br /><br />

                        Email: <a href="mailto:forcremationsca@gmail.com">forcremationsca@gmail.com</a>
                </div>
            </div>            
        </fieldset>      

@endif               



</div>


@stop