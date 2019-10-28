<?php

require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

// create a log channel
$logger = new MonologLogger('sendit');
$logger->pushHandler(new StreamHandler('/tmp/sendit.log'));

$apiKey = '999eac37-ba4d-4a00-b64c-14749dc835fa';
$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Order($apiKey, 'test', $logger);

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
    "deliveryType" => "P2P",
    "operatorName" => "INPOST",
    "destinationCode" => "KRA010",
    "postingCode" => "KRA011",
    "codValue" => null,
    "insuranceValue" => null,
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

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Order\Advice($apiKey, 'test');

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
    "deliveryType" => "P2P",
    "operatorName" => "INPOST",
    "destinationCode" => "KRA010",
    "postingCode" => "KRA011",
    "codValue" => null,
    "insuranceValue" => null,
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

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Pricing($apiKey, 'test', $logger);

$pricingData = [
    "parcel" => [
        "dimensions" => [
            "height" => 20,
            "length" => 20,
            "width" => 20,
            "weight" => 2
        ]
    ]
];
var_dump($apiClient->get($pricingData));

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Pricing($apiKey, 'test');

$pricingData = [
    "parcel" => [
        "dimensions" => [
            "height" => 20,
            "length" => 20,
            "width" => 20,
            "weight" => 2
        ]
    ],
    "deliveryType" => "P2P"
];
var_dump($apiClient->get($pricingData));

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Pricing($apiKey, 'test');

$pricingData = [
    "parcel" => [
        "dimensions" => [
            "height" => 20,
            "length" => 20,
            "width" => 20,
            "weight" => 2
        ]
    ],
    "deliveryType" => "P2D"
];
var_dump($apiClient->get($pricingData));

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Pricing($apiKey, 'test');

$pricingData = [
    "parcel" => [
        "dimensions" => [
            "height" => 20,
            "length" => 20,
            "width" => 20,
            "weight" => 2
        ]
    ],
    'codValue' => 1
];
var_dump($apiClient->get($pricingData));

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Pricing\Todoor($apiKey, 'test');

$pricingData = [
    "parcel" => [
        "dimensions" => [
            "height" => 20,
            "length" => 20,
            "width" => 20,
            "weight" => 2
        ]
    ]
];
var_dump($apiClient->get($pricingData));

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Pos($apiKey, 'test');
$apiClient->setOperator('INPOST');
$apiClient->setPointCode('GRU340');

var_dump($apiClient->get());

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Order\Waybill($apiKey, 'test');
$apiClient->setOrderId('000000636P-000000108');

var_dump($apiClient->get());

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Report($apiKey, 'test');
$apiClient->setOperator('ruch');

file_put_contents('zupa.pdf', $apiClient->get());

$apiClient = new Bliskapaczka\ApiClient\Bliskapaczka\Order\Confirm($apiKey, 'test');
$apiClient->setOperator('POCZTA');

var_dump($apiClient->confirm());