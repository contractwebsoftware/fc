<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Hello Administrator</h2>

        New Customer has completed the Steps.<br /> 
        Website: http://www.forcremation.com<br /><br />

        Name: {{$client->first_name.' '.$client->last_name}}<br />
        Phone: {{$client->phone}}<br />
        Email: {{$client->email}}<br />
        City: {{$client->city}}<br />
        State: {{$client->state}}<br />
        Services For: {{$client->DeceasedInfo->first_name.' '.$client->DeceasedInfo->last_name}} <br /><br />

        Status: {{ucwords(str_replace('_',' ',$client->DeceasedInfo->cremation_reason))}} <br /><br />
        
        Plan Details: {{ucwords(str_replace('_',' ',$client->CremainsInfo->cremain_plan))}}<br />
        Plan Name: <br />
                <?php 
                    if($client->CremainsInfo->package_plan=='1')echo 'Cremation Plan A';
                    if($client->CremainsInfo->package_plan=='2')echo 'Cremation Plan B';
                ?><br />
        Additional Services Requested: <br />
                <?php 
                    if($client->CremainsInfo->cert_plan=='deathcert_wurn') echo 'Mail certificate(s) with urn';
                    if($client->CremainsInfo->cert_plan=='deathcert_cep') echo 'Mail certificate(s) seperately';
                    if($client->CremainsInfo->cert_plan=='deathcert_pickup') echo 'Pick up certificate(s) at our office';
                    echo '<br />';
                    if($client->CremainsInfo->cremation_shipping_plan=='burial') echo 'I will pick up at your facility';
                    if($client->CremainsInfo->cremation_shipping_plan=='scatter_on_land') echo 'Scatter on Land (non-witness, non-recoverable)';
                    if($client->CremainsInfo->cremation_shipping_plan=='scatter_at_sea') echo 'Scatter at Sea (non-witness, non-recoverable)';
                    if($client->CremainsInfo->cremation_shipping_plan=='ship_to_you') echo 'Ship to address via certified registered mail';                   
                ?>
        
    </body>
</html>