<?php

$id = $_POST['id'];
$error = $_POST['error'];

require "./vendor/predis/predis/autoload.php";
Predis\Autoloader::register();
      
$redis = new Predis\Client(array(
    "scheme" => "tcp",
    "host" => "redis-17776.c301.ap-south-1-1.ec2.cloud.redislabs.com",
    "port" => 17776,
    "password" => "SiOXxMK9NecawXRlsdgU0uwflUwUpShk"));
  
  $value = '';
  $value = $redis->get($id);


if( ! isset( $value ) || true === filter_var($error, FILTER_VALIDATE_BOOLEAN ) ) {
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sawpy.sawapi1.repl.co/main/'. $id .'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $json = json_decode($response);


    $redis->set($id, $json[0]->stream_url);
    $value = $redis->get($id);
    echo $value;
} else {
    echo $value;
}