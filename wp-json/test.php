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
    $result = curl_exec($ch);

    var_dump($result);

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    $cookies = array();
    foreach($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }

    $options = array(
        'url'   =>  'https://dev.getreferralmd.com' . '/v1/crm/contacts',
        'jar'   => false,
        'json'  => array(
            'firstName'     => 'firstName',
            'lastName'      => 'lastName',
            'jobTitle'      => 'jobTitle',
            'contact_type'  => 'contact_type',
            'contact_role'  => 'contact_role'
        ),
        'headers' => array(
            'X-XSRF-TOKEN'  => $cookies['XSRF-TOKEN'],
            'API_KEY'       => $params['hmacKey'],
            'API_SIGNATURE' => '',
            'referralMD'    => $cookies['referralMD']
        )
    );

    $payload = calculateHash($params['hmacSecret'], 'POST', '/v1/crm/contacts', json_encode($options['json']));
    $options['headers']['API_SIGNATURE'] = $payload;
    
    var_dump($options);


    $chURL = 'https://dev.getreferralmd.com/v1/profile/me/hmac-key';
    curl_setopt($ch, CURLOPT_URL, $chURL);
    curl_setopt($ch, CURLOPT_HEADER, array());
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