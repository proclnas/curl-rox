<?php

require '../vendor/autoload.php';

try {
    $webCrawler = new \CurlRox\Curl;
    $r = $webCrawler->Uri('http://httpbin.org/get')
                    ->getRequest()
                    ->getHttpResponse();

    echo $r;
} catch (Exception $e) {
    echo $e->getMessage();
}
