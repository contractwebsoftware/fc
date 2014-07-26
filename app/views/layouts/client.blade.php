<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Forcremation</title>
	<link rel="stylesheet" href="{{ asset('packages/Bootflat/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/client.css') }}">
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/ie-only.css') }}" />
	<![endif]-->
        <script src="{{ asset('packages/Bootflat/js/jquery-1.11.1.min.js') }}"></script>
	<!--<script src="{{ asset('packages/Bootflat/js/jquery-1.11.1.min.map') }}"></script>-->
	<script src="{{ asset('packages/Bootflat/js/jquery-migrate-1.2.1.min.js') }}"></script>
	
	@section('header')
	@show
</head>
<body class="container">
	<div id="container">
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
                            @include('layouts._client-sidebar')
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
		$(function(){
                    $('.tooltips').tooltip();
                    
                    $('.step_back_btn').click(function(){ window.history.back(); });
                    
                    $('#login_btn').click(function(){
                        $.getJSON( "{{ action('ClientController@getLoginJson') }}", 
                                { login_email: $('#login_email').val(), login_password: $('#login_password').val() } )
                                .done(function( data ) {
                                    if(data){
                                        if(data.fail)alert(data.fail);
                                        else window.location.reload();
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
                                register_password: $('#register_password').val(), register_phone: $('#register_phone').val(), } )
                            .done(function( data ) {
                                if(data){
                                    if(data.fail)alert(data.fail);
                                    else window.location.reload();                                    
                                }else alert('Account creation Failed, please try again');
                            })
                            .fail(function( jqxhr, textStatus, error ) {
                                alert('Registration failed, you may have already registered with that email address');
                            });
                    });
                    
		});
	</script>
	@show
</body>
</html>