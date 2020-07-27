<?php

namespace Bliskapaczka\ApiClient;

use Psr\Log\LoggerInterface;
use Bliskapaczka\ApiClient\ApiCaller\ApiCaller;
use Bliskapaczka\ApiClient\Mappers\Order;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 */
abstract class AbstractBliskapaczka
{
    /**
     * @const Bliska paczka api version
     */
    const API_VERSION = 'v2';

    /**
     * @const URL for the api
     */
    const API_URL = 'https://api.bliskapaczka.pl';

    /**
     * @const URL for the sandbox api
     */
    const SANDBOX_API_URL = 'https://api.sandbox-bliskapaczka.pl';

    /**
     * Ending of url for specific request
     */
    const REQUEST_URL = '';

    /**
     * Timeout for API
     */
    const API_TIMEOUT = 2;

    /**
     * Timeout for API
     */
    const SANDBOX_API_TIMEOUT = 10;

    /**
     * @var ApiCaller
     */
    private $apiCaller;

    /** @var string */
    private $shopName = 'custome';

    /** @var string  */
    private $shopVersion = 'undefined';

    /**
     * Create Bliskapaczka instance
     *
     * @param string $bearer
     * @param string $mode
     * @param LoggerInterface $logger
     */
    public function __construct($bearer, $mode = 'prod', LoggerInterface $logger = null)
    {
        if (!$bearer) {
            throw new Exception("Invalid api key", 1);
        }

        $this->bearer = (string)$bearer;
        $this->mode = (string)$mode;
        $this->setApiUrl((string)$this->getApiUrlForMode($mode));
        $this->logger = new Logger();
        try {
            $this->setShopNameAndVersionFromPath(getcwd());
        } catch (Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
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
    public function getMode()
    {
        return $this->mode;
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
     * Return end of url for request
     */
    public function getUrl()
    {
        return static::REQUEST_URL;
    }

    /**
     * Return timeout for APIs request
     */
    public function getApiTimeout()
    {
        $timeout = static::API_TIMEOUT;

        if ($this->mode == 'test') {
            $timeout = static::SANDBOX_API_TIMEOUT;
        }

        return $timeout;
    }

    /**
     * Get validator object for this instance
     *
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        // Bliskapaczka\ApiClient\Bliskapaczka\Order
        $className = get_class($this);
        $validatorName = str_replace('\Bliskapaczka', '\Validator', $className);

        if (!class_exists($validatorName)) {
            throw new Exception('Validator not exists', 1);
        }

        return new $validatorName;
    }

    /**
     * @param string $path
     * @return $this
     * @throws \Bliskapaczka\ApiClient\Exception
     */
    protected function setShopNameAndVersionFromPath($path)
    {
        if (strstr($path, 'wp-content')) {
            return $this
                ->setShopName('woocommerce')
                ->setShopVersion(GetterShopVersionFactory::getByShopName('Woocommerce'));
        }
        if (strstr($path, 'modules/bliskapaczka/vendor')) {
            return $this
                ->setShopName('prestashop')
                ->setShopVersion(GetterShopVersionFactory::getByShopName('PrestaShop'));
        }
        if (strstr('app/code/community', 'magento')) {
            return $this
                ->setShopName('magento')
                ->setShopVersion(GetterShopVersionFactory::getByShopName('Magento1'));
        }
        if (strstr($path, '/vendor/bliskapaczkapl')) {
            return $this
                ->setShopName('magento')
                ->setShopVersion(GetterShopVersionFactory::getByShopName('Magento2'));
        }
        return $this;
    }
    
    /**
     * Set shop engine name
     *
     * @param string $name Engine name ex. woocommerce,magento.
     *
     * @return \Bliskapaczka\ApiClient\AbstractBliskapaczka
     */
    public function setShopName($name)
    {
        $this->shopName = $name;
        return $this;
    }
    
    /**
     * Set shop engine version.
     *
     * @param string $version
     * @return \Bliskapaczka\ApiClient\AbstractBliskapaczka
     */
    public function setShopVersion($version)
    {
        $this->version = $version;
        return $this;
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
    protected function doCall($url, $body = '', $headers = array(), $method = 'GET', $expectXML = true)
    {
        // build Authorization header
        $headers[] = 'Authorization: Bearer ' . $this->bearer;
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Bp-Source: ' .  $this->shopName;
        $headers[] = 'Bp-Source-Version' . $this->shopVersion;

        // set options
        $options[CURLOPT_URL] = $this->apiUrl . '/' . static::API_VERSION . '/' . $url;
        $options[CURLOPT_TIMEOUT] = $this->getApiTimeout();
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        if ($method == 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        $response = $this->getApiCaller()->doCall($options);

        return $response;
    }
}
