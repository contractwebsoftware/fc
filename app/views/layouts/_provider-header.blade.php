<div id="navigation" class="admin-nav">
    <img src="{{ asset('img/logo.jpg') }}" alt="logo" id="logo">
   
    <div class="float-right" style="position: absolute;right: 50px;top: 20px;">
        <a href="https://drive.google.com/?tab=mo" target="_blank" style="text-decoration:none;"><img height="24" width="24" src="/img/google-drive.png"> Google Drive</a> &nbsp;
        <a href="https://rightsignature.com" target="_blank" style="text-decoration:none;"><img height="24" width="24" src="/img/right.png"> Right Signature</a> &nbsp;
        <a href="https://secure.freshbooks.com/loginsearch" target="_blank" style="text-decoration:none;"><img height="24" width="24" src="/img/leaf.png"> Freshbooks</a>
    </div>
    <ul class="nav nav-pills">
            <li class="{{(strpos(Request::path(),'admin/customers')!==false || strpos(Request::path(),'clients/steps')!==false?'active':'')}}"><a href="{{ action('AdminController@getCustomers') }}">My Clients</a></li>
            <li class="{{(strpos(Request::path(),'admin/providers')!==false || strpos(Request::path(),'admin/edit-provider')!==false?'active':'')}}"><a href="{{ action('AdminController@getProviders')}}">Provider Setup</a></li>
            <li class="{{(strpos(Request::path(),'admin/setting')!==false?'active':'')}}"><a href="{{ action('AdminController@getSetting') }}">Account Settings</a></li>
            <li ><a href="{{ action('UserController@getLogout')}}">Logout</a></li>
    </ul>
    <div class="clear"></div>
</div>