<?php

error_reporting(E_ALL);

require __DIR__ . '/../../vendor/autoload.php';

use CurlRox\Curl;

try {
    $curl = new Curl;
    $curl->setUri('http://httpbin.org/get');
    $curl->getRequest();
    $response = $curl->getHttpResponse();

    var_dump($response); exit;
} catch (Exception $e) {
    var_dump($e->getMessage());
}