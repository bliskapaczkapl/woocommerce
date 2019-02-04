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
class Pos extends AbstractBliskapaczka implements BliskapaczkaInterface
{
    /**
     * @const URL for the api
     */
    const API_URL = 'https://pos.bliskapaczka.pl/api/';

    const API_VERSION = 'v1';

    /**
     * @const URL for the sandbox api
     */
    const SANDBOX_API_URL = 'https://pos.sandbox-bliskapaczka.pl/api/';

    const REQUEST_URL = 'pos';

    protected $operator = null;
    protected $pointCode = null;

    /**
     * Set operator
     *
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * Set point code
     *
     * @param string $pointCode
     */
    public function setPointCode($pointCode)
    {
        $this->pointCode = $pointCode;
    }

    /**
     * Return valid URL for API call order actions
     *
     * @return string
     */
    public function getUrl()
    {
        if ((!isset($this->operator) || empty($this->operator)) ||
            (!isset($this->pointCode) || empty($this->pointCode))
        ) {
            throw new Exception('Please set valid operator name or valid point code', 1);
        }

        $url = self::REQUEST_URL;

        $url .= '/' . $this->operator . '/' . $this->pointCode;

        return $url;
    }

    /**
     * Call API method create order
     *
     * @param array $data
     * @return json $response
     */
    public function get()
    {
        $response = $this->doCall($this->getUrl(), json_encode(''), array(), 'GET');

        return $response;
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
