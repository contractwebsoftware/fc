<?php
if(is_object(Session::get('no-frame'))){
    $noframe = Session::get('no-frame');
}
else $noframe = false;

if(is_object(Session::get('provider'))){
    $provider = Session::get('provider');
    $provider_name = $provider->id;
}
else $provider_name = '';

?>
@if($noframe)
    <div id="header">
        <img src="{{ asset('img/photo-strip'.rand(1,16).'.jpg') }}" alt="header">
    </div>
@endif
<!--
<nav class="navbar navbar-default client-nav-bar" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
     <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
       <ul class="nav navbar-nav navbar-right">
      </ul>
    </div>
  </div>
</nav>
-->


<div id="navigation">
        <ul id="provider_login_menu">
            <li><a href="{{action('UserController@getLogin')}}">Login</a></li>
            <li><a href="{{action('UserController@getProviderRegistration')}}">Provider Signup</a></li>
            
        </ul>
    <div class="row">
        <div class="col-sm-4">
            <img src="{{ $provider->provider_logo!=''?$provider->provider_logo:asset('img/logo.png?v=2') }}" alt="logo" id="logo">
        </div>
        <div class="col-sm-8">
            <ul class="nav navbar-nav navbar-right">

            <li><a href="http://www.forcremation.com?provider={{$provider_name}}">home</a></li>
            <!--<li><a href="#">how it works</a></li>
            <li><a href="{{action('ClientController@getSteps')}}">step 1</a></li>
            <li><a href="#">info</a></li>-->
            <li><a href="http://www.forcremation.com/contact-us/" target="_blank">contact us</a></li>
            <?php
                if(Sentry::getUser())$client = Client::where('user_id',Sentry::getUser()->id)->first();
                else  $client = new Client();
                if(!is_object($client)){
                    Session::flush();
                    Sentry::logout();
                    $client = new Client();
                }

                $name = $client->first_name;
                //print_r($name);
            ?>
            <li @if($name =="unregistered" or $name =='')style="display:none;"@endif id="logout_link"><a href="{{ action('ClientController@getLogout') }}">Logout</a></li>
            <li @if(Sentry::check() and $name !='unregistered' and $name !='')style="display:none;"@endif id="login_link"><a href="#" id="login_to_save" data-toggle="modal" data-target="#loginModal">Register to Save your File</a></li>

            </ul>
        </div>
    </div>
</div>

@if(is_object(Session::get('provider')))
    <div class="row selected-provider-box">
           <table width="100%" align="center">
            <tr><td align="left">
                    <b style="font-size: 28px;color:#0E7DB6 !important;">{{$provider->business_name}}</b>
                    <br>{{$provider->address}}{{$provider->address!=''?',':''}} {{$provider->city}}{{$provider->city!=''?',':''}} {{$provider->state}} {{$provider->zip}}
                </td>
                <td align="right">
                    <b style="font-size: 20px;color:#0E7DB6 !important;">Estimated Cost: $<span id="current_total_price"></span></b>
                    <br><span>Phone: {{$provider->phone}}</span>
                     @if($provider->ProviderPriceSheet!=null)
                        <Br /><a style="font-weight:bold;" href="{{asset('provider_files/'.$provider->id.'/'.$provider->ProviderPriceSheet->file_name)}}" target="_blank">View Pricing Sheet</a>
                     @endif
                </td></tr>
           </table>
    </div>
@endif

