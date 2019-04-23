#!/bin/bash

php -S 127.0.0.1:8000 -t tests/CurlRox/ &> /dev/null &
phpunit --configuration "./../phpunit.xml" --testdox ../tests --verbose --color=always
