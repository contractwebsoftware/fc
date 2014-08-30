@section('content')

    <div class="row">
        <div class="col-xs-12">
                <h2>Edit Customer</h2>
                <hr>
        </div>
        <div class="col-xs-12">
            {{ Form::open(['action'=>'AdminController@postUpdate','class'=>'form-horizontal','role'=>'form']) }}
                {{ Form::hidden("client[id]",$client->id) }}
                {{ Form::hidden('client_id',$client->id) }}
                {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
        
        
                <fieldset>
                        <legend>Customer Information</legend>
                        

                        <div class="row form-group">
                            <div class="col-sm-12">
                                Client plans for the cremated remains:<br />
                                <select name="cremains_info[package_plan]" id="package_plan" class="form-control">
                                    <option value="0" {{ ($client->CremainsInfo->package_plan=="0"?'selected':'') }}>Select A Plan</option>
                                    <option value="1" {{ ($client->CremainsInfo->package_plan=="1"?'selected':'') }}>Plan A</option>
                                    <option value="2" {{ ($client->CremainsInfo->package_plan=="2"?'selected':'') }}>Plan B</option>
                                </select><br />
                                <!--
                                <strong>Plan A Description</strong>:<br />
                                Basic cremation services, care of your loved one in climatically controlled environment, local transportation, obtaining signatures and filing the death certificate, securing permits from the state, ordering certified copies of the death certificate, preparation for shipping of the urn with cremains inside the U.S. (Shipping for additional cost) Basic urn provided or you may chose an urn from our catalog for an additional cost.<br /><br />

                                <strong>Plan B Description</strong>:<br />
                                Same as plan A, but choose one of the Urns from our list, included in the cost.<br /><br />
                                -->

                                <b><u>Plan Additions</u></b><br />
                                <?php

                                    if($provider->pricing_options->custom1_text!='')echo '<div class="price-options included-'.$provider->pricing_options->custom1_included.'">
                                                 <label for="custom1_req">'.$provider->pricing_options->custom1_text.': $'.$provider->pricing_options->custom1.'</label> 
                                                 <input id="custom1_req" type="checkbox" '.($provider->pricing_options->custom1_req=='Y'?'checked=checked readonly':'').' class="required-'.$provider->pricing_options->custom1_req.'" name="cremains_info[custom1]" '.($client->CremainsInfo->custom1=="1"?'checked=checked':'').' value="1" />
                                                 </div>';


                                    if($provider->pricing_options->custom2_text!='')echo '<div class="price-options included-'.$provider->pricing_options->custom2_included.'">
                                                 <label for="custom2_req">'.$provider->pricing_options->custom2_text.': $'.$provider->pricing_options->custom2.'</label> 
                                                 <input id="custom2_req"  type="checkbox" '.($provider->pricing_options->custom2_req=='Y'?'checked=checked readonly':'').' class="required-'.$provider->pricing_options->custom2_req.'" name="cremains_info[custom2]" '.($client->CremainsInfo->custom2=="1"?'checked=checked':'').' value="1" /> 
                                                 </div>'; 

                                    if($provider->pricing_options->custom3_text!='')echo '<div class="price-options included-'.$provider->pricing_options->custom3_included.'">
                                                 <label for="custom3_req">'.$provider->pricing_options->custom3_text.': $'.$provider->pricing_options->custom3.'</label> 
                                                 <input id="custom3_req" type="checkbox" '.($provider->pricing_options->custom3_req=='Y'?'checked=checked readonly':'').' class="required-'.$provider->pricing_options->custom3_req.'" name="cremains_info[custom3]" '.($client->CremainsInfo->custom3=="1"?'checked=checked':'').' value="1" />                                    
                                                 </div>'; 
                               ?>

                                <br /><br />
                            </div>
                        </div>


                        <div class="row form-group">
                            <div class="col-sm-12">
                                Deceased status:<br />
                                <input name="deceased_info[medical_donation]" type="checkbox" value="1" {{ ($client->DeceasedInfo->medical_donation=="1"?'checked':'') }}> Medical Donation 
                                    <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Selecting a status helps define for the provider the type of help needed, it can always be changed at any time select which one best fits your situation at this time.">?</a><br />                    
                                <select name="deceased_info[cremation_reason]" class="form-control">
                                    <option value="planning_for_future" {{ ($client->DeceasedInfo->cremation_reason=="planning_for_future"?'selected':'') }}>Planning for Future</option>
                                    <option value="a_death_is_pending" {{ ($client->DeceasedInfo->cremation_reason=="a_death_is_pending"?'selected':'') }}>A death is pending</option>
                                    <option value="a_death_has_occurred" {{ ($client->DeceasedInfo->cremation_reason=="a_death_has_occurred"?'selected':'') }}>A death has occurred</option>
                                </select>                    

                                <br />
                                <font style="color:red;font-weight:bold;">If the Death has occurred, Please CALL US now, so we can begin to help.</font><br />

                            </div>
                        </div>

                       
                        
                        
                        
                        

                                        
                                        
                                        
                                        
                                        
            </fieldset>
            {{ Form::close() }}
        </div>
    </div>
@stop