<?php

require __DIR__ . '/../vendor/autoload.php';

use CurlRox\Curl;

try {
    
    $curl = new Curl;
    $curl->setUri('http://httpbin.org/get');
    $curl->getRequest();
    $r = $curl->getHttpResponse();
                    
    echo $r;
} catch (Exception $e) {
    echo $e->getMessage();
}
