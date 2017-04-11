<?php
/**
 * Created by PhpStorm.
 * User: iliegurzun
 * Date: 18/03/2017
 * Time: 16:49
 */
@unlink('generated file.pdf');
$url = '127.0.0.1:8000/app_dev.php/convert';
$fields = [
    'template' => 'template',
    'variables' => [
        'variableName'          => 'Fooo',
        'secondVariableName'    => 'TEsting Second Variable',
        'in2plane'              => 'Testing 3',
        'thirdVariableName'     => "BAR $ @"
    ]
];
//$fields = [
//    'template'  => 'demo_oo_text_2017-03-18'
//];

$fields_string = http_build_query($fields);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$result = json_decode($result, true);
curl_close($ch);

file_put_contents('generated file.pdf', base64_decode($result['content']));