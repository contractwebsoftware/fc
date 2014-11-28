@extends('layouts.general')
@section('content')

<style>
    .glyphicon-ok{color:green;}
    .plans{cursor:pointer; float:left;}
</style>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">
        <h2>Cremation Provider Signup</h2>
        <hr>
        <center>
        <img src="http://www.forcremation.com/wp-content/themes/forcremation/images/travis.png" style="height:63px;width:auto;">
        <a href="http://youtu.be/zFpbwTJhN10?rel=0;HD=1;showinfo=0;controls=0;autoplay=1" target="_blank" class="fancybox-youtube" style="font-weight:bold;font-size:16px;">Watch the video about becoming a Provider</a> <br/>

        <img src="http://www.forcremation.com/wp-content/themes/forcremation/images/susan.png" style="height:63px;width:auto;">
        <a title="Meet-Susan" href="http://www.youtube.com/watch?v=k4EueEuaDAQ&amp;feature=youtu.be&amp;rel=0;HD=1;showinfo=0;controls=0;autoplay=1" style="margin-right: 30px;font-weight:bold;font-size:16px;" class="fancybox-youtube">Meet Susan</a>
        </center>

    </div>
   
	
<!-- Easy FancyBox 1.5.6 using FancyBox 1.3.6 - RavanH (http://status301.net/wordpress-plugins/easy-fancybox/) -->
    
    <link rel="stylesheet" href="http://www.forcremation.com/wp-content/plugins/easy-fancybox/fancybox/jquery.fancybox-1.3.6.pack.css?ver=1.5.6" type="text/css" media="screen" />
    <script type='text/javascript' src='http://www.forcremation.com/wp-content/plugins/easy-fancybox/fancybox/jquery.fancybox-1.3.6.pack.js?ver=1.5.6'></script>
    <script type='text/javascript' src='http://www.forcremation.com/wp-content/plugins/easy-fancybox/jquery.easing.pack.js?ver=1.3'></script>
    <script type='text/javascript' src='http://www.forcremation.com/wp-content/plugins/easy-fancybox/jquery.mousewheel.pack.js?ver=3.1.3'></script>
    <script type="text/javascript">
       
        /* <![CDATA[ */
            var fb_timeout = null;
            var fb_opts = { 'overlayShow' : true, 'hideOnOverlayClick' : true, 'showCloseButton' : true, 'centerOnScroll' : true, 'enableEscapeButton' : true, 'autoScale' : true };
            var easy_fancybox_handler = function(){
                    /* YouTube */
                    $('a[href*="youtube.com/watch"]:not(.nofancybox), area[href*="youtube.com/watch"]:not(.nofancybox)').addClass('fancybox-youtube');
                    $('a[href*="youtu.be/"]:not(.nofancybox), area[href*="youtu.be/"]:not(.nofancybox)').addClass('fancybox-youtube');
                    $('a.fancybox-youtube, area.fancybox-youtube, li.fancybox-youtube a:not(li.nofancybox a)').fancybox( jQuery.extend({}, fb_opts, { 'type' : 'iframe', 'width' : 640, 'height' : 360, 'padding' : 0, 'titleShow' : false, 'titlePosition' : 'float', 'titleFromAlt' : true, 'onStart' : function(selectedArray, selectedIndex, selectedOpts) { selectedOpts.href = selectedArray[selectedIndex].href.replace(new RegExp('youtu.be', 'i'), 'www.youtube.com/embed').replace(new RegExp('watch\\?(.*)v=([a-z0-9\_\-]+)(&|\\?)?(.*)', 'i'), 'embed/$2?$1$4') } }) );
                    /* Auto-click */ 
                    $('#fancybox-auto').trigger('click');
            }
     
        /* ]]> */
    </script>
    <script type="text/javascript">
    jQuery(document).on('ready post-load', easy_fancybox_handler );
    </script>
    <style type="text/css">
	.fancybox-hidden{display:none}.rtl #fancybox-left{left:auto;right:0px}.rtl #fancybox-right{left:0px;right:auto}.rtl #fancybox-right-ico{background-position:-40px -30px}.rtl #fancybox-left-ico{background-position:-40px -60px}.rtl .fancybox-title-over{text-align:right}.rtl #fancybox-left-ico,.rtl #fancybox-right-ico{right:-9999px}.rtl #fancybox-right:hover span{right:auto;left:20px}.rtl #fancybox-left:hover span{right:20px}#fancybox-img{max-width:none;max-height:none}
	#fancybox-outer{background-color:#fff}
	#fancybox-content{border-color:#fff}
	#fancybox-content{color:inherit}
    </style>
    
    <div class="col-xs-12 col-md-8 col-md-offset-2">
        {{ Form::open(['action'=>'UserController@postRegisterProvider','class'=>'form-horizontal','role'=>'form','onsubmit'=>"return validateForm();"]) }}
            <fieldset>
                    <legend>Company Information</legend>
                    <div class="form-group">
                        <label  class="sr-only" for="business_name">Business Name</label>
                        <div class="col-sm-12"><input type="text" placeholder="Business Name" name="business_name" id="business_name" class="form-control" value="{{Input::get('business_name')}}" /></div>
                    </div>
                    <div class="form-group">
                        <label  class="sr-only" for="address">Company Address</label>
                        <div class="col-sm-12"><textarea placeholder="Address" name="address" id="address" class="form-control" row="3" value="{{Input::get('address')}}" /></textarea></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6">
                          <input type="text" class="form-control" id="city" placeholder="City" name="city" value="{{Input::get('city')}}" />
                        </div>
                        <div class="col-sm-3">
                              <input type="text" class="form-control" id="state" placeholder="State" name="state" value="{{Input::get('state')}}" />
                        </div>
                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="zip" placeholder="Zip" name="zip" value="{{Input::get('zip')}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="sr-only" for="website">Website</label>
                        <div class="col-sm-12"><input type="text" placeholder="Website" name="website" id="website" class="form-control" value="{{Input::get('website')}}" /></div>
                    </div>
                    <div class="form-group">
                        <label  class="sr-only" for="phone">Phone</label>
                        <div class="col-sm-12"><input type="text" placeholder="Phone" name="phone" id="phone" class="form-control" value="{{Input::get('phone')}}" /></div>
                    </div>
                    <div class="form-group">
                        <label  class="sr-only" for="fax">Fax</label>
                        <div class="col-sm-12"><input type="text" placeholder="Fax" name="fax" id="fax" class="form-control" value="{{Input::get('fax')}}" /></div>
                    </div>   
                    <!--
                    <div class="form-group">
                        <label for="provider_radius" class="col-sm-12">Select your serviceable area from the above address</label>
                        <div class="col-sm-12">
                            <select name="provider[provider_radius]" id="provider_radius" class="form-control">
                                    <option value="5"  {{ (Input::get('provider_radius')=='5') ? ' selected' : '' }}>5 Miles</option>
                                    <option value="10" {{ (Input::get('provider_radius')=='10') ? ' selected' : '' }}>10 Miles</option>
                                    <option value="15" {{ (Input::get('provider_radius')=='15') ? ' selected' : '' }}>15 Miles</option>
                                    <option value="20" {{ (Input::get('provider_radius')=='20') ? ' selected' : '' }}>20 Miles</option>
                                    <option value="30" {{ (Input::get('provider_radius')=='30') ? ' selected' : '' }}>30 Miles</option>
                                    <option value="40" {{ (Input::get('provider_radius')=='40') ? ' selected' : '' }}>40 Miles</option>
                                    <option value="50" {{ (Input::get('provider_radius')=='50') ? ' selected' : '' }}>50 Miles</option>
                            </select>
                        </div>
                    </div>-->
            </fieldset>
            <Br />
            <fieldset>
                <legend style="margin-bottom: 0px;">Plan Options</legend>
                <table data-toggle="buttons"  style="margin-top: 0px;">
                    <thead >
                        <th></th>
                        <th><label class="btn btn-primary {{(Input::get('plan_id')==$provider_plan_basic->id || Input::get('plan_id')==''?'active':'')}}" for="plan_basic"><input type="radio" name="plan_id" id="plan_basic" value="{{$provider_plan_basic->id}}" {{(Input::get('plan_id')==$provider_plan_basic->id || Input::get('plan_id')==''?'checked':'')}}> &nbsp; <b>Basic</b> &nbsp; ${{$provider_plan_basic->price}} <sub>/m</sub></label></th>
                        <th><label class="btn btn-primary {{(Input::get('plan_id')==$provider_plan_premium->id?'active':'')}}" for="plan_premium"><input type="radio" name="plan_id" id="plan_premium" value="{{$provider_plan_premium->id}}" {{(Input::get('plan_id')==$provider_plan_premium->id?'checked':'')}}> &nbsp; <b>Premium</b> &nbsp; ${{$provider_plan_premium->price}} <sub>/m</sub></label></th>
                    </thead>
                    <tr><td>Cases Per Month</td><td>Ten</td><td>Unlimited</td></tr>
                    <tr><td>Certified Signatures</td><td>Ten</td><td>Unlimited</td></tr>
                    <tr><td>Online Training</td><td><i class="glyphicon glyphicon-ok"></i></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Platform Custom Pricing</td><td><i class="glyphicon glyphicon-ok"></i></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Digital Brochure</td><td><i class="glyphicon glyphicon-ok"></i></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Signature Certificate</td><td><i class="glyphicon glyphicon-ok"></i></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Custom Documents</td><td>$65 set up</td><td><i class="glyphicon glyphicon-ok"></i> &nbsp; Included</td></tr>
                    <tr><td>Product Support</td><td><i class="glyphicon glyphicon-ok"></i></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Providers Website  Link</td><td></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Google Places Link</td><td><i class="glyphicon glyphicon-ok"></i></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Freshbooks Integration</td><td></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                    <tr><td>Custom Platform Home Page</td><td></td><td><i class="glyphicon glyphicon-ok"></i></td></tr>
                </table> 
            </fieldset>
            <Br />   
            <fieldset>
                    <legend>User Information</legend>
                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="email">Login Email Address</label>
                            <div class="col-sm-12"><input type="email" placeholder="Login Email Address" name="email" id="email" class="form-control" value="{{Input::get('email')}}" /></div>
                    </div>
                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="password">Password</label>
                            <div class="col-sm-12"><input type="password" placeholder="Password" name="password" id="password" class="form-control" value="" /></div>
                    </div>
                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="confirm-password">Confirm Password</label>
                            <div class="col-sm-12"><input type="password" placeholder="Confirm Password" name="password_confirmation" id="confirm-password" class="form-control"  value="" /></div>
                    </div>
                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="contact_first_name">Contact First Name</label>
                            <div class="col-sm-12"><input type="text" placeholder="Contact First Name" name="contact_first_name" id="contact_first_name" class="form-control" value="{{Input::get('contact_first_name')}}" /></div>
                    </div>
                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="contact_last_name">Contact Last Name</label>
                            <div class="col-sm-12"><input type="text" placeholder="Contact Last Name" name="contact_last_name" id="contact_last_name" class="form-control" value="{{Input::get('contact_last_name')}}" /></div>
                    </div>
                    <!--
                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="contact_email">Contact Email Address</label>
                            <div class="col-sm-12"><input type="text" placeholder="Contact Email Address" name="contact_email" id="contact_email" class="form-control" value="{{Input::get('contact_email')}}" /></div>
                    </div>-->

                    <div class="form-group">
                            <label  class="sr-only col-sm-12" for="agreement">Agreement</label>
                            <div class="col-sm-12"><input type="checkbox" name="agreement" id="agreement" class="form-control"  value="1" />
                                <label for="agreement">I have read and accept the 
                                <a href="#" onclick="window.open('{{action('UserController@getProviderTerms','_blank','height=300,width=400,location=no')}}'); return false;">Provider Usage Policy</a>
                                </label>
                            </div>
                    </div>
                    <div class="form-group">
                            <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-block" onclick="return validateForm();">Apply</button>
                            </div>
                    </div>
            </fieldset>
        {{ Form::close() }}
    </div>
</div>
<script>
    function validateForm(){
        //alert($('#agreement').prop('checked'));
        if($('#agreement').prop('checked')==false){
            alert('Please accept the terms and conditions');
            return false;
        }
        return true;
    }
</script>
@stop