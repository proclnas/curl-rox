<?php


class CurlRoxTest extends \PHPUnit_Framework_TestCase {

    public function testExtensionsLoaded()
    {
        $this->assertTrue(extension_loaded('curl'));
    }

    public function testGetAndPostRequest()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();
        $response = $curl->getHttpResponse();
        $this->assertNotNull($response);

        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->setPostPayload([
            'test' => '1',
            'foo' => 'bar'
        ]);
        $curl->postRequest();
        $response = $curl->getHttpResponse();

        $this->assertNotNull($response);
        $this->assertContains('foo', $response);
    }

    public function testCookieFile()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();

        $cookieFile = $curl->getCookieFile();
        $this->assertFileExists($cookieFile);
    }

    public function testDomIstanceOf()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php?test');
        $curl->getRequest();
        $curl->setCallback(function($http_response, \DiDom\Document $dom, \CurlRox\Curl $curl_rox){
            $elements = $dom->find('a');

            foreach ($elements as $element) {
                $this->assertInstanceOf('\Didom\Element', $element);
            }
        });
    }

    public function testJsonResponse()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->setPostPayload(['test' => '1', 'foo' => 'bar']);
        $curl->postRequest();
        $response = $curl->getHttpResponse(true);
        $this->assertArrayHasKey('foo', $response);
    }

    public function testDebugTo()
    {
        $fileName = 'debug.txt';
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->setPostPayload(['test' => '1', 'foo' => 'bar']);
        $curl->postRequest();
        $curl->debugTo($fileName);

        $this->assertFileExists($fileName);
        unlink($fileName);
    }

    public function testPublicGettersAndSetters()
    {
        $curl = new \CurlRox\Curl;
        $vars = array_keys(get_object_vars($curl));

        array_map(function($var) use ($curl) {
            $set = sprintf('set%s', ucfirst($var));
            $get = sprintf('get%s', ucfirst($var));

            $curl->$set(true);
            $this->assertInternalType('bool', $curl->$get());
        }, $vars);

        array_map(function($var) use ($curl) {
            $set = sprintf('set%s', ucfirst($var));
            $get = sprintf('get%s', ucfirst($var));

            $curl->$set('foo');
            $this->assertContains('foo', $curl->$get());
        }, $vars);
    }
}