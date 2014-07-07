<div id="header">
	<img src="{{ asset('img/head-bckg1.jpg') }}" alt="header">
</div>
<div id="navigation">
	<img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo">
	<ul>
		<li><a href="http://www.forcremation.com">home</a></li>
		<li><a href="#">how it works</a></li>
		<li><a href="{{action('ClientController@getSteps')}}">step 1</a></li>
		<li><a href="#">info</a></li>
		<li><a href="#">contact us</a></li>
                <li><a href="{{action('UserController@getLogin')}}">Provider Login</a></li>
	</ul>
	<div class="clear"></div>
</div>