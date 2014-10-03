<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Welcome to ForCremation</h2>

        Hello {{$provider->business_name}},<br />
        Thank you for applying to become a preferred provider<br /><br />
        
        Your application has been submitted and a ForCremation representative will be contacting you shortly. <br />
        Becoming a provider can take 7 to 14 days. If you have any questions feel free to call us at (541) 200-9989.<br /><br />
            
        Account Login: {{$provider->email}}<br />
        Account Password: {{$pass}}<br />
        Your Membership Plan: {{($provider->plan_id=='1' || $provider->plan_id=='0'?'Basic':'Premium')}}.
        Website: http://app.forcremation.com/users/login<br /><br />

    </body>
</html>