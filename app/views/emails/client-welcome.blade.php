<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Welcome to ForCremation</h2>
        
        Hello {{$client->first_name.' '.$client->last_name}},<br />
        Thank you for allowing us to provide information about cremation services. We are here to help you through this sometimes overwhelming task of taking care of someone you care about and love. We recognize this time is difficult and hope that our simple service can help make things easier.
        <br /><br />
        Call us at {{ $provider->phone }}<if you have any questions or concerns.<br />
        We wait for your instructions - and are here day or night.<br />
        Your Login and Access information is provided below for when you return to http://www.forcremation.com<br />
        We have the simplest, most convenient method for cremation and look forward to serving you.<br />
        {{ $provider_contact }}<br /><br />

        Account Login: {{$login}}<br />
        Account Password: {{$pass}}<br />
        Website: http://app.forcremation.com/users/login<br /><br />

    </body>
</html>