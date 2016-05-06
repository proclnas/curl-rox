<?php

namespace CurlRox;

use DiDom\Document;


class Curl
{
    /**
     *  Add Referer auto to responses with Location
     *
     * @var bool
     */
    public $auto_referer;

    /**
     * Uri to be reached.
     *
     * @var string
     */
    private $uri;

    /**
     * Context User-Agent
     *
     * @var string
     */
    private $user_agent;

    /**
     * Cookie file
     *
     * @var string
     */
    private $cookie_file;

    /**
     * Post requests payload
     *
     * @var array
     */
    private $post_payload;

    /**
     * Raw response
     *
     * @var string
     */
    private $http_response;

    /**
     * Last request http info
     *
     * @var array
     */
    private $http_info;

    /**
     * Http headers
     *
     * @var array
     */
    private $http_headers;

    /**
     * Default curl opts
     *
     * @var array
     */
    private $opts;

    /**
     * Request flag, return if the wcrawler did a request.
     *
     * @var bool
     */
    private $requested;

    /**
     * Accept encoding
     *
     * @var string
     */
    private $encoding;

    /**
     * Change the "Show result" behaviour
     *
     * @var bool
     */
    private $raw;

    /**
     * Follow redirects
     *
     * @var bool
     */
    private $follow_location;

    /**
     * Check ssl with ca cert
     *
     * @var
     */
    private $check_ssl;

    /**
     * Context timeout
     *
     * @var int
     */
    private $timeout;

    /**
     * CA certificate (SSL)
     *
     * @var string
     */
    private $ca_cert;

    const HTTP_CODE_OK             = 200;
    const HTTP_CODE_REDIRECT       = 302;
    const HTTP_CODE_NOT_FOUND      = 404;
    const HTTP_CODE_INTERNAL_ERROR = 502;

    /**
     * WebCrawler constructor.
     *
     * @param string $uri
     * @throws \Exception
     */
    public function __construct($uri = null)
    {
        if (!extension_loaded('curl'))
            throw new \Exception(
                'Extension php_curl not loaded'
            );

        $this->uri             = $uri;
        $this->cookie_file     = tempnam(sys_get_temp_dir(), 'Curl');
        $this->user_agent      = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0)';
        $this->timeout         = 30;
        $this->follow_location = true;
        $this->auto_referer    = true;
        $this->check_ssl       = false;
        $this->http_headers    = [];
        $this->raw             = true;
    }

    /**
     * Remove cookie file from local system
     */
    public function __destruct()
    {
        if (file_exists($this->cookie_file))
            unlink($this->cookie_file);
    }

    /**
     * Add target to the web crawler
     *
     * @param string $uri
     * @return Curl
     */
    public function Uri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Return current uri
     *
     * @throws \Exception
     * @return string
     */
    public function getUri()
    {
        if (is_null($this->uri))
            throw new \Exception(
                'Uri not setted yet'
            );

        return $this->uri;
    }

    /**
     * Change cookie file name
     *
     * @param string $cookie_file
     * @return Curl
     */
    public function setCookieFile($cookie_file)
    {
        $this->cookie_file = $cookie_file;
        return $this;
    }

    /**
     * Get current cookie file
     *
     * @return string
     */
    public function getCookieFile()
    {
        return $this->cookie_file;
    }

    /**
     * User agent
     *
     * @param string $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->user_agent = $user_agent;
    }

    /**
     * Get current user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    public function raw($raw = true)
    {
        $this->raw = $raw;
        return $this;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Disable redirects
     *
     * @param $follow_location
     * @return Curl
     */
    public function setFollowLocation($follow_location)
    {
        $this->follow_location = $follow_location;
        return $this;
    }

    /**
     * Get follow redirects behaviour
     *
     * @return bool
     */
    public function getFollowLocation()
    {
        return $this->follow_location;
    }

    /**
     * Set context timeout
     *
     * @param $timeout
     * @return Curl
     * @throws \Exception
     */
    public function timeout($timeout)
    {
        if (!is_int($timeout))
            throw new \Exception(
                'Timeout method accept only int values'
            );

        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Get timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Accept encoding setting
     *
     * @param $encoding
     * @return Curl
     */
    public function encoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Http headers to be sent
     *
     * @param $http_headers
     * @return Curl
     */
    public function httpHeaders($http_headers)
    {
        $this->http_headers = $http_headers;
        return $this;
    }

    /**
     * Get http headers
     *
     * @return mixed
     */
    public function getHttpHeaders()
    {
        return $this->http_headers;
    }

    /**
     * Set referer automatically to responses with Location header
     *
     * @param $auto_referer
     * @return Curl;
     */
    public function setAutoReferer($auto_referer)
    {
        $this->auto_referer = $auto_referer;
        return $this;
    }

    /**
     * Get auto-referer setting
     *
     * @return bool
     */
    public function getAutoReferer()
    {
        return $this->auto_referer;
    }

    /**
     * Set check ssl and ca cert
     *
     * @param $ca_cert
     * @return Curl
     * @throws \Exception
     */
    public function checkSsl($ca_cert)
    {
        if (!file_exists($ca_cert))
            throw new \Exception(
                sprintf('Cert %s not found', $ca_cert)
            );

        $this->ca_cert   = $ca_cert;
        $this->check_ssl = true;

        return $this;
    }

    public function getCaCert()
    {
        return $this->ca_cert;
    }

    public function getCheckSsl()
    {
        return $this->check_ssl;
    }

    private function prepareOpts($post = false)
    {
        $timeout     = $this->getTimeout();
        $cookie_file = $this->getCookieFile();

        $this->opts = [
            CURLOPT_RETURNTRANSFER => $this->getRaw(),
            CURLOPT_FOLLOWLOCATION => $this->getFollowLocation(),
            CURLOPT_USERAGENT      => $this->getUserAgent(),
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_COOKIEJAR      => $cookie_file,
            CURLOPT_COOKIEFILE     => $cookie_file,
            CURLOPT_ENCODING       => $this->getEncoding(),
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

        if ($post !== false) {
            $this->opts[CURLOPT_POST]       = true;
            $this->opts[CURLOPT_POSTFIELDS] = $this->getPostPayload();
        }

        return $this->opts;
    }

    /**
     * Post payload to post requests
     *
     * @param array $post_payload
     * @return Curl
     * @throws \Exception
     */
    public function setPostPayload($post_payload)
    {
        $this->post_payload = $post_payload;

        if (is_array($post_payload))
            $this->post_payload = http_build_query($post_payload);

        return $this;
    }

    /**
     * Get current post payload
     *
     * @return string
     */
    public function getPostPayload()
    {
        if (!isset($this->post_payload))
            return 'post payload not setted yet.' . PHP_EOL;

        return $this->post_payload;
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

        $this->http_response = curl_exec($ch);
        $this->http_info     = curl_getinfo($ch);
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

        $this->http_response = curl_exec($ch);
        $this->http_info     = curl_getinfo($ch);
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
            if (array_key_exists($key, $this->http_info))
                return $this->http_info[$key];
        }

        return $this->http_info;
    }

    /**
     * Return last raw response, optional json args acceptable.
     *
     * @param bool $json_decode
     * @return array|string
     */
    public function getHttpResponse($json_decode = false)
    {
        return ($json_decode !== false) ? json_decode($this->http_response, true) : $this->http_response;
    }

    /**
     * Verify the success of the request based on the last code response
     *
     * @return bool
     */
    public function ok()
    {
        $ok = false;

        if ($this->getHttpInfo('http_code') === self::HTTP_CODE_OK)
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

        $http_response = $this->getHttpResponse();

        $didom = new Document;
        $dom   = $didom->loadHtml($http_response);

        call_user_func_array($callback, [$http_response, $dom, $this]);

        return $this;
    }

    /**
     * Write results of $this->http_response
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
}