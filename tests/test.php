<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$output = curl_exec($ch);
curl_close($ch);

if ($output === 'Hello World') {
    echo 'OK';
    exit(0);
} else {
    exit(1);
}
