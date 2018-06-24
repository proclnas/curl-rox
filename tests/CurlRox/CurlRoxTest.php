<?php


class CurlRoxTest extends \PHPUnit_Framework_TestCase {

    public function testExtensionsLoaded()
    {
        $this->assertTrue(extension_loaded('curl'));
    }

    public function testGetAndPostRequest()
    {
        $curl = new \CurlRox\Curl;
        $r = $curl->Uri('http://127.0.0.1:8000/server.php')
                  ->getRequest();

        $this->assertNotNull($r->getHttpResponse());

        $curl = new \CurlRox\Curl;
        $r = $curl->Uri('http://127.0.0.1:8000/server.php')
                  ->setPostPayload([
                      'test' => '1',
                      'foo' => 'bar'
                  ])->postRequest();

        $this->assertNotNull($r->getHttpResponse(true));
        $this->assertContains('foo', $r->getHttpResponse());
    }

    public function testCookieFile()
    {
        $curl = new \CurlRox\Curl;
        $curl->Uri('http://127.0.0.1:8000/server.php')
             ->getRequest();

        $cookie_file = $curl->getCookieFile();
        $this->assertFileExists($cookie_file);
    }

    public function testDomIstanceOf()
    {
        $curl = new \CurlRox\Curl;
        $curl->Uri('http://127.0.0.1:8000/server.php?test')
             ->getRequest()
             ->setCallback(function($http_response, \DiDom\Document $dom, \CurlRox\Curl $curl_rox){
                $elements = $dom->find('a');

                foreach ($elements as $element)
                    $this->assertInstanceOf('\Didom\Element', $element);
             });
    }

    public function testJsonResponse()
    {
        $curl = new \CurlRox\Curl;
        $r = $curl->Uri('http://127.0.0.1:8000/server.php')
                  ->setPostPayload(['test' => '1', 'foo' => 'bar'])
                  ->postRequest()
                  ->getHttpResponse(true);

        $this->assertArrayHasKey('foo', $r);
    }

    public function testDebugTo()
    {

        $file_name = 'debug.txt';
        $curl = new \CurlRox\Curl;
        $curl->Uri('http://127.0.0.1:8000/server.php')
             ->setPostPayload(['test' => '1', 'foo' => 'bar'])
             ->postRequest()
             ->debugTo($file_name);

        $this->assertFileExists($file_name);
        unlink($file_name);
    }
}