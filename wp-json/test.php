<?php



function calculateHash($secret, $method, $url, $body){
    $timestamp = (new DateTime('now', new DateTimeZone('+0000')))->format('Y-m-d\TH:i\Z');
    $payload = $timestamp . $method . $url . $body;
    $computedSignature = hash_hmac("sha256", $payload, $secret, FALSE);
    return $computedSignature;
}


function POST_Data($params){
    $chURL = 'https://dev.getreferralmd.com/login';

    $ch = curl_init($chURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $args = array(
        'username' => 'nick@jeffpayne.net',
        'password' => 'D4u2!@xCngzf'
    );
    curl_setopt($ch, CURLOPT_HEADER, $args);
    //curl_setopt($ch, CURLOPT_VERBOSE, true);
    //$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
    //curl_setopt($ch, CURLOPT_STDERR, $fp);
    
    $result = curl_exec($ch);

    var_dump($result);

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    $cookies = array();
    foreach($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }

    //var_dump($cookies);

    $options = array(
        //'url'   => 'https://dev.getreferralmd.com/v1/crm/contacts',
        //'jar'   => false,
        //'json'  => array(
            'firstName'     => 'firstName',
            'lastName'      => 'lastName',
            'jobTitle'      => 'jobTitle',
            'contact_type'  => 'contact_type',
            'contact_role'  => 'contact_role'
        //),
        //'headers' => array(
        //    'XSRF-TOKEN'    => $cookies['XSRF-TOKEN'],
        //    'API_KEY'       => $params['hmacKey'],
            //'API_SIGNATURE' => '',
            //'referralMD'    => $cookies['referralMD']
        //)
    );

    //$payload = calculateHash($params['hmacSecret'], 'POST', '/v1/crm/contacts', json_encode($options['json']));
    $payload = calculateHash($params['hmacSecret'], 'POST', '/v1/crm/contacts', json_encode($options));
    //$options['headers']['API_SIGNATURE'] = $payload;
    
    


    $chURL = 'https://dev.getreferralmd.com/v1/profile/me/hmac-key';
    //$chURL = 'https://dev.getreferralmd.com/v1/crm/contacts';
    curl_setopt($ch, CURLOPT_URL, $chURL);
    $args = array(
        'XSRF-TOKEN'    => $cookies['XSRF-TOKEN'],
        'API_SIGNATURE' => $payload,
        'API_KEY'       => $params['hmacKey']
    );

    var_dump($args);

    
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Cookie: XSRF-TOKEN=".$cookies['XSRF-TOKEN']));
    //curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options));

    curl_setopt($ch, CURLOPT_HTTPHEADER, $args);

    $response = curl_exec($ch);
    

    var_dump($response);

    curl_close($ch);
}


echo '<pre>';
    
    $params = array(
        'hmacKey'       => '707b000bc175cf58cd6aefb002a9d959',
        'hmacSecret'    => '9e02d1e0245166c3940f46888fd87807'
    );

    POST_Data($params);

echo '</pre>';


?>