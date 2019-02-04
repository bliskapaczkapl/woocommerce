<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Todoor;

use Bliskapaczka\ApiClient\Bliskapaczka\Order\Advice as OrderAdvice;
use Bliskapaczka\ApiClient\BliskapaczkaInterface;
use Bliskapaczka\ApiClient\AbstractBliskapaczka;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Advice extends OrderAdvice implements BliskapaczkaInterface
{
    /**
     * Return end of url for request
     */
    public function getUrl()
    {
        return static::REQUEST_URL;
    }

    /**
     * Call API method create order
     *
     * @param array $data
     * @return json $response
     */
    public function create(array $data)
    {
        if (isset($this->orderId)) {
            $data['number'] = $this->orderId;
        }

        return parent::create($data);
    }
}
