<div id="navigation" class="admin-nav">
    <img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo">
   
    <ul class="nav nav-pills">
            <li class="{{(strpos(Request::path(),'admin/customers')!==false || strpos(Request::path(),'clients/steps')!==false?'active':'')}}"><a href="{{ action('AdminController@getCustomers') }}">Customers</a></li>
            <li class="{{(strpos(Request::path(),'admin/providers')!==false || strpos(Request::path(),'admin/edit-provider')!==false?'active':'')}}"><a href="{{ action('AdminController@getProviders')}}">Providers</a></li>
            <!--<li class="{{(Request::path('admin/*')?'active':'')}}"><a href="{{ action('AdminController@getAllUser') }}">All Users</a></li>-->
            <li class="{{(strpos(Request::path(),'admin/billing-page')!==false?'active':'')}}"><a href="{{ action('AdminController@getBillingPage') }}">Billings</a></li>
            <li class="{{(strpos(Request::path(),'admin/setting')!==false?'active':'')}}"><a href="{{ action('AdminController@getSetting') }}">Settings</a></li>
            <li ><a href="{{ action('UserController@getLogout')}}">Logout</a></li>
    </ul>
    <div class="clear"></div>
</div>