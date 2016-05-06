# Curl Rox: Just another curl wrapper for webCrawling purposes

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/proclnas/curl-rox/blob/master/LICENSE)

PHP Curl Rox Class is an object-oriented wrapper of the PHP cURL extension targeting webCrawling or similar tasks like api consuming.

---

- [Installation](#installation)
- [Requirements](#requirements)
- [How to use](#how-to-use)
- [Methods](#methods)
- [Contribute](#contribute)
- [License] (#license)

---

### Installation

To install PHP Curl Rox, Just:

    $ composer require proclnas/curl-rox
    
### Requirements

- PHP: 5.5, 5.6, 7
- php-curl

### How to use

#### GET Request

```php
require '/vendor/autoload.php'

try {
    
    $webCrawler = new \CurlRox\Curl;
    $r = $webCrawler->Uri('http://httpbin.org/get')
                    ->getRequest()
                    ->getHttpResponse();
                    
    echo $r;
catch (Exception $e) {
    echo $e->getMessage();
}
```

#### POST Request

```php

try {
    $webCrawler = new \CurlRox\Curl;
    $r = $webCrawler->Uri('http://httpbin.org/post')->postRequest(
            ['name' => 'Proclnas', 'Language' => 'PHP']
         )->getHttpResponse();
                    
    echo $r;
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### JSON parsing

Passing true to Curl::getHttpResponse automatically calls a json_decode($http_response, true) to the response

```php

try {
    
    $webCrawler = new \CurlRox\Curl;
    $r = $webCrawler->Uri('http://client-fake.org/api/clients/get')
                    ->httpHeaders([
                        'X-Requested-With', 'XMLHttpRequest'   
                    ])->getRequest()
                    ->getHttpResponse(true);
    
    var_dump($r);
catch (Exception $e) {
    echo $e->getMessage();
}
```

#### Parsing with dom (scraping all links in the page)

CurlRox response extends [\DiDom\Document::loadHtml](https://github.com/Imangazaliev/DiDOM), allowing easy interaction with dom
using the (\DiDom\Document $dom) parameter.

DiDom allows to interact with HTML in several ways, see here to more info: [Didom Repo](https://github.com/Imangazaliev/DiDOM)

```php

try {
    $webCrawler = new \CurlRox\Curl;
    $webCrawler->Uri('http://google.com')
               ->getRequest();
               
    $webCrawler->setCallback(function($http_response, \DiDom\Document $dom, \CurlRox\Curl $curl_rox){
        $elements = $dom->find('a');
        
        foreach ($elements as $element)
            echo 'Link found: ', $element->attr('href'), PHP_EOL;
    });
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### Output

```
Link found: https://mail.google.com/mail/?tab=wm
Link found: https://www.google.com.br/imghp?hl=pt-BR&tab=wi&ei=jfkrV-q1CYS2wAS8oYH4CQ&ved=0EKouCBQoAQ
Link found: https://www.google.com.br/intl/pt-BR/options/
Link found: https://accounts.google.com/ServiceLogin?hl=pt-BR&passive=true&continue=https://www.google.com.br/%3Fgfe_rd%3Dcr%26ei%3DiPkrV-6uJo2dwQSsmKu4Dw%26gws_rd%3Dssl
Link found: javascript:void(0)
Link found: https://www.google.com/url?q=https://support.google.com/websearch/answer/463%3Futm_source%3Dgoogle.com%26utm_medium%3Dcallout%26utm_campaign%3DFFDHP&source=hpp&id=5082245&ct=7&usg=AFQjCNFBUGlUSE08cHgcuB_OXZHigRGNAw
Link found: https://www.google.com.br/webhp?hl=pt-BR
Link found: //support.google.com/websearch/answer/186645?hl=pt
Link found: //www.google.com.br/intl/pt-BR/policies/privacy/?fg=1
Link found: //www.google.com.br/intl/pt-BR/policies/terms/?fg=1
Link found: https://www.google.com.br/preferences?hl=pt-BR
Link found: https://www.google.com.br/preferences?hl=pt&fg=1
Link found: /advanced_search?hl=pt&fg=1
``` 

---

#### Check for 200' http code response (ok response)

```php

try {
    
    $webCrawler = new \CurlRox\Curl;
    $r = $webCrawler->Uri('http://fake-links.org/')
                    ->getRequest();
                    
$webCrawler->setCallback(function($http_response, \DiDom\Document $dom, \CurlRox\Curl $curl_rox){
        
        // Check http code
        if (!$pwc->ok())
            exit (
                sprintf('Error reaching %s, http_code: %s' . PHP_EOL, $pwc->getUri(), $pwc->getHttpInfo('http_code'))
            );

        $elements = $dom->find('a');
        
        foreach ($elements as $element)
            echo 'Link found: ', $element->attr('href'), PHP_EOL;
    });
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### Save response to file (Debug reasons)

```php

try {
    
    $webCrawler = new \CurlRox\Curl;
    $r = $webCrawler->Uri('http://fake-links.org/')
                    ->getRequest()
                    ->debugTo('/tmp/review.html');

} catch (Exception $e) {
    echo $e->getMessage();
}
```

### Methods

```php
Curl::__construct($uri = null)
Curl::__destruct()
Curl::Uri($uri)
Curl::getUri()
Curl::setCookieFile($cookie_file);
Curl::getCookieFile()
Curl::setUserAgent($user_agent)
Curl::getUserAgent()
Curl::raw($raw = true)
Curl::getRaw()
Curl::setFollowLocation($follow_location)
Curl::getFollowLocation()
Curl::timeout($timeout)
Curl::getTimeout()
Curl::encoding($encoding)
Curl::getEncoding()
Curl::httpHeaders($http_headers)
Curl::getHttpHeaders()
Curl::setAutoReferer($bool)
Curl::getAutoReferer()
Curl::checkSsl($bool)
Curl::getCaCert()
Curl::getCheckSsl()
Curl::setPostPayload($post_payload)
Curl::getPostPayload()
Curl::getRequest()
Curl::postRequest()
Curl::getHttpInfo($http_info_key)
Curl::getHttpResponse($bool_json_decode)
Curl::ok()
Curl::getLastHttpCode()
Curl::setCallback($callable)
Curl::debugTo($file)
```

## TODO:

 - Add cases to PHPUnit test
 - Better validation on setters
 - Add CurlMulti support

Feel free to fork and pull request to help the project! ;)

### License

```
The MIT License (MIT)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```