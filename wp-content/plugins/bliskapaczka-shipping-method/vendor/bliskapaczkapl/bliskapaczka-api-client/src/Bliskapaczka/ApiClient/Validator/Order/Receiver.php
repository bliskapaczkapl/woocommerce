<?php

namespace Bliskapaczka\ApiClient\Validator\Order;

use Bliskapaczka\ApiClient\Validator\Order;
use Bliskapaczka\ApiClient\AbstractValidator;
use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Exception;

/**
 * Order Validator class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 */
class Receiver extends Order implements ValidatorInterface
{
    protected $properties = [
        'receiverEmail' => ['maxlength' => 60],
        'receiverPhoneNumber' => ['notblank' => true],
        'receiverFirstName' => ['maxlength' => 30, 'notblank' => true],
        'receiverLastName' => ['maxlength' => 30, 'notblank' => true]
    ];
}
