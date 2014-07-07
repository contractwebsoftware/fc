<div id="navigation">
	<img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo">
	<ul>
		<li><a href="{{ action('ProviderController@getCustomers') }}">Customers</a></li>
		<li><a href="{{ action('ProviderController@getInformation') }}">Information</a></li>
		<li><a href="{{ action('ProviderController@getSetting') }}">Settings</a></li>
		<li><a href="#">Utilities</a></li>
		<li><a href="{{ action('UserController@getLogout') }}">Logout</a></li>
	</ul>
	<div class="clear"></div>
</div>