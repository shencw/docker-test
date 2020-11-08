<?php


$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:9001');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
if (!empty($data)) {
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
}
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($curl);
//var_dump(curl_error($curl));
curl_close($curl);
var_dump($output);
