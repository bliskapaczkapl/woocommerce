<?php
namespace Bliskapaczka\ApiClient\Validator\Order;

use Bliskapaczka\ApiClient\Validator\Order;
use Bliskapaczka\ApiClient\ValidatorInterface;

/**
 * Validator for Available Services.
 *
 * @author marko
 */
class AvailableServices extends Order implements ValidatorInterface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        unset($this->properties['destinationCode']);
        $this->properties['serviceType'] = [];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Bliskapaczka\ApiClient\AbstractValidator::validationByProperty()
     */
    protected function validationByProperty()
    {
        foreach ($this->properties as $property => $settings) {
            if (! isset($this->data[$property])
                && isset($settings['notblank'])
                && $settings['notblank'] === true) {
                throw new \Exception($property . " is required", 1);
            }

            $this->notBlank($property, $settings);
            $this->maxLength($property, $settings);
            $this->specificValidation($property);
        }
    }

    /**
     * Verify allowe srvice type
     *
     * @param string $serviceType
     * @throws \Exception
     */
    protected function serviceType($serviceType)
    {
        if ('XPRESS' == trim(mb_strtoupper($this->data['operatorName']))) {
            $types = [
                'LOCAL',
                'COUNTRY',
                'INTERNATIONAL',
                'VOIVODESHIP',
                'ECOMMERCE'
            ];

            if (false === in_array($serviceType, $types)) {
                throw new \Exception(
                    'serviceType for operator XPRESS must be one from values: ' . implode(', ', $types),
                    1
                );
            }
        }
    }
}
