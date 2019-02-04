<?php

namespace Bliskapaczka\ApiClient\Validator\Order\Advice;

use Bliskapaczka\ApiClient\Validator\Order\Advice;
use Bliskapaczka\ApiClient\AbstractValidator;
use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Exception;

/**
 * Sender Data Validator class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Sender extends Advice implements ValidatorInterface
{
    protected $properties = [
        'senderEmail' => ['maxlength' => 60, 'notblank' => true],
        'senderPhoneNumber' => ['notblank' => true],
        'senderPostCode' => ['notblank' => true],
        'senderFirstName' => ['maxlength' => 30, 'notblank' => true],
        'senderLastName' => ['maxlength' => 30, 'notblank' => true],
        'senderStreet' => ['maxlength' => 30, 'notblank' => true],
        'senderBuildingNumber' => ['maxlength' => 10, 'notblank' => true],
        'senderFlatNumber' => ['maxlength' => 10],
        'senderCity' => ['maxlength' => 30, 'notblank' => true],
        'codPayoutBankAccountNumber' => [],
    ];
}
