<?php

use PHPUnit\Framework\TestCase;

class CurlRoxTest extends TestCase {

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function objectCanBeInstantiated()
    {
        $obj = new \CurlRox\Curl;
        return $obj;
    }

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function extensionsLoaded()
    {
        $this->assertTrue(extension_loaded('curl'));
    }

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function getAndPostRequest()
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
        $this->assertStringContainsString('foo', $response);
    }

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function cookieFileCanBeCreated()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();

        $cookieFile = $curl->getCookieFile();
        $this->assertFileExists($cookieFile);
    }

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function domCanBeReached()
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

    /**
     * @test
     * @objectCanBeInstantiated
     */
    public function jsonResponse()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->setPostPayload(['test' => '1', 'foo' => 'bar']);
        $curl->postRequest();
        $response = $curl->getHttpResponse(true);
        $this->assertArrayHasKey('foo', $response);
    }

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function debugCanBeDone()
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

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function publicGettersAndSetters()
    {
        $curl = new \CurlRox\Curl;
        $vars = array_keys(get_object_vars($curl));

        array_map(function($var) use ($curl) {
            $set = sprintf('set%s', ucfirst($var));
            $get = sprintf('get%s', ucfirst($var));

            $curl->$set(true);
            $this->assertIsBool($curl->$get());
        }, $vars);

        array_map(function($var) use ($curl) {
            $set = sprintf('set%s', ucfirst($var));
            $get = sprintf('get%s', ucfirst($var));

            $curl->$set('foo');
            $this->assertStringContainsString('foo', $curl->$get());
        }, $vars);
    }

    /**
     * @test
     * @depends objectCanBeInstantiated
     */
    public function sslShouldFailWithInexistentCaCert()
    {
        $this->expectException(\Exception::class);
        $curl = new \CurlRox\Curl;
        $curl->checkSsl('not-exists');
    }
}