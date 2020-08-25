<?php
namespace Bliskapaczka\ApiClient\Bliskapaczka\Order;

use Bliskapaczka\ApiClient\AbstractBliskapaczka;
use Bliskapaczka\ApiClient\BliskapaczkaInterface;

/**
 * Available Services API Client.
 *
 * @author marko
 */
class AvailableServices extends AbstractBliskapaczka implements BliskapaczkaInterface
{

    const REQUEST_URL = 'order/services';

    /**
     * Get avaible services
     *
     * @see https://api-docs.sandbox-bliskapaczka.pl/#_available_services
     *
     * @param array $data
     *          Data to sent for the endpoint
     * @param array $headers
     *          Extra headers to sent
     *
     * @return string Respone json body
     */
    public function get(array $data, array $headers = [])
    {
        $this->validate($data);
        return $this->doCall($this->getUrl(), \json_encode($data), $headers, 'POST');
    }

    /**
     *
     * {@inheritdoc}
     * @see \Bliskapaczka\ApiClient\BliskapaczkaInterface::validate()
     */
    public function validate(array $data)
    {
        $validator = $this->getValidator();
        $validator->setData($data);
        $validator->validate();
    }
}
