<?php

use PHPUnit\Framework\TestCase;
use \org\bovigo\vfs\vfsStream;

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
    public function sslShouldFailWithInexistentCaCert()
    {
        $this->expectException(\Exception::class);
        $curl = new \CurlRox\Curl;
        $curl->checkSsl('not-exists');
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function getSpecificHttpInfoKey()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();
        $this->assertNotNull($curl->getHttpInfo('url'));
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function httpOkInASimpleRequest()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();
        $this->assertTrue($curl->ok());
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function throwExceptionTryingGetLastHttpCodeWithoutRequest()
    {
        $this->expectException(\Exception::class);
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getLastHttpCode();
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function retrieveCompleteHttpInfo()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();
        $this->assertNotNull($curl->getHttpInfo());
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function getLastHttpCodeAfterRequest()
    {
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();
        $this->assertNotNull($curl->getLastHttpCode());
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function throwExceptionPassingNoCallableInSetCallback()
    {
        $this->expectException(\Exception::class);
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->getRequest();
        $curl->setCallback(1337);
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function configureCaCertFile()
    {
        vfsStream::setup('tmpDir', null, ['data' => 'foobar']);
        $fakeCaCert = 'vfs://tmpDir/data';
        $curl = new \CurlRox\Curl;
        $curl->setUri('http://127.0.0.1:8000/server.php');
        $curl->checkSsl($fakeCaCert);
        $curl->getRequest();
        $this->assertTrue($curl->getCheckSsl());
    }

    /**
     * @test 
     * @depends objectCanBeInstantiated
     */
    public function existentGetAndSetterShouldBeCalledByMagicCall()
    {
        $curl = new \CurlRox\Curl;
        $curl->setHttpHeaders(['foo' => 'bar', 'ok' => 'ok']);
        $this->assertNotNull($curl->getHttpHeaders());
    }
}