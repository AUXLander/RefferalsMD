<?php



function calculateHash($secret, $method, $url, $body){
    $timestamp = (new DateTime('now', new DateTimeZone('+0000')))->format('Y-m-d\TH:i\Z');
    $payload = $timestamp . $method . $url . $body;
    $computedSignature = hash_hmac("sha256", $payload, $secret, FALSE);
    return $computedSignature;
}


function POST_Data($params){
    $baseURL = 'https://dev.getreferralmd.com';
    //$params->hmacKey = API KEY = 707b000bc175cf58cd6aefb002a9d959
    //API SECRET 9e02d1e0245166c3940f46888fd87807

    $options = array(
        'url'   =>  $baseURL . '/v1/crm/contacts',
        'jar'   => false,
        'json'  => array(
            'firstName'     => 'firstName',
            'lastName'      => 'lastName',
            'jobTitle'      => 'jobTitle',
            'contact_type'  => 'contact_type',
            'contact_role'  => 'contact_role'
        ),
        'headers' => array(
            'X-XSRF-TOKEN'  => 'zoBBfiGY-lIdWxqrypSV2eU0rCt5ZOj404Zo',
            'API_KEY'       => $params['hmacKey'],
            'API_SIGNATURE' => '',
            'referralMD'    => "s:TFptIpopZq9ROIS0FaVkUu4W_yzG-1hI.kVYHhthMyGilOqR3bi2lJbVI7ruBZu9/20WNHcX3TiI"
        )
    );

    $payload = calculateHash($params['hmacSecret'], 'POST', '/v1/crm/contacts', json_encode($options['json']));

    $options['headers']['API_SIGNATURE'] = $payload;

    var_dump($options);
    /*
    $url = 'https://dev.getreferralmd.com/v1/profile/me/hmac-key';
    $data = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($options)
        )
    );
    $context  = stream_context_create($data);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { 
        echo '<p style="color:red">Error</p><br>';
    }
    var_dump($result);
    */

    /*
    file_get_contents(
        'https://dev.getreferralmd.com/v1/profile/me/hmac-key'
        . '?'
    );
    */

    //file_get_contents('https://dev.getreferralmd.com/login')

    /*
    $ch = curl_init('https://dev.getreferralmd.com/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 
        array(
            'username' => 'nick@jeffpayne.net',
            'password' => 'D4u2!@xCngzf'
        )
    );
    $result = curl_exec($ch); 



    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    $cookies = array();
    foreach($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    var_dump($cookies);
    */

    
    $ch = curl_init('https://dev.getreferralmd.com/v1/profile/me/hmac-key');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
    $response = curl_exec($ch);
    curl_close($ch);

    var_dump($response);
    
}


echo '<pre>';

    $params = array(
        'hmacKey'       => '707b000bc175cf58cd6aefb002a9d959',
        'hmacSecret'    => '9e02d1e0245166c3940f46888fd87807'
    );

    POST_Data($params);

echo '</pre>';


?>