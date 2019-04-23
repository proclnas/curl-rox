<?php

namespace CurlRox;

use DiDom\Document;
use CurlRox\Core\OObject;
use CurlRox\Core\CurlEnum;

class Curl extends OObject
{
    /**
     *  Add Referer auto to responses with Location
     *
     * @var bool
     */
    public $autoReferer;

    /**
     * Uri to be reached.
     *
     * @var string
     */
    public $uri;

    /**
     * Context User-Agent
     *
     * @var string
     */
    public $userAgent;

    /**
     * Cookie file
     *
     * @var string
     */
    public $cookieFile;

    /**
     * Post requests payload
     *
     * @var array
     */
    public $postPayload;

    /**
     * Raw response
     *
     * @var string
     */
    public $httpResponse;

    /**
     * Last request http info
     *
     * @var array
     */
    public $httpInfo;

    /**
     * Http headers
     *
     * @var array
     */
    public $httpHeaders;

    /**
     * Request flag, return if the wcrawler did a request.
     *
     * @var bool
     */
    public $requested;

    /**
     * Context timeout
     *
     * @var int
     */
    public $timeout;

    /**
     * Change the "Show result" behaviour
     *
     * @var bool
     */
    public $raw;

    /**
     * Follow redirects
     *
     * @var bool
     */
    public $followLocation;

    /**
     * Check ssl with ca cert
     *
     * @var
     */
    public $checkSsl;

    /**
     * CA certificate (SSL)
     *
     * @var string
     */
    public $caCert;

    /**
     * Default curl opts
     *
     * @var array
     */
    private $opts;

    /**
     * Accept encoding
     *
     * @var string
     */
    private $encoding;

    /**
     * WebCrawler constructor.
     *
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        $this->cookieFile     = tempnam(sys_get_temp_dir(), 'Curl');
        $this->userAgent      = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0)';
        $this->timeout        = 30;
        $this->followLocation = true;
        $this->autoReferer    = true;
        $this->checkSsl       = false;
        $this->httpHeaders    = [];
        $this->raw            = true;
    }

    /**
     * Remove cookie file from local system
     */
    public function __destruct()
    {
        if (file_exists($this->cookieFile))
            unlink($this->cookieFile);
    }

    /**
     * Set check ssl and ca cert
     *
     * @param $caCert
     * @return Curl
     * @throws \Exception
     */
    public function checkSsl($caCert)
    {
        if (!file_exists($caCert))
            throw new \Exception(
                sprintf('Cert %s not found', $caCert)
            );

        $this->caCert   = $caCert;
        $this->checkSsl = true;

        return $this;
    }

    /**
     * Post payload to post requests
     *
     * @param array $postPayload
     * @return Curl
     * @throws \Exception
     */
    public function setPostPayload($postPayload)
    {
        $this->postPayload = $postPayload;

        if (is_array($postPayload))
            $this->postPayload = http_build_query($postPayload);

        return $this;
    }

    /**
     * Get request
     *
     * @throws \Exception
     * @return Curl
     */
    public function getRequest()
    {
        $ch   = curl_init($this->getUri());
        $opts = $this->prepareOpts();

        curl_setopt_array(
            $ch, $opts
        );

        $this->httpResponse = curl_exec($ch);
        $this->httpInfo     = curl_getinfo($ch);
        $this->requested     = true;

        if (curl_errno($ch)) throw new \Exception(curl_error($ch));

        curl_close($ch);

        return $this;
    }

    /**
     * Post request
     *
     * @throws \Exception
     * @return Curl
     */
    public function postRequest()
    {
        $ch   = curl_init($this->getUri());
        $opts = $this->prepareOpts(true);

        curl_setopt_array(
            $ch, $opts
        );

        $this->httpResponse = curl_exec($ch);
        $this->httpInfo     = curl_getinfo($ch);
        $this->requested     = true;

        if (curl_errno($ch)) throw new \Exception(curl_error($ch));

        curl_close($ch);

        return $this;
    }

    /**
     * Get info about last response of the scope
     *
     * @param string $key
     * @return array
     */
    public function getHttpInfo($key = null)
    {
        if (!is_null($key)) {
            if (array_key_exists($key, $this->httpInfo))
                return $this->httpInfo[$key];
        }

        return $this->httpInfo;
    }

    /**
     * Return last raw response, optional json args acceptable.
     *
     * @param bool $json_decode
     * @return array|string
     */
    public function getHttpResponse($json_decode = false)
    {
        return ($json_decode !== false) ? json_decode($this->httpResponse, true) : $this->httpResponse;
    }

    /**
     * Verify the success of the request based on the last code response
     *
     * @return bool
     */
    public function ok()
    {
        $ok = false;

        if ($this->getHttpInfo('http_code') === CurlEnum::HTTP_CODE_OK)
            $ok = true;

        return $ok;
    }

    /**
     * Get last http code
     *
     * @return string
     * @throws \Exception
     */
    public function getLastHttpCode()
    {
        if (!$this->requested)
            throw new \Exception('No requests done to get the last http code' . PHP_EOL);

        return $this->getHttpInfo('http_code');
    }

    /**
     * Request callback
     *
     * @param callable $callback
     * @throws \Exception
     * @return Curl
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback))
            throw new \Exception (
                sprintf ('Error: %s is not a valid callable', $callback)
            );

        $httpResponse = $this->getHttpResponse();

        $didom = new Document;
        $dom   = $didom->loadHtml($httpResponse);

        call_user_func_array($callback, [$httpResponse, $dom, $this]);

        return $this;
    }

    /**
     * Write results of $this->httpResponse
     *
     * @param string $file_name
     * @return Curl
     */
    public function debugTo($file_name)
    {
        file_put_contents (
            $file_name,
            $this->getHttpResponse(),
            LOCK_EX
        );

        echo sprintf(
            'Writed response to %s' . PHP_EOL, $file_name
        );

        return $this;
    }

    public function setHttpHeaders(array $headers)
    {
        $headers = array_map(function($key, $value){
            return sprintf('%s: %s', $key, $value);
        }, array_keys($headers), array_values($headers));

        $this->httpHeaders = $headers;
    }

    /**
     * Prepare opts to request
     *
     * @param boolean $isPost
     * @return array
     */
    private function prepareOpts($isPost = false)
    {
        $timeout    = $this->getTimeout();
        $cookieFile = $this->getCookieFile();

        $this->opts = [
            CURLOPT_RETURNTRANSFER => $this->getRaw(),
            CURLOPT_FOLLOWLOCATION => $this->getFollowLocation(),
            CURLOPT_USERAGENT      => $this->getUserAgent(),
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_AUTOREFERER    => $this->getAutoReferer(),
            CURLOPT_HTTPHEADER     => $this->getHttpHeaders(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];

        if ($this->getCheckSsl()) {
            $this->opts[CURLOPT_SSL_VERIFYPEER] = true;
            $this->opts[CURLOPT_SSL_VERIFYHOST] = 2;
            $this->opts[CURLOPT_CAINFO]         = $this->getCaCert();
        }

        if ($isPost !== false) {
            $this->opts[CURLOPT_POST]       = true;
            $this->opts[CURLOPT_POSTFIELDS] = $this->getPostPayload();
        }

        return $this->opts;
    }
}