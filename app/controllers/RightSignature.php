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

    function getDocuments($cid='',$pid='',$state='')
    {
        ## https://rightsignature.com/apidocs/api_documentation_php
        # state = 'completed', 'pending', 'trash'  //'completed', 'pending', 'trash'


        $url = $this->secure_base_url . "/api/documents.xml";
        #echo "Getting documents...\n";


        ## TAG SEARCH
        $tag_search = $and = $state_search = '';
        if($cid != '' or $pid != '')$tag_search = 'tags=';
        if($cid != ''){
            $tag_search .= 'cid:'.$cid;
            $and=',';
        }
        if($pid != '')$tag_search .= $and.'pid:'.$pid;


        if($state != '') $state_search = 'state='.$state;

        if($state_search != '' || $tag_search != '')$url .= '?';
        if($tag_search != ''){
            $url .= $tag_search;
            $amp = '&';
        }
        if($state_search != '')$url .= $amp.$state_search;

        #dd($url);
        $header = Array();
        $response = $this->httpRequest($url, $header, "GET");
        return $response;
    }

    function getSignDocument($doc_guid='')
    {
        ##  https://rightsignature.com/api/documents/ABCDEFGHIJKLMN0P.xml
        # state = 'completed', 'pending', 'trash'  //'completed', 'pending', 'trash'


        $url = $this->secure_base_url . '/api/documents/'.$doc_guid.'.xml';
        #echo "Getting documents...\n";

        #dd($url);
        $header = Array();
        $response = $this->httpRequest($url, $header, "GET");
        return $response;
    }

    function sendReminder($doc_guid='')
    {
        $url = $this->secure_base_url . "/api/documents/$doc_guid/send_reminders.xml";
        $header = Array();
		$xml = "<?xml version='1.0' encoding='UTF-8'?><document></document>";
        $response = $this->httpRequest($url, $header, "POST", $xml);
//dd($response);

        return  $response;

    }

    function sendDocuments($doc_data)
    {
        $url = $this->secure_base_url . "/api/documents.xml";
        #$url = $this->secure_base_url . "/builder/new?rt=test";



        #$doc_data['doc_url'] = 'http://www.forcremation.com/images/test.pdf';
//.'<recipient><is_sender>true</is_sender><role>signer</role></recipient></recipients>'
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                    .'<document>'
                        .'<document_data><type>url</type><value>'.$doc_data['doc_url'].'</value></document_data><subject>Signature Required</subject>'
                        .'<recipients>'
                            .'<recipient><name>'.$doc_data['doc_cc_name'].'</name><email>'.$doc_data['doc_cc_email'].'</email><role>cc</role></recipient>'
                            .'<recipient><name>'.$doc_data['doc_to_sign_name'].'</name><email>'.$doc_data['doc_to_sign_email'].'</email><role>signer</role></recipient>'
                            .'<recipient><is_sender>true</is_sender><role>cc</role></recipient></recipients>'
                        .'<tags>'
                            .'<tag><name>Sent From API</name></tag>'
                            .'<tag><name>Client</name><value>'.$doc_data['doc_name'].'</value></tag>'
                            .'<tag><name>Deceased Name</name><value>'.$doc_data['doc_deceased'].'</value></tag>'
                            .'<tag><name>Pid</name><value>'.$doc_data['doc_pid'].'</value></tag>'
                            .'<tag><name>Cid</name><value>'.$doc_data['doc_cid'].'</value></tag>'
                            .$doc_data['doc_forms_included']
                            .'</tags>'
                        .'<expires_in>30 days</expires_in>'
                        .'<action>'.$doc_data['doc_action'].'</action>'
                        .'<callback_location>https://provider.forcremation.com/admin/redirect-callback/</callback_location>'
                        .'<use_text_tags>false</use_text_tags>'
                      .'</document>';
        /*
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
            .'<callback_location>https://provider.forcremation.com/admin/redirect-callback/</callback_location>'
            .'<use_text_tags>false</use_text_tags>'
            .'</document>';*/

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
            $curl = curl_init($url);
            #curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            #curl_setopt($curl, CURLOPT_HEADER,true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);        // Timeout in seconds
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            // Append 'api-token' to Headers
            #curl_setopt($curl, CURLOPT_HTTPHEADER, array("api-token: $this->secure_token"));

            if ($body) {


                $headers = array(
                    "Content-type: text/xml",
                    "api-token:".$this->secure_token,
                    "Expect: "
                );

                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


                #curl_setopt($curl, CURLOPT_HTTPHEADER, array("api-token: ".$this->secure_token,"Content-Type:text/xml;charset=utf-8"));
                #dd(curl_getinfo($curl));
                #$curl["header_size"] = (int)$curl["header_size"]-1;
            }
            else {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("api-token: $this->secure_token"));
            } // Set the headers.


            #if (FALSE === $curl)
            #    throw new Exception('failed to initialize');


            $data = curl_exec($curl);
            $in = curl_getinfo($curl, CURLINFO_HEADER_OUT);
            #dd($in);
            #Return Redirect::back()->with('error', $data);

            $information = curl_getinfo($curl);
            #echo '<pre>'.$in.'<br />'.$body;
           # dd($data);
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