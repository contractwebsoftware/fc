<div id="header">
	<img src="{{ asset('img/head-bckg1.jpg') }}" alt="header">
</div>
<div id="navigation">
	<a href="http://www.forcremation.com"><img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo"></a>

	<ul>
		<li><a href="http://www.forcremation.com">home</a></li>
		<li><a href="http://www.forcremation.com/help">how it works</a></li>
		<li><a href="{{action('ClientController@getSteps')}}">step 1</a></li>
		<li><a href="http://www.forcremation.com/general-info/">info</a></li>
		<li><a href="http://www.forcremation.com/contact-us/">contact us</a></li>

		@if( strpos(Request::path(),'users/provider-registration')===false )
			<li><a href="{{action('UserController@getLogin')}}">Login</a></li>
			<li><a href="{{action('UserController@getProviderRegistration')}}">Provider Signup</a></li>
		@endif
	</ul>
	<div class="clear"></div>
</div>