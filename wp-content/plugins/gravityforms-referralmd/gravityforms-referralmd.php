<?php
/*
Plugin Name: Gravity Forms: GetReferralMD
Plugin URI: https://github.com/nickhempsey/gravityforms-referralmd
Description: Send data to ReferralMD from Gravity Forms
Version: 1.0.0
Author: @nickhempsey, @auxlander
Author URI: https://jeffpayne.net
*/
function gfrmd_calculateHash($secret, $method, $url, $body){
    $timestamp = (new DateTime('now', new DateTimeZone('+0000')))->format('Y-m-d\TH:i\Z');
    $payload = $timestamp . $method . $url . $body;
    $computedSignature = hash_hmac("sha256", $payload, $secret, FALSE);
    return $computedSignature;
}
function gfrmd_post_data($params, $body){
    /*
        $params = array(
            'baseUrl'       => '',
            'endpointUrl'   => '',
            'hmacKey'       => '',
            'hmacSecret'    => ''
        );
     */
    $baseUrl = $params['baseUrl'];

    if( $baseUrl == NULL || strlen($baseUrl) < 5 ) {
        $baseUrl = 'https://dev.getreferralmd.com';
    }

    $options = array(
        'url'     => $baseUrl . $params['endpointUrl'],
        'headers' => array(
            'X-XSRF-TOKEN'  => '',
            'API_KEY'       => $params['hmacKey'],
            'API_SIGNATURE' => '',
        )
    );

    $payload = gfrmd_calculateHash( $params['hmacSecret'], 'POST', $params['endpointUrl'], json_encode($body) );
    $options['headers']['API_SIGNATURE'] = $payload;

    $response = wp_remote_post(
        $options['url'],
        array(
            'method' => 'POST',
            'headers' => $options['headers'],
            'body' => $body,
        )
    );
    if (is_wp_error($response)) {
       $error_message = $response->get_error_message();
       echo "Something went wrong: $error_message";
    }
    return $response;
}



add_action('wp_footer','gfrmd_test_connection');
function gfrmd_test_connection() {
    
    if(is_front_page() || is_home()) {
        echo '<pre style="color:white">';
            $params = array(
                'endpointUrl'   => '/v1/profile/me/invitations',
                'hmacKey'       => '707b000bc175cf58cd6aefb002a9d959',
                'hmacSecret'    => '9e02d1e0245166c3940f46888fd87807'
            );
            $body = array(
                'emails'    => array("auxlander.1000@gmail.com"),
                'message'   => "Test"
            );
            print_r(gfrmd_post_data($params, $body));
        echo '</pre>';
    }
    
}