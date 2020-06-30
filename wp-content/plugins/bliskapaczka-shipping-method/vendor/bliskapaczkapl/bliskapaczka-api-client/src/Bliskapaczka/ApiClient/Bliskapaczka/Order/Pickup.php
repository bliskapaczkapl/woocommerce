<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order;

use Bliskapaczka\ApiClient\BliskapaczkaInterface;
use Bliskapaczka\ApiClient\AbstractBliskapaczka;

/**
 * Class Pickup
 *
 * @package Bliskapaczka\ApiClient\Bliskapaczka\Order
 * @author Paweł Karbowniczek <pkarbowniczek@divante.com>
 */
class Pickup extends AbstractBliskapaczka implements BliskapaczkaInterface
{
    const REQUEST_URL = 'order/pickup';

    /** @var string */
    private $orderId = null;

    /**
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validate(array $data)
    {
        return true;
    }

    /**
     * @param array $data
     *
     * @return json $response
     */
    public function create(array $data)
    {
        if (isset($this->orderId)) {
            $data['orderNumbers'] = $this->orderId;
        }

        return $this->doCall($this->getUrl(), json_encode($data), array(), 'POST');
    }
}
