<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Forcremation</title>
	<link rel="stylesheet" href="{{ asset('packages/Bootflat/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/client.css') }}">
    <link rel="stylesheet" href="{{ asset('css/colorbox.css') }}">
    <!--[if IE]>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/ie-only.css') }}" />
	<![endif]-->
        <script src="{{ asset('packages/Bootflat/js/jquery-1.11.1.min.js') }}"></script>
	<!--<script src="{{ asset('packages/Bootflat/js/jquery-1.11.1.min.map') }}"></script>-->
	<script src="{{ asset('packages/Bootflat/js/jquery-migrate-1.2.1.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('videos/jwplayer/jwplayer.js') }}" ></script>

	@section('header')
	@show
    <?php
        if(!is_null(Session::get('no-frame'))){
            $noframe = Session::get('no-frame');
            if($noframe == 'y'){
				$w = (int)((int)Input::get('w')-300);
				
				$w = $w < 320 ? 320 : $w;
				$w = $w > 1000 ? 1000 : $w;
				
                ?>
                <link rel="stylesheet" href="<?=asset('css/client-no-frame.css')?>">
                <style>
                    body,#no-frame-container,#content{width:<?=$w?>px!important;}
					
                </style>
                <?php
            }
        }
        else $noframe = false;
    ?>
</head>
<body class="<?php if($noframe == false)echo 'container';?>">
	<div id="<?php if($noframe == false)echo 'container';else echo 'no-frame-container';?>">
		@yield('header')
            @if ( !Sentry::check() )
				@include('layouts._client-header')
            @else
				{{-- take the groups --}}
				<?php $group = json_decode(Sentry::getUser()->getGroups()); ?>
				@if( $group[0]->name == 'Provider')
					@include('layouts._provider-header')
				@elseif( $group[0]->name == 'Admin')
					@include('layouts._admin-header')
				@elseif( $group[0]->name == 'Client')
					@include('layouts._client-header')
				
				@endif
			@endif
		@show
		<div id="content">
			@include('layouts.notification')
                        <div  class="row">
                            <?php if($noframe == false) {?> @include('layouts._client-sidebar') <?php } ?>
                            @yield('content')
                        </div>
		</div>
		<div id="footer"></div>
	</div>
	@section('footer')
        
        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Sign In</h4>
              </div>
              <div class="modal-body">
                <div class="row form-group">
                    <div class="col-sm-12">
                        <h2>Login</h2>
                        <input type="text" id="login_email" placeholder="Email" />
                        <input type="password" id="login_password" placeholder="Password" />
                        <button class="btn btn-primary pull-right" id="login_btn">Login</button>
                    </div>
                    <div class="clear" /><br />
                    <div class="col-sm-12">
                        <h2>Register</h2>
                        <input type="text" id="register_name" placeholder="Your Name" />
                        <input type="text" id="register_email" placeholder="Email" />
                        <input type="password" id="register_password" placeholder="Password" />
                        <input type="text" id="register_phone" placeholder="Phone" />
                        <button class="btn btn-primary pull-right" id="register_btn">Register</button>
                    </div>
                </div>
                  
              </div>
            </div>
          </div>
        </div>
        
	<script src="{{ asset('packages/Bootflat/js/bootstrap.min.js') }}"></script>
	<script>
	    $('#current_total_price').html('{{$client->sale_summary_r['total'] > $provider->pricing_options->basic_cremation ? $client->sale_summary_r['total']:$provider->pricing_options->basic_cremation}}');


		$(function(){
            $('.tooltips').tooltip();

            $('.step_back_btn').click(function(){ window.history.back(); });

            $('#login_btn').click(function(){
                $.getJSON( "{{ action('ClientController@getLoginJson') }}",
                        { login_email: $('#login_email').val(), login_password: $('#login_password').val() } )
                        .done(function( data ) {
                            if(data){
                                if(data.fail)alert(data.fail);
                                else window.location.href = "{{ url('clients/steps') }}";
                            }
                            else alert('Login Failed, please try again');
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            alert('Login Failed, please try again');
                        });
            });

            $('#register_btn').click(function(){
                $.getJSON( "{{ action('ClientController@getCreationAccountJson') }}",
                    { register_name: $('#register_name').val(), register_email: $('#register_email').val(),
                        register_password: $('#register_password').val(), register_phone: $('#register_phone').val() } )
                    .done(function( data ) {
                        if(data){
                            if(data.fail)alert(data.fail);
                            else window.location.href = "{{ url('clients/steps') }}";
                        }else alert('Account creation Failed, please try again');
                    })
                    .fail(function( jqxhr, textStatus, error ) {
                        alert('Registration failed, you may have already registered with that email address');
                    });
            });

            if($('#login_to_save').is(':visible') && $('fieldset#step1').text()==''){
                $('.warn-login-text').html('<a href="#" class="warning-text" data-toggle="modal" data-target="#loginModal">For Account Access Please Create a Login and Password Here</a>');
            }

            $('#owl-example label.btn-primary').each(function(){
                $('#owl-example label.btn-primary').find('b').html('<i class="glyphicon glyphicon-unchecked"></i> &nbsp; Select ');
                $('#owl-example label.btn-primary.active').find('b').html('<i class="glyphicon glyphicon-check" style="color:#77CA77;"></i> &nbsp; Selected');
            });

            $('#owl-example label.btn-primary').click(function(){
                $('#owl-example label.btn-primary').find('b').html('<i class="glyphicon glyphicon-unchecked"></i> &nbsp; Select ');
                $(this).find('b').html('<i class="glyphicon glyphicon-check" style="color:#77CA77;"></i> &nbsp; Selected');
            });

		});
	</script>
	@show
            <!-- Google Code for Remarketing Tag -->
        <!--------------------------------------------------
        Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
        --------------------------------------------------->
        <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = 1019102063;
            var google_custom_params = window.google_tag_params;
            var google_remarketing_only = true;
            /* ]]> */
        </script>
        <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
            <div style="display:inline;">
                <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1019102063/?value=0&amp;guid=ON&amp;script=0"/>
            </div>
        </noscript>
        <script src="{{ asset('js/jquery.colorbox-min.js') }}"></script>

</body>
</html>