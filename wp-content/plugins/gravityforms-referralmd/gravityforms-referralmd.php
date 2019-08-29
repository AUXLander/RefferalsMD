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
    $baseUrl = 'https://dev.getreferralmd.com';
    $options = array(
        'url'   =>   $baseUrl . '/v1/crm/contact',
        'headers' => array(
            'X-XSRF-TOKEN'  => '',
            'API_KEY'       => $params['hmacKey'],
            'API_SIGNATURE' => '',
        )
    );
    $payload = gfrmd_calculateHash( $params['hmacSecret'], 'POST', '/v1/crm/contact', json_encode($body) );
    $options['headers']['API_SIGNATURE'] = $payload;

    $response = wp_remote_post(
        $options['url'],
        array(
            'method' => 'POST',
            'headers' => $options['headers'],
            'body' => $body,
        )
    );
    if ( is_wp_error( $response ) ) {
       $error_message = $response->get_error_message();
       echo "Something went wrong: $error_message";
       return $response;
    } 
    else {
       return $response;
    }
}

add_action('wp_footer','gfrmd_test_connection');
function gfrmd_test_connection() {
    if(is_front_page() || is_home()) {
        echo '<pre>';
            $params = array(
                'hmacKey'       => '707b000bc175cf58cd6aefb002a9d959',
                'hmacSecret'    => '9e02d1e0245166c3940f46888fd87807'
            );
            $body = array(
                'firstName'     => 'firstName',
                'lastName'      => 'lastName',
                'jobTitle'      => 'jobTitle',
                'contact_type'  => 'contact_type',
                'contact_role'  => 'contact_role'
            );
            gfrmd_post_data($params, $body);
        echo '</pre>';
    }
}