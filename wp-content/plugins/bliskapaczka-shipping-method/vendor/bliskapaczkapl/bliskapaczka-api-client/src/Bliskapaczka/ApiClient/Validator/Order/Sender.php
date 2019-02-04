<?php

namespace Bliskapaczka\ApiClient\Validator\Order;

use Bliskapaczka\ApiClient\Validator\Order;
use Bliskapaczka\ApiClient\AbstractValidator;
use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Exception;

/**
 * Sender Data Validator class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Sender extends Order implements ValidatorInterface
{
    protected $properties = [
        'senderEmail' => ['maxlength' => 60],
        'senderPhoneNumber' => [],
        'senderPostCode' => [],
        'senderFirstName' => ['maxlength' => 30],
        'senderLastName' => ['maxlength' => 30],
        'senderStreet' => ['maxlength' => 30],
        'senderBuildingNumber' => ['maxlength' => 10],
        'senderFlatNumber' => ['maxlength' => 10],
        'senderCity' => ['maxlength' => 30],
        'codPayoutBankAccountNumber' => [],
    ];
}
