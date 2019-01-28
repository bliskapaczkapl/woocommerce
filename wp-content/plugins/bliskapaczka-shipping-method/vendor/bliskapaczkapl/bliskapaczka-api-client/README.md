[![Build Status](https://travis-ci.org/bliskapaczkapl/bliskapaczka-api-client.svg?branch=master)](https://travis-ci.org/bliskapaczkapl/bliskapaczka-api-client)

# BliskaPaczka API Client

This package is PHP API client for Bliskapaczka.pl API.

## Features

Client has support for API actions:
- order
  - save
  - get waybill
- pricing
  - get

For more information pleas see [Bliskapaczka API documentation](https://api-docs.bliskapaczka.pl)

## Usage

### Initialize client

```
$apiKey = 'xxxxx-xxxxx-xxxxx-xxxxx-xxxxx';
$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey, 'test');
```

### Create new order

```
$orderData = [
    "senderFirstName" => "string",
    "senderLastName" => "string",
    "senderPhoneNumber" => "606555433",
    "senderEmail" => "bob@example.com",
    "senderStreet" => "string",
    "senderBuildingNumber" => "string",
    "senderFlatNumber" => "string",
    "senderPostCode" => "54-130",
    "senderCity" => "string",
    "receiverFirstName" => "string",
    "receiverLastName" => "string",
    "receiverPhoneNumber" => "600555432",
    "receiverEmail" => "eva@example.com",
    "operatorName" => "INPOST",
    "destinationCode" => "KRA010",
    "postingCode" => "KOS01L",
    "codValue" => 0,
    "insuranceValue" => 0,
    "additionalInformation" => "string",
    "parcel" => [
        "dimensions" => [
            "height" => 20,
            "length" => 20,
            "width" => 20,
            "weight" => 2
        ]
    ]
];
var_dump($apiClient->create($orderData));
```

## Developing

### How to run unit tests?
```
php vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/unit
```

### How to run SCA?
```
php vendor/bin/phpcs --standard=PSR2 src/ tests/
php vendor/bin/phpmd src/ text codesize
php vendor/bin/phpcpd src/
php vendor/bin/phpdoccheck --directory=src/ 
php vendor/bin/phploc src/
```

### How to run API tests as a Client?
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