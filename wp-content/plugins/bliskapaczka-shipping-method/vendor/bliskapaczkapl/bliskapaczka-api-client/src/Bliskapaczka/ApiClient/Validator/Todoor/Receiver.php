<?php

namespace Bliskapaczka\ApiClient\Validator\Todoor;

use Bliskapaczka\ApiClient\Validator\Todoor;
use Bliskapaczka\ApiClient\AbstractValidator;
use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Exception;

/**
 * Todoor Validator class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 */
class Receiver extends Todoor implements ValidatorInterface
{
    protected $properties = [
        'receiverEmail' => ['maxlength' => 60],
        'receiverPhoneNumber' => ['notblank' => true],
        'receiverFirstName' => ['maxlength' => 30, 'notblank' => true],
        'receiverLastName' => ['maxlength' => 30, 'notblank' => true],
        'receiverStreet' => ['maxlength' => 30, 'notblank' => true],
        'receiverBuildingNumber' => ['maxlength' => 10, 'notblank' => true],
        'receiverFlatNumber' => ['maxlength' => 10],
        'receiverPostCode' => ['notblank' => true],
        'receiverCity' => ['maxlength' => 30, 'notblank' => true]
    ];
}
