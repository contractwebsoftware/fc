@section('content')

<div class="row" style="margin:0 5px;">
    <div class="col-xs-12">
        <h2>Edit Provider &nbsp; <i style="font-size:14px;">{{ $provider->business_name }}</i></h2>
        <hr>
    </div>



    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li class="{{$current_tab==''||$current_tab=='company_info'?'active':''}}"><a href="#company_info" role="tab" data-toggle="tab">Company Information</a></li>
        <li class="{{$current_tab=='client_links'?'active':''}}"><a href="#client_links" role="tab" data-toggle="tab">Provider Home Page</a></li>
        <li class="{{$current_tab=='provider_files'?'active':''}}"><a href="#provider_files" role="tab" data-toggle="tab">Forms</a></li>
        @if(Sentry::getUser()->role=='admin')
          <li class="{{$current_tab=='provider_zips'?'active':''}}"><a href="#provider_zips" role="tab" data-toggle="tab">Provider Locations</a></li>
          <li class="{{$current_tab=='customer_document_forms'?'active':''}}"><a href="#customer_document_forms" role="tab" data-toggle="tab">Document Builder</a></li>
          <li class="{{$current_tab=='provider_clients'?'active':''}}"><a href="#provider_clients" role="tab" data-toggle="tab">Clients</a></li>
          <li class="{{$current_tab=='provider_signatures'?'active':''}}"><a href="#provider_signatures" role="tab" data-toggle="tab">Signed Documents</a></li>
        @endif
        <li class="{{$current_tab=='provider_pricing'?'active':''}}"><a href="#provider_pricing" role="tab" data-toggle="tab">Pricing</a></li>
        <li class="{{$current_tab=='provider_urns'?'active':''}}"><a href="#provider_urns" role="tab" data-toggle="tab">Urns</a></li>
        <li class="{{$current_tab=='provider_help'?'active':''}}"><a href="#provider_help" role="tab" data-toggle="tab" style="color:green;">Help</a></li>

    </ul>

    <!-- Tab panes -->
    <div class="tab-content">


        <div class="tab-pane {{$current_tab==''||$current_tab=='company_info'?'active':''}}" id="company_info">
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                    {{ Form::open(['action'=>'AdminController@postUpdateProvider','class'=>'form-horizontal','role'=>'form']) }}
                            {{ Form::hidden("provider[id]",$provider->id) }}
                                
                                    <div class="form-group" {{(Sentry::getUser()->role=='admin')?'':'style="display:none;"'}}>
                                        <label  class="col-sm-2" for="provider_status">Provider Status</label>
                                        <div class="col-sm-10">
                                            <select name="provider[provider_status]" id="provider_status" class="form-control">
                                                <option value="0" {{ ($provider->provider_status=='0') ? ' selected' : '' }}>UnApproved</option>
                                                <option value="1" {{ ($provider->provider_status=='1') ? ' selected' : '' }}>Approved</option>
                                                <option value="2" {{ ($provider->provider_status=='2') ? ' selected' : '' }}>Deleted</option>
                                            </select>
                                        </div>
                                    </div>
                            
                                    <div class="form-group">
                                        <label  class="col-sm-2" for="provider_status">Provider Plan</label>
                                        <div class="col-sm-10">
                                            <div data-toggle="buttons" id="provider-plan-buttons">
                                                @if(Sentry::getUser()->role=='admin')
                                                    <label class="btn btn-primary {{($provider_plan_basic->id == $provider->plan_id?'active':'')}}" for="plan_basic"><input type="radio" name="provider[plan_id]" id="plan_basic" value="{{$provider_plan_basic->id}}" {{($provider_plan_basic->id == $provider->plan_id || $provider->plan_id==''?'checked':'')}}> &nbsp; <b>Basic</b> &nbsp; ${{$provider_plan_basic->price}} <sub>/m</sub></label>
                                                    <label class="btn btn-primary {{($provider_plan_premium->id == $provider->plan_id?'active':'')}}" for="plan_premium"><input type="radio" name="provider[plan_id]" id="plan_premium" value="{{$provider_plan_premium->id}}" {{($provider_plan_premium->id == $provider->plan_id?'checked':'')}}> &nbsp; <b>Premium</b> &nbsp; ${{$provider_plan_premium->price}} <sub>/m</sub></label>
                                                @elseif($provider_plan_basic->id == $provider->plan_id)
                                                    <label class="btn btn-primary {{($provider_plan_basic->id == $provider->plan_id?'active':'')}}" for="plan_basic"><input type="radio" name="provider[plan_id]" id="plan_basic" value="{{$provider_plan_basic->id}}" {{($provider_plan_basic->id == $provider->plan_id || $provider->plan_id==''?'checked':'')}}> &nbsp; <b>Basic</b> &nbsp; ${{$provider_plan_basic->price}} <sub>/m</sub></label>
                                                    <label class="btn btn-primary {{($provider_plan_premium->id == $provider->plan_id?'active':'')}}" for="plan_premium"><input type="radio" name="provider[plan_id]" id="plan_premium" value="{{$provider_plan_premium->id}}" {{($provider_plan_premium->id == $provider->plan_id?'checked':'')}}> &nbsp; <b>Premium</b> &nbsp; ${{$provider_plan_premium->price}} <sub>/m</sub></label>
                                                @elseif($provider_plan_premium->id == $provider->plan_id)
                                                    <label class="btn btn-primary {{($provider_plan_basic->id == $provider->plan_id?'active':'')}} disabled" for="plan_basic"><input type="radio" disabled name="provider[plan_id]" id="plan_basic" value="{{$provider_plan_basic->id}}" {{($provider_plan_basic->id == $provider->plan_id || $provider->plan_id==''?'checked':'')}}> &nbsp; <b>Basic</b> &nbsp; ${{$provider_plan_basic->price}} <sub>/m</sub></label>
                                                    <label class="btn btn-primary {{($provider_plan_premium->id == $provider->plan_id?'active':'')}}" for="plan_premium"><input type="radio" name="provider[plan_id]" id="plan_premium" value="{{$provider_plan_premium->id}}" {{($provider_plan_premium->id == $provider->plan_id?'checked':'')}}> &nbsp; <b>Premium</b> &nbsp; ${{$provider_plan_premium->price}} <sub>/m</sub></label>
                                                    &nbsp; <sub><i>If you wish to downgrade your account please contact us at <a href="mailto:forcremation@gmail.com?subject=Downgrading%20Account%20For%20{{$provider->email}}" target="_blank">forcremation@gmail.com</a></i></sub>
                                                @endif
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <script>
                                        $('#provider-plan-buttons label.btn-primary').each(function(){
                                            $('#provider-plan-buttons label.btn-primary').find('b').html('<i class="glyphicon glyphicon-unchecked"></i> &nbsp; Select ');
                                            $('#provider-plan-buttons label.btn-primary.active').find('b').html('<i class="glyphicon glyphicon-check"  style="color:#77CA77;"></i> &nbsp; Selected');
                                        });

                                        $('#provider-plan-buttons label.btn-primary').click(function(){
                                            $('#provider-plan-buttons label.btn-primary').find('b').html('<i class="glyphicon glyphicon-unchecked"></i> &nbsp; Select ');
                                            $(this).find('b').html('<i class="glyphicon glyphicon-check"  style="color:#77CA77;"></i> &nbsp; Selected');
                                        });
                                    </script>

                                    <div class="form-group" style="<?php if(Sentry::getUser()->role!='admin')echo 'display:none;'; ?>">
                                        <label  class="col-sm-2" for="freshbooks_billing_cost">FreshBooks Invoice Price</label>
                                        <div class="col-sm-10">
                                            <input type="text" placeholder="Invoice Price" name="provider[freshbooks_billing_cost]" id="provider" class="form-control" value="{{ $provider->freshbooks_billing_cost }}">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label  class="col-sm-2" for="provider_login">Provider Login</label>
                                        <div class="col-sm-4">
                                            <input type="text" placeholder="Login Email" name="provider_login" id="provider_login" class="form-control" value="{{ $fuser->email }}">
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" placeholder="Login Password" name="newpassword" class="form-control" value="">
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" placeholder="Confirm Password" name="newpassword_confirmation" class="form-control" value="">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label  class="col-sm-2" for="provider_login">Provider Contact Info</label>
                                        <div class="col-sm-5">
                                            <input type="text" placeholder="Contact First Name" name="provider[contact_first_name]" id="contact_first_name" class="form-control" value="{{ $provider->contact_first_name }}">
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="text" placeholder="Contact Last Name" name="provider[contact_last_name]" class="form-control" value="{{ $provider->contact_last_name }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                            <label  class="col-sm-2" for="business_name">Provider Name</label>
                                            <div class="col-sm-10">
                                                    <input type="text" placeholder="Business Name" name="provider[business_name]" id="provider" class="form-control" value="{{ $provider->business_name }}">
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label  class="col-sm-2" for="address">Company Address</label>
                                            <div class="col-sm-10"><textarea placeholder="Address" name="provider[address]" id="address" class="form-control" row="3">{{ $provider->address }}</textarea></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="city" placeholder="City" name="provider[city]" value="{{ $provider->city }}">
                                        </div>
                                        <div class="col-sm-3">
                                                <input type="text" class="form-control" id="state" placeholder="State" name="provider[state]" value="{{ $provider->state }}">
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="zip" placeholder="Zip" name="provider[zip]" value="{{ $provider->zip }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-2" for="website">Provider</label>
                                        <div class="col-sm-10"><input type="text" placeholder="Website" name="provider[website]" id="website" class="form-control" value="{{ $provider->website }}"></div>
                                    </div>
                                    <div class="form-group">
                                            <label  class="col-sm-2" for="default_for_state">Default Provider For State</label>
                                            <div class="col-sm-10">
                                                <select  placeholder="Default Provider For State" name="provider[default_for_state]" id="default_for_state" class="form-control" value="{{ $provider->default_for_state }}">
                                                    <option value="" {{ ($provider->default_for_state==''?'selected=selected':'') }}>Not A Default Provider</option>

                                                        @foreach($states as $key=>$state)
                                                            <option value="{{$state['name_shor']}}" {{ ($provider->default_for_state==$state['name_shor']?'selected=selected':'') }}>{{$state['name_long']}}</option>
                                                        @endforeach
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label  class="col-sm-2" for="email">Email</label>
                                            <div class="col-sm-10"><input type="email" placeholder="Email" name="provider[email]" id="email" class="form-control" value="{{ $provider->email }}"></div>
                                    </div>
                                    <div class="form-group">
                                            <label  class="col-sm-2" for="phone">Phone</label>
                                            <div class="col-sm-10"><input type="text" placeholder="Phone" name="provider[phone]" id="phone" class="form-control" value="{{ $provider->phone }}"></div>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-2" for="fax">Fax</label>
                                        <div class="col-sm-10"><input type="text" placeholder="Fax" name="provider[fax]" id="fax" class="form-control" value="{{ $provider->fax }}"></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2" for="fax">Administrative Coordinator</label>
                                        <div class="col-sm-5"><input type="text" placeholder="Administrative Coordinator First Name" name="provider[admin_fn]" id="admin_fn" class="form-control" value="{{ $provider->admin_fn }}" /></div>
                                        <div class="col-sm-5"><input type="text" placeholder="Administrative Coordinator Last Name" name="provider[admin_ln]" id="admin_ln" class="form-control" value="{{ $provider->admin_ln }}" /></div>
                                    </div>
                                    
                                    <div class="row"  style="<?php if(Sentry::getUser()->role!='admin')echo 'display:none;'; ?>">

                                        <div class="form-group" >
                                            <label  class="col-sm-2" for="freshbooks_clients_enabled"><b style="font-size:16px">Enable Freshbooks Integration</b></label>
                                            <div class="col-sm-10">
                                                <input type="checkbox" name="provider[freshbooks_clients_enabled]" id="freshbooks_clients_enabled" class="form-control" value="1" {{ ($provider->freshbooks_clients_enabled=='1'?'checked=checked':'') }} />
                                            </div>
                                        </div>

                                        <div id='freshbooks_settings'>

                                            <div class="form-group" >
                                                <label  class="col-sm-2" for="freshbooks_api_url">Freshbooks API Domain<Br />
                                                <i style="font-size:11px;font-weight:normal;">e.g. enter "forcremationcom" if your API url is https://<b>forcremationcom</b>.freshbooks.com/api/2.1/xml-in</i>
                                                </label>
                                                <div class="col-sm-10">
                                                    <input type="text" placeholder="API URL" name="provider[freshbooks_api_url]" id="freshbooks_api_url" class="form-control" value="{{ $provider->freshbooks_api_url }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label  class="col-sm-2" for="freshbooks_api_token">Freshbooks Authentication Token<Br />
                                                <i style="font-size:11px;">e.g. 95cad39d654646s2d2a2a119e6559</i></label>
                                                <div class="col-sm-10">
                                                    <input type="text" placeholder="API Key" name="provider[freshbooks_api_token]" id="freshbooks_api_token" class="form-control" value="{{ $provider->freshbooks_api_token }}">
                                                </div>
                                            </div>

                                            <div class="form-group" >
                                                <label  class="col-sm-2" for="freshbooks_clients_people">Allow Creation of Freshbooks People</label>
                                                <div class="col-sm-10">
                                                    <input type="checkbox" name="provider[freshbooks_clients_people]" id="freshbooks_clients_people" class="form-control" value="1" {{ ($provider->freshbooks_clients_people=='1'?'checked=checked':'') }} />
                                                </div>
                                            </div>
                                            <div class="form-group" >
                                                <label  class="col-sm-2" for="freshbooks_clients_invoice">Allow Creation of Freshbooks Invoices</label>
                                                <div class="col-sm-10">
                                                    <input type="checkbox" name="provider[freshbooks_clients_invoice]" id="freshbooks_clients_invoice" class="form-control" value="1" {{ ($provider->freshbooks_clients_invoice=='1'?'checked=checked':'') }} />
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <br />


                                    <div class="row"  style="<?php if(Sentry::getUser()->role!='admin')echo 'display:none;'; ?>">

                                        <div class="form-group" >
                                            <label  class="col-sm-2" for="freshbooks_clients_enabled"><b style="font-size:16px">RightSignature Integration</b><br />
                                            <i style="font-size:11px;"><b>Secret Token</b> (not "oAuth Consumer Secret")</i></label>
                                            <div class="col-sm-10">
                                                <input type="text" placeholder="API Secret Token" name="provider[rightsignature_secret]" id="rightsignature_secret" class="form-control" value="{{ $provider->rightsignature_secret }}">

                                            </div>
                                        </div>
                                    </div>
                                    <br />

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                                <button type="submit" class="btn btn-primary btn-block">Update</button>
                                        </div>
                                    </div>
                                    <script>
                                        function fb_settings(){
                                            if($('#freshbooks_clients_enabled').prop('checked'))$('#freshbooks_settings').slideDown();
                                            else $('#freshbooks_settings').slideUp();
                                        }
                                        $(function(){
                                            $('#freshbooks_clients_enabled').click(function(){
                                                fb_settings();                                                
                                            });
                                        });
                                        fb_settings();
                                    </script>
                                    
                    {{ Form::close() }}
                    </fieldset>
                </div><!--/col-12-->
            </div><!--/row-->
	</div> <!-- /END Company info tab -->
        


    <div class="tab-pane {{$current_tab=='client_links'?'active':''}}" id="client_links">
        <br /><br />
        @if(Sentry::getUser()->role=='admin')

            <div class="row">
                <div class="col-xs-12">
                    <center>
                    <h4>Customize my provider homepage</h4>
                        My Homepage URL: &nbsp;
                        <a href="http://www.forcremation.com/?provider={{$provider->id}}" target="_blank">http://www.forcremation.com/?provider={{$provider->id}}</a>

                    </center>
                </div>
            </div>
            <br />

            <div class="row">
                <div class="col-xs-12">
                    <h5>My Logo</h5>
                    <fieldset>
                        {{ Form::open(['action'=>'AdminController@postUpdateFiles', 'class'=>'form-horizontal','files'=>true]) }}
                        {{ Form::hidden("provider[id]",$provider->id) }}
                        {{ Form::hidden("tab", 'client_links') }}
                        <div class="row">
                            <div class="col-xs-1">
                                @if($provider_logo != null)
                                    <img src="{{ asset('/provider_files/'.$provider->id.'/'.$provider_logo->file_name) }}" style="max-width:100px;width:100%;height:auto;"/> &nbsp;
                                    <br />&nbsp; &nbsp; &nbsp; [ <i>{{link_to_action('AdminController@getRemoveFiles', "Delete", array('fileid'=>$provider_logo ->id,'provider_id'=>$provider->id,'tab'=>'client_links'), $attributes = array('onclick'=>'return confirm("Are you sure you want to delete this file?")',"style"=>"color:red!important;"))}}</i> ]<br />
                                @endif
                            </div>
                            <div class="form-group col-xs-11">
                                <label for="provider_files"><i>* Logo should be a PNG file type with maximum of 300px wide and 100px tall</i></label>
                                <input type="file" id="provider_files_logo" name="provider_logo" class="form-control" />

                                <br />
                                <button type="submit" class="btn btn-primary btn-block" style="width:150px;">Upload</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </fieldset>
                </div>
            </div>
            <br />

            <div class="row">
                <div class="col-xs-12">
                    <h5>My Homepage Slides</h5>
                    <fieldset>
                        {{ Form::open(['action'=>'AdminController@postUpdateFiles', 'class'=>'form-horizontal','files'=>true]) }}
                        {{ Form::hidden("provider[id]",$provider->id) }}
                        {{ Form::hidden("tab", 'client_links') }}
                        <label for="provider_files"><i>* Images should be 1000px wide by 300px tall</i></label>

                        <div class="form-group row">

                            @for($x=1; $x<=3; $x++)
                                <div class="col-xs-1">
                                    @if(array_key_exists('provider_slide_'.$x, $provider_homepage_files))
                                        <img src="{{ asset('/provider_files/'.$provider->id.'/'.$provider_homepage_files['provider_slide_'.$x]->file_name) }}" style="max-width:100px;width:100%;height:auto;"/> &nbsp;
                                        <br />&nbsp; &nbsp; &nbsp; [ <i>{{link_to_action('AdminController@getRemoveFiles', "Delete", array('fileid'=>$provider_homepage_files['provider_slide_'.$x]->id,'provider_id'=>$provider->id,'tab'=>'client_links'), $attributes = array('onclick'=>'return confirm("Are you sure you want to delete this file?")',"style"=>"color:red!important;"))}}</i> ]<br />
                                    @endif
                                </div>
                                <div class="col-xs-3">
                                    Slide {{$x}}:
                                    <input type="file" id="provider_files_{{$x}}" name="provider_slide_{{$x}}" class="form-control" /> <br />
                                </div>
                            @endfor

                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-primary btn-block">Upload</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </fieldset>
                </div>
            </div>
            <br />
        @endif

        @if(Sentry::getUser()->role!='admin')
            <div class="row">
                <div class="col-xs-4">

                </div>
                <div class="col-xs-8">
                    <label for="provider_files" class="col-xs-9 pull-right text-right">My Homepage URL: &nbsp;
                        <a class="pull-right" href="http://www.forcremation.com/?provider={{$provider->id}}" target="_blank">http://www.forcremation.com/?provider={{$provider->id}}</a>
                    </label>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xs-12">
               <h5>Links to My Provider Homepage</h5>
               <fieldset>
                    <div class="form-group">
                       <label for="provider_files" class="col-xs-3">My Cremation Registration URL</label>
                       <div class="col-xs-9">
                       Your Provider Registration URL
                           <textarea class="client_url">{{ url('clients/steps?provider_id='.$provider->id) }}</textarea><br />
                           <i>Example: {{url('clients/steps?provider_id='.$provider->id)}}</i>
                       </div>
                    </div>
                    <br style="float:none;clear:both;" /><br style="float:none;clear:both;" /><br style="float:none;clear:both;" />

                    <div class="form-group">
                        <label for="provider_files" class="col-xs-3">HTML Link To My Cremation Registration</label>
                        <div class="col-xs-9">
                        Copy This Link HTML To Your Site
                            <textarea class="client_url" >{{ link_to('clients/steps?provider_id='.$provider->id) }}</textarea><br />
                            <i>Example: {{link_to('clients/steps?provider_id='.$provider->id)}}</i>
                        </div>
                    </div>
                    <br style="float:none;clear:both;" /><br style="float:none;clear:both;" /><br style="float:none;clear:both;" />

                    <div class="form-group">
                        <label for="provider_files" class="col-xs-3">Cremation Registration Widget</label>
                        <div class="col-xs-9">
                        Add the following HTML to your site to display a widget with link
                            <textarea class="client_url" ><iframe src="http://forcremation.com/providers/badges.php?provider_id={{$provider->id}}" style="width:100%;height:200px;overflow:hidden;margin:0px;padding:0px;" frameborder="0"></iframe></textarea><br />
                            <i>Example: <br />
                            <iframe src="http://forcremation.com/providers/badges.php?provider_id={{$provider->id}}" style="width:300px;height:200px;overflow:hidden;margin:0px;padding:0px;" frameborder="0"></iframe></i>
                        </div>
                    </div>
               </fieldset>

               <style>
                    .client_url{margin-bottom:0px;}
               </style>
               <script>
                   $('.client_url').focus(function() {
                       var $this = $(this);

                       $this.select();

                       window.setTimeout(function() {
                           $this.select();
                       }, 1);

                       // Work around WebKit's little problem
                       function mouseUpHandler() {
                           // Prevent further mouseup intervention
                           $this.off("mouseup", mouseUpHandler);
                           return false;
                       }

                       $this.mouseup(mouseUpHandler);
                   });
               </script>
            </div>
        </div>
    </div>









        <div class="tab-pane {{$current_tab=='provider_files'?'active':''}}" id="provider_files">
        <div class="row">
		<div class="col-xs-12">
                    <fieldset>
                    {{ Form::open(['action'=>'AdminController@postUpdateFiles', 'class'=>'form-horizontal','files'=>true]) }}
                    {{ Form::hidden("provider[id]",$provider->id) }}
                    {{ Form::hidden("tab", 'provider_files') }}

                        <?php
                        $fileNames = Array('pricing'=>"General Price List",
                                            //'vitals'=>"Vitals/Summary",
                                            'hospital_release'=>"Hospital Release", 
                                            'cremation_authorization'=>'Cremation Authorization',
                                            'disposition_embalming'=>'Disposition-Embalming',
                                            'preneed_release'=>'Pre-need Release',
                                            'corner_medical_examiner'=>'Corner-Medical Examiner',
                                            'personnel_effects'=>'Personnel Effects',
                                            'transfer_of_authority'=>'Transfer of Authority',
                                            'viewing_release'=>'Viewing Release',
                                            'other'=>'Other 1',
                                            'other2'=>'Other 2',
                                            'other3'=>'Other 3',
                                            'other4'=>'Other 4',
                                            'other5'=>'Other 5');


                    ?>

                            <div class="form-group">
                                <label for="provider_files" class="col-xs-4">Upload Provider Form To ForCremation Staff</label>
                                <div class="col-xs-8">
                                       
                                    Form Type:
                                    <select name="provider_files_type" id="provider_files_type">
                                        <option value="">Select A Type of Form</option>
                                        <?php 
                                        foreach($fileNames as $key=>$value){
                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                        }
                                        ?>
                                    </select>
                                    <input type="file" id="provider_files" name="provider_files_new" class="form-control">
                                        
                                </div>
                                <br style="float:none;clear:both;"/><br />&nbsp; &nbsp; <b>Current Forms</b>:
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
			{{ Form::close() }}
                        </fieldset>
		</div><!--/col-12-->
            </div><!--/row-->
	</div> <!-- /END Company info tab -->
	
      
    @if(Sentry::getUser()->role=='admin')
       <div class="tab-pane {{$current_tab=='customer_document_forms'?'active':''}}" id="customer_document_forms">
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                    {{ Form::open(['action'=>'AdminController@postUpdateProviderForms', 'class'=>'form-horizontal','files'=>true]) }}
                    {{ Form::hidden("provider[id]",$provider->id) }}

                    <div class="form-group">
                        <div class="col-xs-2">
                            Editing Form:
                            <select id="edit_custom_form" name="edit_custom_form" style="font-size:16px;" >
                                <?php
                                    $forms = ProviderController::getDocumentTypes();
                                   
                                    foreach($forms as $key=>$form_name){
                                        echo '<option value="'.$key.'" '.($custom_form_num==$key?'selected':'').' >'.$form_name.'</option>';
                                    }
                                    if($custom_form_num=='')$this_form = 'customer_form_1';
                                    else $this_form = 'customer_form_'.$custom_form_num;

                                    $custom_form = $provider->$this_form;
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-2">
                            <button value="Change Form" name="change_form" class="btn btn-primary btn-block">Change Form</button>                           
                        </div>
                        <div class="col-xs-8"></div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <textarea id="custom_provider_form" name="provider[{{$this_form}}]" class="form-control customer_form_ta" >{{$custom_form}}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </div>
                        <div class="col-xs-4">
                            <a class="btn btn-success btn-block" id="download_forms" target="_blank" href="{{ action('ClientController@getCustomerDocuments',array(1,$provider->id,$this_form) ) }}">Preview Example</a>
                        </div>
                    </div>

                <a href="#" onclick="$('#key_map').slideToggle();return false;">Show Client and Provider Keywords</a>
                <div style="display:none;" id="key_map">
                   <?php
                   echo '<table><tr><td align=left style="vertical-align: top;">
                    <ul class="form_keys"><li><b>Person Registering for Cremation Services</b></li>
                    <li>{{client_status}}</li>
                    <li>{{client_first_name}}</li>
                    <li>{{client_middle_name}}</li>
                    <li>{{client_last_name}}</li>
                    <li>{{client_legal_name}}</li>
                    <li>{{client_initials}}</li>
                    <li>{{client_relationship}}</li>
                    <li>{{client_apt}}</li>
                    <li>{{client_address}}</li>
                    <li>{{client_city}}</li>
                    <li>{{client_state}}</li>
                    <li>{{client_zip}}</li>
                    <li>{{client_phone}}</li>
                    <li>{{client_agreed_to_ftc}}</li>
                    <li>{{client_confirmed_legal_auth}}</li>
                    <li>{{client_confirmed_legal_auth_name}}</li>
                    <li>{{client_confirmed_correct_info}}</li>
                    <li>{{client_confirmed_correct_info_initial}}</li>
                    <li>{{client_feedback_questions_text}}</li>
                    <li>{{client_feedback_suggestions}}</li>
                    <li>{{client_is_junk}}</li>
                    <li>{{client_purchase_option}}</li>
                    <li>{{client_majority_count}}<Br /><br /></li>
                    
                    <li><b>Urn Selection</b></li>
                    <li>{{ClientProducts_name}}</li>
                    <li>{{ClientProducts_price}}</li>
                    <li>{{ClientProducts_description}}</li>
                    <li>{{ClientProducts_note}}</li>
                    </ul>
                    
                    </td><td align=left style="vertical-align: top;">
                    <ul class="form_keys"><li><b>Provider</b></li>
                    <li>{{provider_business_name}}</li>
                    <li>{{provider_address}}</li>
                    <li>{{provider_city}}</li>
                    <li>{{provider_state}}</li>
                    <li>{{provider_zip}}</li>
                    <li>{{provider_email}}</li>
                    <li>{{provider_website}}</li>
                    <li>{{provider_phone}}</li>
                    <li>{{provider_fax}}</li>
                    <li>{{provider_provider_radius}}</li>
                    <li>{{provider_provider_status}}</li>
                    <li>{{provider_logo_url}}<Br /><br /></li>

                    <li><b>Deceased\'s Fmaily Information</b></li>
                    <li>{{DeceasedFamilyInfo_srdp_first_name}}</li>
                    <li>{{DeceasedFamilyInfo_srdp_middle_name}}</li>
                    <li>{{DeceasedFamilyInfo_srdp_last_name}}</li>
                    <li>{{DeceasedFamilyInfo_fthr_first_name}}</li>
                    <li>{{DeceasedFamilyInfo_fthr_middle_name}}</li>
                    <li>{{DeceasedFamilyInfo_fthr_last_name}}</li>
                    <li>{{DeceasedFamilyInfo_fthr_birth_city}}</li>
                    <li>{{DeceasedFamilyInfo_mthr_first_name}}</li>
                    <li>{{DeceasedFamilyInfo_mthr_middle_name}}</li>
                    <li>{{DeceasedFamilyInfo_mthr_last_name}}</li>            
                    <li>{{DeceasedFamilyInfo_mthr_birth_city}}<Br /><br /></li>

                    <li><b>Payment Summary Sheet</b></li>
                    <li>{{sales_summary_sheet}}</li>

                    </ul>
                    </td><td align=left style="vertical-align: top;">

                    <ul class="form_keys">
                    <li><b>Deceased Information</b></li>
                    <li>{{DeceasedInfo_first_name}}</li>
                    <li>{{DeceasedInfo_middle_name}}</li>
                    <li>{{DeceasedInfo_last_name}}</li>
                    <li>{{DeceasedInfo_aka}}</li>
                    <li>{{DeceasedInfo_location}}</li>
                    <li>{{DeceasedInfo_type_of_location}}</li>
                    <li>{{DeceasedInfo_address}}</li>
                    <li>{{DeceasedInfo_apt}}</li>
                    <li>{{DeceasedInfo_city}}</li>
                    <li>{{DeceasedInfo_state}}</li>
                    <li>{{DeceasedInfo_zip}}</li>
                    <li>{{DeceasedInfo_county}}</li>
                    <li>{{DeceasedInfo_phone}}</li>
                    <li>{{DeceasedInfo_dob}}</li>
                    <li>{{DeceasedInfo_dod}}</li>
                    <li>{{DeceasedInfo_gender}}</li>
                    <li>{{DeceasedInfo_race}}</li>
                    <li>{{DeceasedInfo_weight}}</li>
                    <li>{{DeceasedInfo_marriage_status}}</li>
                    <li>{{DeceasedInfo_birth_city_state}}</li>
                    <li>{{DeceasedInfo_yrs_in_county}}</li>
                    <li>{{DeceasedInfo_schooling_level}}</li>
                    <li>{{DeceasedInfo_military}}</li>
                    <li>{{DeceasedInfo_occupation}}</li>
                    <li>{{DeceasedInfo_business_type}}</li>
                    <li>{{DeceasedInfo_years_in_occupation}}</li>
                    <li>{{DeceasedInfo_has_pace_maker}}</li>
                    <li>{{DeceasedInfo_cremation_reason}}</li>
                    <li>{{DeceasedInfo_medical_donation}}</li>
                    <li>{{DeceasedInfo_ssn}}</li></ul>
                    </td><td align=left style="vertical-align: top;">

                    <ul class="form_keys">
                    <li><b>Cremation Information</b></li>
                    <li>{{CremainsInfo_number_of_certs}}</li>
                    <li>{{CremainsInfo_cert_plan}}</li>
                    <li>{{CremainsInfo_cremain_plan}}</li>
                    <li>{{CremainsInfo_cremation_shipping_plan}}</li>
                    <li>{{CremainsInfo_keeper_of_cremains}}</li>
                    <li>{{CremainsInfo_shipto_first_name}}</li>
                    <li>{{CremainsInfo_shipto_middle_name}}</li>
                    <li>{{CremainsInfo_shipto_last_name}}</li>
                    <li>{{CremainsInfo_shipto_address}}</li>
                    <li>{{CremainsInfo_shipto_apt}}</li>
                    <li>{{CremainsInfo_shipto_city}}</li>
                    <li>{{CremainsInfo_shipto_state}}</li>
                    <li>{{CremainsInfo_shipto_zip}}</li>
                    <li>{{CremainsInfo_shipto_phone}}</li>
                    <li>{{CremainsInfo_shipto_email}}</li>
                    <li>{{CremainsInfo_package_plan}}</li>
                    <li>{{CremainsInfo_custom1}}</li>
                    <li>{{CremainsInfo_custom2}}</li>
                    <li>{{CremainsInfo_custom3}}<Br /><br /></li>

                    <li><b>Deceased Current Location</b></li>
                    <li>{{DeceasedInfoPresentLoc_location}}</li>
                    <li>{{DeceasedInfoPresentLoc_type_of_location}}</li>
                    <li>{{DeceasedInfoPresentLoc_address}}</li>
                    <li>{{DeceasedInfoPresentLoc_apt}}</li>
                    <li>{{DeceasedInfoPresentLoc_city}}</li>
                    <li>{{DeceasedInfoPresentLoc_state}}</li>
                    <li>{{DeceasedInfoPresentLoc_zip}}</li> </ul>   
                    </td></tr></table>';
                   ?>
                </div>
                
                <style>
                        .form_keys li{cursor:pointer;}
                        .form_sheet{border:1px solid #999;width:100%;}
                        .form_sheet td{border:1px solid #999;} 
                </style>
                <script src="//cdn.ckeditor.com/4.4.4/full/ckeditor.js"></script>
                 
                <script>
                  


                    $().ready( function() {
                       var editor = CKEDITOR.replace( 'custom_provider_form', { height: '500px' });
                       editor.config.enterMode = CKEDITOR.ENTER_BR;

                       $('.form_keys li').click(function() {
                         //alert('awesome'+$(this).text()); 
                         //$(".cke_source:first").insertAtCaret($(this).text());
                         //$('.cke_wysiwyg_frame:first').src($('.cke_wysiwyg_frame:first'));
                         var editor = CKEDITOR.instances.custom_provider_form;
                         editor.insertText( $(this).text() );

                         return false
                       });
                       /*
                       $("#DragWordList li").draggable({helper: 'clone'});
                       $(".txtDropTarget").droppable({
                         accept: "#DragWordList li",
                         drop: function(ev, ui) {
                           $(this).insertAtCaret(ui.draggable.text());
                         }
                       });*/

                     });
                  
                </script>
               
                {{ Form::close() }}
                </fieldset>
                </div><!--/col-12-->
            </div><!--/row-->
	</div> <!-- /END Forms info tab -->
        
        <?php /*
        <div class="row" >
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
         */ ?>
       
    
    <div class="tab-pane {{$current_tab=='provider_zips'?'active':''}}" id="provider_zips">
        <div class="row">
            <div class="col-xs-12">
                <fieldset>
                <!-- Start managing zip codes -->
                {{ Form::open(['action'=>'AdminController@postUpdateZip']) }}
                {{ Form::hidden("provider[id]",$provider->id) }}
        


                <div class="row">
                    <label for="provider_radius" class="col-sm-12">Select Provider Serviceable Area from the Above Address</label>
                </div>
                <div class="row">
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




		        <div class="row">

			        <div class="col-xs-12 col-md-4">
                            <div class="table-responsive">
                                <label for="provider_zip_code" >Currently Assigned Zip Codes</label><br style="float:none;clear:both;"/>
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

                                                @if($zips!=null)
                                                   @foreach($zips as $zip)
                                                       <?php
                                                        $zips_r[$zip->zip]=$zip->zip;
                                                        echo '<tr><td width="90%"><label for="zip-'.$zip->zip.'" style="cursor:pointer;">'.$zip->zip.'</label></td>'
                                                                . '<td width="10%"><input type="checkbox" class="removezips" id="zip-'.$zip->zip.'" name="removezips['.$zip->zip.']" value="'.$zip->zip.'" /></td>'
                                                                . '</tr>';
                                                       ?>
                                                    @endforeach
                                                @endif

                                        </tbody>
                                    </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block" class="form-control">Remove</button>
				            </div>
			        </div>
                    <div class="col-xs-12 col-md-8">

                        <div class="form-group">
                            <label for="provider_zip_code" >Add Zip Codes</label>
                            <div class="col-xs-8">
                                    <i>You can enter multiple zips separated by commas</i><br />
                                    <input type="text" id="provider_zip_code" placeholder="Enter Zip Code Here" name="provider_zip_code" class="form-control">
                            </div>
                        </div>
                        <br style="float:none;clear:both;"/>
                        <br style="float:none;clear:both;"/>


                        <div class="form-group">
                            <label for="provider_zip_code">Lookup radius of zips</label>
                            <div class="col-xs-8">
                                <i>Currently listing zips in the radius of this zip:</i><br />
                                <input type="text" id="zip_search" placeholder="Enter Zip Code Here" name="zip_search" class="form-control" value="{{$provider->zip}}" style="float:left;" />
                            </div>
                            <div class="col-xs-4">
                                <br />
                                <button type="button" class="btn btn-primary " id="search_zips_btn" class="form-control">Search</button>
                            </div>
                        </div>

                        <br style="float:none;clear:both;"/>

                        <div class="row">
                            <div class="col-xs-8" >
                                &nbsp; &nbsp; <a href="#" onclick="addcheckall();return false;"><b>Select All</b></a><br />
                                    <div id="zip_list" style="height:300px;overflow-x:auto;border:1px solid #aaa;width:100%;margin:5px 5px;padding:5px 17px;">
                                    <?php
                                        if($zip_info!=null){
                                            foreach($zip_info as $zip){
                                                //if(!in_array($zip->zip,$zips_r))
                                                echo '<br style="float:none;clear:both;"/><label for="addzip-'.$zip->zip.'" style="cursor:pointer;float:left;">'.$zip->zip.' </label>'
                                                        . '&nbsp; <input type="checkbox" class="addzips" id="addzip-'.$zip->zip.'" name="addzips['.$zip->zip.']" value="'.$zip->zip.'" /> '
                                                        . '&nbsp; ~ '.round($zip->distance,2).' miles';
                                            }

                                        }
                                    ?>
                                    </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-xs-8"><button type="submit" class="btn btn-primary btn-block" class="form-control">Update Zip Info</button></div>
                            <div class="col-xs-4">

                            </div>
                        </div>
                </div>
		</div>

        <script>

                $('#search_zips_btn').on('click',function(){

                        $.getJSON( "{{action("AdminController@getZipsByLocation")}}", {
                            zip: $('#zip_search').val(),
                            radius:$('#provider_radius').val()
                        })
                        .done(function( data ) {
                            if(data){
                                $( "#zip_list" ).empty();

                                $.each( data, function( i, item ) {
                                    //alert(item);
                                    $( "#zip_list" ).append('<br style="float:none;clear:both;"/><label for="addzip-'+ item.zip + '" style="cursor:pointer;float:left;">'+ item.zip + ' </label>');
                                    $( "#zip_list" ).append('&nbsp; <input type="checkbox" class="addzips" id="addzip-'+ item.zip + '" name="addzips['+ item.zip + ']" value="'+ item.zip + '" />');
                                    $( "#zip_list" ).append('&nbsp; ~ '+item.distance+' miles');
                                });
                            }

                        });


                });
                $('#zip_search').keydown( function(e) {
                    var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
                    if(key == 13) {
                        e.preventDefault();
                        $('#search_zips_btn').click();
                    }
                });
        </script>


            {{ Form::close() }}
            </fieldset>
        </div><!--/col-12-->
        </div><!--/row-->
    </div> <!-- /END zip info tab -->




    <div class="tab-pane {{$current_tab=='provider_clients'?'active':''}}" id="provider_clients">
	<div class="row">
            <div class="col-xs-12">
            {{ $clients->links() }}
            <fieldset>
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
                    @if(count($clients)<1)
                        <tr><td colspan="7"><br /><center><b><i style="color:green;">Your clients will appear here when they register</i></b></center><br /></td></tr>
                    @endif
            </tbody>
            </table>
            </fieldset>
            {{ $clients->links() }}
            </div><!--/col-12-->
        </div><!--/row-->
    </div> <!-- /END Client info tab -->



    <div class="tab-pane {{$current_tab=='provider_signatures'?'active':''}}" id="provider_signatures">
        <div class="row">
            <div class="col-xs-12">

                <fieldset>
                    <table class="">
                        <thead>
                        <tr>
                            <th style="width:90px;">Date</th>
                            <th>Forms Sent</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th class="text-right">Original Document</th>
                            <th class="text-right">Signed Document</th>
                            <th class="text-right">History</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach( $signature_docs['documents']['document'] as $signed_docs )
                            <tr>
                                <td >{{ date('m/d/Y h:i a',strtotime($signed_docs['created-at'].' - 7 hours')) }}</td>
                                <td >{{ $signed_docs['doc_types'] }}</td>
                                <td>
                                    @if(is_object($signed_docs['client']))
                                        <a href="{{ action('AdminController@getEditClient', $signed_docs['client']->id) }}">{{$signed_docs['client']->first_name.' '.$signed_docs['client']->last_name}}</a>
                                    @endif
                                </td>

                                <td >{{ ucwords($signed_docs['state']) }}</td>

                                <td class="text-right">
                                    <a href="{{ urldecode($signed_docs['pdf-url']) }}" target="_blank" class="btn btn-xs btn-default">
                                        <span class="glyphicon glyphicon-list-alt"></span> &nbsp; View
                                    </a>
                                </td>
                                <td class="text-right">
                                    @if($signed_docs['signed-pdf-url'] != '')
                                    <a href="{{ urldecode($signed_docs['signed-pdf-url']) }}" target="_blank" class="btn btn-xs btn-default">
                                        <span class="glyphicon glyphicon-pencil"></span> &nbsp; View
                                    </a>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="#" onclick="getSignedDoc('{{$signed_docs['guid'] }}')" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal">
                                        <span class="glyphicon glyphicon-search"></span> &nbsp; History
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @if(count($signature_docs['documents']['document'])<1)
                            <tr><td colspan="7"><br /><center><b><i style="color:green;">Your clients' documents will appear here when they are sent</i></b></center><br /></td></tr>
                        @endif
                        </tbody>
                    </table>
                </fieldset>

                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog"  style="width:800px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Client Document</h4>
                            </div>
                            <div class="modal-body" id="doc_info">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                            </div>
                        </div>
                    </div>
                </div>

            </div><!--/col-12-->
        </div><!--/row-->
    </div> <!-- /END Client info tab -->

    <script>
        function getSignedDoc(guid) {
            $.get('{{ action('AdminController@getSignedDoc') }}/'+guid, function (data) {
                $('#doc_info').html(data);
            });
        }
    </script>
   @endif





   <div class="tab-pane {{$current_tab=='provider_pricing'?'active':''}}" id="provider_pricing">
	<div class="row">
		<div class="col-xs-12">
                    <fieldset>
                    {{ Form::open(['action'=>'AdminController@postUpdatePricing', 'class'=>'form-horizontal']) }}
                    {{ Form::hidden("provider[id]",$provider->id) }}

                            <div class="form-group">
                                    <label for="basic_cremation" class="col-xs-12 col-md-6">Plan A Package</label>
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
                                    <label for="premium_cremation" class="col-xs-12 col-md-6">Plan B Package</label>
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
                                <label for="weight_lt_250" class="col-xs-12 col-md-6">Filing Fee</label>
                                <div class="col-xs-12 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" id="filing_fee" name="pricing[filing_fee]" value="{{ $pricing->filing_fee }}" class="form-control">
                                    </div>
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
		
		{{ Form::close() }}
                </fieldset>
		</div><!--/col-12-->
            </div><!--/row-->
	</div> <!-- /END Pricing info tab -->
        
        
    <div class="tab-pane {{$current_tab=='provider_urns'?'active':''}}" id="provider_urns">
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                    {{ Form::open(['action'=>'AdminController@postUpdateProviderUrns','class'=>'form-horizontal','role'=>'form','#'=>'provider_urns']) }}
                    {{ Form::hidden("provider[id]",$provider->id) }}
    
                       
                    <table class="">
                    <thead>
                        <tr>
                            <th style="width:150px;">Image</th>
                            <th style="width:250px;">Name</th>
                            <th style="width:100px;">Price</th>
                            <th style="width:100%;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $provider_products as $product )
                           
                            @if($product->product_id=='')
                                <input type="hidden" name="product[{{$product->id}}][product_id]" value="{{$product->id}}" />
                            @endif
                            <tr>
                                <td valign="top"><img src="{{ $product->image }}" style="width:150px;height:auto;" /><input type="hidden" name="product[{{ $product->id }}][image]" value="{{ $product->image }}" /></td>
                                <td valign="top" style="padding:0 5px;"><input type="text" name="product[{{ $product->id }}][name]" value="{{ $product->name }}"/></td>
                                <td valign="top" style="padding:0 5px;"><table><tr><td align="right" style="padding-top:0px;padding-right:5px;">$</td><td width="85%" align="left"><input type="text" name="product[{{ $product->id }}][price]" value="{{ $product->price }}"/></td></tr></table></div></td>
                                <td valign="top"><textarea name="product[{{ $product->id }}][description]" id="urn_description{{ $product->id }}" style="width:100%;height:130px;">{{ $product->description }}</textarea></td>
                            </tr>
                            <script>    
                                $().ready(function(){
                                    var editor = CKEDITOR.replace( 'urn_description{{ $product->id }}', {height: '120px'});
                                    editor.config.enterMode = CKEDITOR.ENTER_BR;
                                    editor.config.toolbarGroups = [
                                        { name: 'links' },
                                        { name: 'basicstyles', groups: [ 'basicstyles'] },
                                        { name: 'styles' },
                                        { name: 'colors' }

                                    ];
                                });
                            </script>
                        @endforeach                       
                    </tbody>
                </table>
                    
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </div>
                    </div>
		{{ Form::close() }}
               </fieldset>
            </div><!--/col-12-->
        </div><!--/row-->
    </div> <!-- /END Forms info tab -->

<div class="tab-pane {{$current_tab=='provider_help'?'active':''}}" id="provider_help">
    <div class="row">
        <div class="col-xs-12">
            <fieldset>

                <h3>Provder Help</h3>
                <div class="form-group">
                    <div class="col-xs-12">
                        <a href="http://www.forcremation.com/images/Provider-Set-up.mp4" class="video_layer">Provider Setup</a><br />
                        <a href="http://www.forcremation.com/images/logging-in-forcremation.mp4" class="video_layer">Provider Login</a><br />
                        <a href="http://www.forcremation.com/images/Provider-Pricing.mp4" class="video_layer">Provider Pricing</a><br />
                        <a href="http://www.forcremation.com/images/Forms-ForCremation.mp4" class="video_layer">How To Upload Forms</a><br />
                        <a href="http://www.forcremation.com/images/urns-and-urn-pricing.mp4" class="video_layer">Urns and Urn Pricing</a>


                        <br /><br />
                    </div>
                </div>

                <h3>RightSignature Help</h3>
                <div class="form-group">
                    <div class="col-xs-12">
                        <a href="http://player.vimeo.com/video/58565515" target="_blank" class="vimeo">RightSignature Quick Start</a><br />
                        <a href="http://player.vimeo.com/video/44946281" target="_blank" class="vimeo">Send Document for Signature</a><br />
                        <a href="http://player.vimeo.com/video/44946438" target="_blank" class="vimeo">Create A Template</a><br />
                        <a href="http://player.vimeo.com/video/68087812" target="_blank" class="vimeo">How To Use Template Overlays</a><br />
                        <a href="http://player.vimeo.com/video/77429248" target="_blank" class="vimeo">How To Group Boxes</a><br />
                        <a href="http://player.vimeo.com/video/41097282" target="_blank" class="vimeo">How To Use Google Drive with Righsignature</a><br />
                        <a href="http://player.vimeo.com/video/89738082" target="_blank" class="vimeo">Requesting Attachments</a><br />
                        <a href="http://player.vimeo.com/video/33310103" target="_blank" class="vimeo">RightSignature and Freshbooks</a><br /><br />
                    </div>
                </div>

                <h3>Freshbooks Help</h3>
                <div class="form-group">
                    <div class="col-xs-12">
                        <a href="http://www.youtube.com/embed/ySHC-ffp0bQ?rel=0&amp;wmode=transparent" target="_blank" class="youtube">Say Hello To Fresbooks</a><br />
                        <a href="http://www.youtube.com/embed/gur9VYXDkz8?rel=0&amp;wmode=transparent" target="_blank" class="youtube">Your First Invoice</a><br />
                        <a href="http://player.vimeo.com/video/83720440" target="_blank" class="vimeo">Creating a Client ( use organization to create client )</a><br />
                        <a href="http://player.vimeo.com/video/88883845" target="_blank" class="vimeo">Full Tutorial Instructions</a><br />
                        <a href="http://player.vimeo.com/video/83720439" target="_blank" class="vimeo">Tracking Exspnses</a><br />
                    </div>
                </div>

            </fieldset>
        </div><!--/col-12-->
    </div><!--/row-->
</div> <!-- /END Help1 info tab -->



<script src="{{ asset('js/jquery.colorbox-min.js') }}"></script>
<script>
    $().ready(function(){
        $('.video_layer').colorbox({iframe:true, innerWidth:640, innerHeight:344});
        $(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
        $(".vimeo").colorbox({iframe:true, innerWidth:500, innerHeight:409});
    });

</script>

    
</div><!--/end tab-content -->
</div><!--/row-->

@stop