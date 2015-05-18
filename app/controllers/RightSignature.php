<?php
/**
 * Created by PhpStorm.
 * User: bendavol
 * Date: 5/16/15
 * Time: 10:54 AM
 */


class RightSignature
{
    public $base_url = "https://rightsignature.com";
    public $secure_base_url = "https://rightsignature.com";
    #Consumer Key: pZ1pInLyDdEAzJkP2DATtIpd0kQI2uUNGpTug67g
    #oAuth Consumer Secret:	VjFf4DzpByqNzSt7d7pyUzGFIM0iaXYIHG1Cj6m2

    public $oauth_callback = "oob";
    public $secure_token = "4nymWlQGXzc8IcULYCoDbzeX1aIaPdOToH4Fu5XK";

    public $debug = false;

    function __construct($oauth_callback = NULL)
    {

        #$this->secure_token = $secure_token;
        if ($oauth_callback) {
            $this->oauth_callback = $oauth_callback;
        }

    }

    function getAccessToken()
    {
        $headers = Array();
        $url = $request->to_url();
        $response = $this->httpRequest($url, $headers, "GET");
        parse_str($response, $response_params);
        if($debug) echo $response . "\n";

        $this->access_token = new OAuthConsumer($response_params['oauth_token'], $response_params['oauth_token_secret'], 1);
    }

    function getDocuments()
    {
        $url = $this->secure_base_url . "/api/documents.xml";
        #echo "Getting documents...\n";
        $header = Array();
        $response = $this->httpRequest($url, $header, "GET");
        return $response;
    }

    function sendDocuments($doc_data)
    {
        $url = $this->secure_base_url . "/api/documents.xml";
        #$url = $this->secure_base_url . "/builder/new?rt=test";



        $doc_data['doc_url'] = 'http://www.forcremation.com/images/test.pdf';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                    .'<document>'
                        .'<document_data><type>url</type><value>'.$doc_data['doc_url'].'</value></document_data><subject>'.$doc_data['doc_name'].'</subject>'
                        .'<recipients>'
                            .'<recipient><name>'.$doc_data['doc_cc_name'].'</name><email>'.$doc_data['doc_cc_email'].'</email><role>cc</role></recipient>'
                            .'<recipient><name>'.$doc_data['doc_to_sign_name'].'</name><email>'.$doc_data['doc_to_sign_email'].'</email><role>signer</role></recipient>'
                            .'<recipient><is_sender>true</is_sender><role>signer</role></recipient></recipients>'
                        .'<tags>'
                            .'<tag><name>sent_from_api</name></tag>'
                            .'<tag><name>client_id</name><value>'.$doc_data['doc_client_id'].'</value></tag>'
                            .'<tag><name>forms</name><value>'.$doc_data['doc_forms_included'].'</value></tag>'
                            .'</tags>'
                        .'<expires_in>30 days</expires_in>'
                        .'<action>'.$doc_data['doc_action'].'</action>'
                        .'<callback_location>http://provider.forcremation.com/admin/redirect-callback/</callback_location>'
                        .'<use_text_tags>false</use_text_tags>'
                      .'</document>';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<document>'
            .'<document_data><type>url</type><value>'.$doc_data['doc_url'].'</value></document_data><subject>ForCremation Signature</subject>'
            .'<recipients>'
            .'<recipient><name>RightSignature</name><email>bendavol@gmail.com</email><role>cc</role></recipient>'
            .'<recipient><name>Ben Davol</name><email>bendavol+test@gmail.com</email><role>signer</role></recipient>'
            .'<recipient><is_sender>true</is_sender><role>signer</role></recipient></recipients>'
            .'<tags><tag><name>sent_from_api</name></tag><tag><name>mutual_nda</name></tag><tag><name>user_id</name><value>123456</value></tag></tags>'
            .'<expires_in>5 days</expires_in>'
            .'<action>redirect</action>'
            .'<callback_location>http://provider.forcremation.com/admin/redirect-callback/</callback_location>'
            .'<use_text_tags>false</use_text_tags>'
            .'</document>';

        $header = Array();
#dd($xml);
        Log::info("Doc Sent: $url ".$xml);
        $response = $this->httpRequest($url, $header, "POST", $xml);

        return $response;
    }

    function addUser($uname, $email)
    {
        $url = $this->secure_base_url . "/api/users.xml";
        #echo "Adding user...\n";
        $xml = "<?xml version='1.0' encoding='UTF-8'?><user><name>$uname</name><email>$email</email></user>";
        #echo $xml;

        $header = Array();
        $response = $this->httpRequest($url, $header, "POST", $xml);
        return $response;
    }

    function testPost()
    {
        $url = $this->secure_base_url . "/api/test.xml";
        $xml = "<?xml version='1.0' encoding='UTF-8'?><testnode>Hello World!</testnode>";
        echo $xml;

        $header = Array();
        $response = $this->httpRequest($url, $header, "POST", $xml);
        return $response;
    }

    function httpRequest($url, $headers, $method, $body = NULL)
    {
        if (!$method) {
            $method = "GET";
        }

        // Append 'api-token' to Headers
        #$headers[] = "api-token: ".$this->secure_token;
        #$headers[] = "Content-Type: text/xml;charset=utf-8";
        #dd($headers);
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            // Append 'api-token' to Headers
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("api-token: $this->secure_token")); // Set the headers.


            if ($body) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml;charset=utf-8", "api-token: $this->secure_token"));


            }


            if (FALSE === $curl)
                throw new Exception('failed to initialize');


            $data = curl_exec($curl);
            $in = curl_getinfo($curl, CURLINFO_HEADER_OUT);
            #dd($in);
            $information = curl_getinfo($curl);
            #echo '<pre>'.$in;
            #dd($information);


            if (FALSE === $data)
                throw new Exception(curl_error($curl), curl_errno($curl));

            // ...process $content now
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }



        #echo'<pre>';print_r($data);
        if ($this->debug) {
            #echo "In httpRequest: Recieved DATA\n===========\n" . $data . "\n===========\n";
        }
        curl_close($curl);

        return $data;
    }




}