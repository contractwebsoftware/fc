<div id="navigation">
    <img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo">
    <ul>
            <li><a href="{{ action('AdminController@getCustomers') }}">Customers</a></li>
            <li><a href="{{ action('AdminController@getProviders')}}">Providers</a></li>
            <!--<li><a href="{{ action('AdminController@getAllUser') }}">All Users</a></li>-->
            <li><a href="{{ action('AdminController@getBillingPage') }}">Billings</a></li>
            <li><a href="{{ action('AdminController@getSetting') }}">Settings</a></li>
            <li><a href="{{ action('UserController@getLogout')}}">Logout</a></li>
    </ul>
    <div class="clear"></div>
   
    
    
    
</div>