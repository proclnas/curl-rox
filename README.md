# Curl Rox: Just another curl wrapper for webCrawling purposes

[![Build Status](https://api.travis-ci.org/proclnas/curl-rox.svg?branch=master)](https://travis-ci.org/proclnas/curl-rox)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/proclnas/curl-rox/blob/master/LICENSE)
[![Packagist](https://img.shields.io/badge/packagist-install-brightgreen.svg)](https://packagist.org/packages/proclnas/curl-rox)

PHP Curl Rox Class is an object-oriented wrapper of the PHP cURL extension targeting webCrawling or similar tasks like api consuming.

---

- [Installation](#installation)
- [Requirements](#requirements)
- [How to use](#how-to-use)
- [Methods](#methods)
- [Todo](#todo)
- [License](#license)

---

### Installation

To install PHP Curl Rox, Just:

```bash
# clone the repository
git clone https://github.com/proclnas/curl-rox.git
cd curl-rox

# install dependencies
composer install

# or require via composer
composer require proclnas/curl-rox
```
    
### Requirements

- PHP: 5.6, 7
- php-curl

### How to use

#### GET Request

```php
require __DIR__ . '/vendor/autoload.php';

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
```

#### POST Request

```php

try {
    $curl = new Curl;
    $curl->setUri('http://httpbin.org/post');
    $curl->setPostPayload(['name' => 'Proclnas', 'Language' => 'PHP'])
    $curl->postRequest();
    $r = $curl->getHttpResponse();
                    
    echo $r;
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### JSON parsing

Passing true to Curl::getHttpResponse automatically calls a json_decode($http_response, true) to the response

```php

try {
    
    $curl = new Curl;
    $curl->setUri('http://httpbin.org/get');
    $curl->setHttpHeaders([
        'X-Requested-With', 'XMLHttpRequest'   
    ]);
    $curl->getRequest();
    $r = $curl->getHttpResponse(true);
    
    var_dump($r);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### Parsing with dom (scraping all links in the page)

CurlRox response extends [\DiDom\Document::loadHtml](https://github.com/Imangazaliev/DiDOM), allowing easy interaction with dom
using the (\DiDom\Document $dom) parameter.

DiDom allows to interact with HTML in several ways, see here to more info: [Didom Repo](https://github.com/Imangazaliev/DiDOM)

```php

try {
    $curl = new Curl;
    $curl->setUri('http://google.com');
    $curl->getRequest();
               
    $curl->setCallback(function($httpResponse, \DiDom\Document $dom, Curl $curlRox){
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
    
    $curl = new Curl;
    $curl->setUri('http://fake-links.org/');
    $curl->getRequest();
                    
    $curl->setCallback(function($http_response, \DiDom\Document $dom, Curl $curlRox){    
        // Check http code
        if (!$curlRox->ok())
            exit (
                sprintf('Error reaching %s, http_code: %s' . PHP_EOL, $curlRox->getUri(), $curlRox->getHttpInfo('http_code'))
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
    
    $curl = new Curl;
    $curl->setUri('http://fake-links.org/');
    $curl->getRequest();
    $curl->debugTo('/tmp/review.html');

} catch (Exception $e) {
    echo $e->getMessage();
}
```

### Methods

```php
Curl::__construct($uri = null)
Curl::__destruct()
Curl::setUri($uri)
Curl::getUri()
Curl::setCookieFile($cookie_file);
Curl::getCookieFile()
Curl::setUserAgent($user_agent)
Curl::getUserAgent()
Curl::setRaw($raw = true)
Curl::getRaw()
Curl::setFollowLocation($follow_location)
Curl::getFollowLocation()
Curl::setTimeout($timeout)
Curl::getTimeout()
Curl::encoding($encoding)
Curl::getEncoding()
Curl::setHttpHeaders($http_headers)
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

## Todo

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
