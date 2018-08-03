<?php

require  __DIR__.'/vendor/autoload.php';

$client = new \GuzzleHttp\Client(array(
    'base_url'   => 'http://localhost:8000',
    'defaults' => array(
        'exceptions' => false
    )
));
$nickName = 'ObjectOrienter' .rand(0,9000);
$data = array(
    'nickName' => $nickName,
    'avatarNumber' => 6,
    'tagLine' => 'a test dev'
);
$response = $client->post('/api/programmers',array(
    'body' => json_encode($data)
));

echo $response;
echo "\n\n";
