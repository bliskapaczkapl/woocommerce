<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order;

use Bliskapaczka\ApiClient\BliskapaczkaInterface;
use Bliskapaczka\ApiClient\AbstractBliskapaczka;
use Bliskapaczka\ApiClient\Exception;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Confirm extends AbstractBliskapaczka implements BliskapaczkaInterface
{
    const REQUEST_URL = 'orders/confirm';

    private $orderId = null;

    /**
     * Set operator name
     *
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * Return valid URL for API call for order Confirmation
     *
     * @return string
     */
    public function getUrl()
    {
        if (!isset($this->operator) || empty($this->operator)) {
            throw new  Exception('Please set valid operator name', 1);
        }

        $url = static::REQUEST_URL . '?operatorName=' . $this->operator;

        return $url;
    }

    /**
     * Call API method create order
     *
     * @param array $data
     * @return json $response
     */
    public function confirm()
    {
        $response = $this->doCall($this->getUrl(), json_encode(''), array(), 'POST');

        return $response;
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return true
     */
    public function validate(array $data)
    {
        return true;
    }
}
