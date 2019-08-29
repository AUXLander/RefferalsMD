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

    $options = (object)[
        'url'   =>  $baseURL . '/v1/crm/contacts',
        'jar'   => false,
        'json'  => (object) [
            'firstName'     => 'firstName',
            'lastName'      => 'lastName',
            'jobTitle'      => 'jobTitle',
            'contact_type'  => 'contact_type',
            'contact_role'  => 'contact_role'
        ],
        'headers' => (object) [
            'X-XSRF-TOKEN'  => '',
            'API_KEY'       => $params->hmacKey,
            'API_SIGNATURE' => ''
        ]
    ];

    $payload = calculateHash($params->hmacKey, 'POST', '/v1/crm/contacts', json_encode($options->json));

    $options->headers->API_SIGNATURE = $payload;

    var_dump($options);

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
}


echo '<pre>';
    POST_Data((object)['hmacKey' => '9e02d1e0245166c3940f46888fd87807']);
echo '</pre>';


?>