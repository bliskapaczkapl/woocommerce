# BliskaPaczka Api Client

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/bliskapaczkapl/bliskapaczka-api-client.

### How to run unit tests 
```
php vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/unit
```

### How to run SCA
```
php vendor/bin/phpcs --standard=PSR2 src/ tests/
php vendor/bin/phpmd src/ text codesize
php vendor/bin/phpcpd src/
php vendor/bin/phpdoccheck --directory=src/ 
php vendor/bin/phploc src/
```

### How to run API tests as a Client
```
php vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/pact/
```

#### Setup Pact Mock

Via gem
```
gem install pact-mock_service
pact-mock-service --port 1234
```

or use docker
```
docker run -p 1234:1234 -v /tmp/log:/var/log/pacto -v /tmp/contracts:/opt/contracts madkom/pact-mock-service
```

### How to run unit tests on docker

```
docker build -t bliskapaczka_docker_php5 .
```

```
docker run -v $(pwd):/app --rm bliskapaczka_docker_php5 --bootstrap tests/bootstrap.php tests/unit
```