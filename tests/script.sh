php -S 127.0.0.1:8000 -t tests/CurlRox/ &> /dev/null &
pid="${!}"
cd ..
ll;pwd
phpunit --configuration ../phpunit.xml --verbose --color=always