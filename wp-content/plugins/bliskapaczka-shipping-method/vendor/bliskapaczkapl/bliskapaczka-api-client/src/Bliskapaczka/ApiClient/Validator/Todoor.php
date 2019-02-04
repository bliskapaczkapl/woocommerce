<?php

namespace Bliskapaczka\ApiClient\Validator;

use Bliskapaczka\ApiClient\AbstractValidator;
use Bliskapaczka\ApiClient\ValidatorInterface;
use Bliskapaczka\ApiClient\Exception;

/**
 * Todoor Mapper class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 */
class Todoor extends AbstractValidator implements ValidatorInterface
{
    protected $properties = [
        'senderEmail' => ['maxlength' => 60, 'notblank' => true],
        'receiverEmail' => ['maxlength' => 60],
        'senderPhoneNumber' => ['notblank' => true],
        'receiverPhoneNumber' => ['notblank' => true],
        'senderPostCode' => ['notblank' => true],
        'senderLastName' => ['maxlength' => 30, 'notblank' => true],
        'senderStreet' => ['maxlength' => 30, 'notblank' => true],
        'senderBuildingNumber' => ['maxlength' => 10, 'notblank' => true],
        'senderFlatNumber' => ['maxlength' => 10],
        'senderFirstName' => ['maxlength' => 30, 'notblank' => true],
        'senderCity' => ['maxlength' => 30, 'notblank' => true],
        'receiverFirstName' => ['maxlength' => 30, 'notblank' => true],
        'receiverLastName' => ['maxlength' => 30, 'notblank' => true],
        'receiverStreet' => ['maxlength' => 30, 'notblank' => true],
        'receiverBuildingNumber' => ['maxlength' => 10, 'notblank' => true],
        'receiverFlatNumber' => ['maxlength' => 10],
        'receiverPostCode' => ['notblank' => true],
        'receiverCity' => ['maxlength' => 30, 'notblank' => true],
        'deliveryType' => ['notblank' => true],
        'operatorName' => ['notblank' => true],
        'postingCode' => [],
        'codValue' => [],
        'codPayoutBankAccountNumber' => [],
        'additionalInformation' => [],
        'parcel' => []
    ];

    /**
     * Validate data
     */
    public function validate()
    {
        /* Original Bliskapaczka validator regexps
        numer konta: /^\d{26}$/
        nip: /^\d{10}$/
        kod pocztowy: /^\d{2}\-\d{3}$/

        @NotBlank
        @Size(max = 30)
        private String senderFirstName;
        @NotBlank
        @Size(max = 30)
        private String senderLastName;
        @NotEmpty
        @PolishPhoneNumber
        private String senderPhoneNumber;
        @NotBlank
        @Email
        @Size(max = 60)
        private String senderEmail;
        @NotBlank
        @Size(max = 30)
        private String senderStreet;
        @NotBlank
        @Size(max = 10)
        private String senderBuildingNumber;
        @Size(max = 10)
        private String senderFlatNumber;
        @NotBlank
        @PostCode
        private String senderPostCode;
        @NotBlank
        @Size(max = 30)
        private String senderCity;

        @NotBlank
        @Size(max = 30)
        private String receiverFirstName;
        @NotBlank
        @Size(max = 30)
        private String receiverLastName;
        @NotEmpty
        @PolishPhoneNumber
        private String receiverPhoneNumber;
        @NotBlank
        @Email
        @Size(max = 60)
        private String receiverEmail;
        @NotBlank
        private OperatorName operatorName;
        */

        # Basic validation for all propoerties
        $this->validationByProperty();
    }
}
