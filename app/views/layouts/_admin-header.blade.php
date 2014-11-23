<div id="navigation" class="admin-nav">
    <img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo">
   
    <div class="float-right" style="position: absolute;right: 50px;top: 20px;">
        <a href="https://drive.google.com/?tab=mo" target="_blank" style="text-decoration:none;"><img height="24" width="24" src="http://www.forcremation.com/images/google-drive.png"> Google Drive</a> &nbsp;
        <a href="https://rightsignature.com" target="_blank" style="text-decoration:none;"><img height="24" width="24" src="http://www.forcremation.com/images/right.png"> Right Signature</a> &nbsp;
        <a href="https://secure.freshbooks.com/loginsearch" target="_blank" style="text-decoration:none;"><img height="24" width="24" src="http://www.forcremation.com/images/leaf.png"> Freshbooks</a>
    </div>
    <ul class="nav nav-pills">
            <li class="{{(strpos(Request::path(),'admin/customers')!==false || strpos(Request::path(),'clients/steps')!==false?'active':'')}}"><a href="{{ action('AdminController@getCustomers') }}">Clients</a></li>
            <li class="{{(strpos(Request::path(),'admin/providers')!==false || strpos(Request::path(),'admin/edit-provider')!==false?'active':'')}}"><a href="{{ action('AdminController@getProviders')}}">Providers</a></li>
            <li class="{{(strpos(Request::path(),'admin/funeralhomes')!==false || strpos(Request::path(),'admin/edit-funeralhomes')!==false?'active':'')}}"><a href="{{ action('AdminController@getFuneralhomes')}}">Funeral Homes</a></li>
            <!--<li class="{{(Request::path('admin/*')?'active':'')}}"><a href="{{ action('AdminController@getAllUser') }}">All Users</a></li>-->
            <li class="{{(strpos(Request::path(),'admin/billing-page')!==false?'active':'')}}"><a href="{{ action('AdminController@getBillingPage') }}">Billings</a></li>
            <li class="{{(strpos(Request::path(),'admin/setting')!==false?'active':'')}}"><a href="{{ action('AdminController@getSetting') }}">Settings</a></li>
            <li class="{{(strpos(Request::path(),'users/manage-users')!==false?'active':'')}}"><a href="{{ action('UserController@getManageUsers') }}">Admin Users</a></li>
            <li ><a href="{{ action('UserController@getLogout')}}">Logout</a></li>
    </ul>
    <div class="clear"></div>
    
</div>