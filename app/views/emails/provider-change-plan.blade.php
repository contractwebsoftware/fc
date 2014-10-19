<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Hello Administrator</h2>
        You will need to update your provider billing for the following client:<br />
        
        Provider: {{$provider->business_name}} ,<br />
        Account Login: {{$provider->email}}<br />
        Phone: {{$provider->phone}}<br />
        Membership Plan: {{($provider->plan_id=='1' || $provider->plan_id=='0'?'Basic':'Premium')}}.
        
    </body>
</html>