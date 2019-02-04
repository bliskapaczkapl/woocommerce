<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\BliskapaczkaInterface;
use Bliskapaczka\ApiClient\AbstractBliskapaczka;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 */
class Pricing extends AbstractBliskapaczka implements BliskapaczkaInterface
{
    const REQUEST_URL = 'pricing';

    /**
     * Call API method create order
     *
     * @param array $data
     * @return json $response
     */
    public function get(array $data)
    {
        $response = $this->doCall($this->getUrl(), json_encode($data), array(), 'POST');

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
