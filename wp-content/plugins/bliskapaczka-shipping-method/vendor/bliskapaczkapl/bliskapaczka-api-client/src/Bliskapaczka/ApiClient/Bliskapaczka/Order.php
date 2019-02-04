<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\BliskapaczkaInterface;
use Bliskapaczka\ApiClient\AbstractBliskapaczka;
use Bliskapaczka\ApiClient\Exception;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 */
class Order extends AbstractBliskapaczka implements BliskapaczkaInterface
{
    const REQUEST_URL = 'order';

    protected $orderId = null;

    /**
     * Set order id
     *
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Return valid URL for API call order actions
     *
     * @return string
     */
    public function getUrl()
    {
        $url = static::REQUEST_URL;

        if (isset($this->orderId)) {
            $url .= '/' . $this->orderId;
        }

        return $url;
    }

    /**
     * Call API method create order
     *
     * @param array $data
     * @return json $response
     */
    public function create(array $data)
    {
        $this->validate($data);

        $response = $this->doCall($this->getUrl(), json_encode($data), array(), 'POST');

        return $response;
    }

    /**
     * Call API method get order
     *
     * @return json $response
     */
    public function get()
    {
        if (!isset($this->orderId) || empty($this->orderId)) {
            throw new  Exception('Please set valid order ID', 1);
        }

        $response = $this->doCall($this->getUrl(), json_encode(''), array(), 'GET');

        return $response;
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return bool
     */
    public function validate(array $data)
    {
        $validator = $this->getValidator();
        $validator->setData($data);
        $validator->validate();
    }
}
