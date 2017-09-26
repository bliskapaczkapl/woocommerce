<?php

namespace Bliskapaczka\ApiClient;

use Psr\Log\LoggerInterface;
use Bliskapaczka\ApiClient\ApiCaller\ApiCaller;
use Bliskapaczka\ApiClient\Mappers\Order;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Bliskapaczka
{
    /**
     * @const Bliska paczka api version
     */
    const API_VERSION = 'v1';

    /**
     * @const URL for the api
     */
    const API_URL = 'https://api.bliskapaczka.pl';

    /**
     * @const URL for the sandbox api
     */
    const SANDBOX_API_URL = 'https://api.sandbox-bliskapaczka.pl';

    /**
 * @var ApiCaller
*/
    private $apiCaller;

    /**
     * Create Bliskapaczka instance
     *
     * @param string $bearer
     * @param string $mode
     */
    public function __construct($bearer, $mode = 'prod')
    {
        $this->bearer = (string)$bearer;
        $this->setApiUrl((string)$this->getApiUrlForMode($mode));
        $this->logger = new Logger();
    }

    /**
     * Get API Caller
     *
     * @return ApiCaller
     */
    public function getApiCaller()
    {
        if ($this->apiCaller === null) {
            $this->apiCaller = new ApiCaller($this->logger);
        }

        return $this->apiCaller;
    }

    /**
     * Get API url for mode
     *
     * @param string $mode
     * @return string
     */
    public function getApiUrlForMode($mode)
    {
        $url = '';

        switch ($mode) {
            case 'test':
                $url = self::SANDBOX_API_URL;
                break;

            case 'prod':
                $url = self::API_URL;
                break;
        }

        return $url;
    }

    /**
     * Return API url
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * In some cases we need other API url
     *
     * @param string $url
     */
    public function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }

    /**
     * Create cURL configuration and call
     *
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param string $method
     * @param bool $expectXML
     */
    private function doCall($url, $body = '', $headers = array(), $method = 'GET', $expectXML = true)
    {
        // build Authorization header
        $headers[] = 'Authorization: Bearer ' . $this->bearer;
        $headers[] = 'Content-Type: application/json';
        
        // set options
        $options[CURLOPT_URL] = $this->apiUrl . '/v1/' . $url;
        $options[CURLOPT_TIMEOUT] = 1;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;
        
        if ($method == 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        $response = $this->getApiCaller()->doCall($options);

        return $response;
    }

    /**
     * Call API method create order
     *
     * @param array $data
     */
    public function createOrder(array $data)
    {
        $url = 'order';

        $order = Order::createFromArray($data);
        $order->validate();

        $response = $this->doCall($url, json_encode($data), array(), 'POST');

        return $response;
    }

    /**
     * Call API method create order
     *
     * @param array $data
     */
    public function getPricing(array $data)
    {
        $url = 'pricing';

        $response = $this->doCall($url, json_encode($data), array(), 'POST');

        return $response;
    }
}
