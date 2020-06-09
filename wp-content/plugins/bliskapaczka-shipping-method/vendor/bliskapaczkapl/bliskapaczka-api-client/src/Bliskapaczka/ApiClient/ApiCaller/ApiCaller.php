<?php

namespace Bliskapaczka\ApiClient\ApiCaller;

use Bliskapaczka\ApiClient\Logger;

/**
 * Class ApiCaller
 *
 * @package            Bliskapaczka\ApiClient\ApiCaller
 * @codeCoverageIgnore That makes a HTTP request with the bpost API
 */
class ApiCaller
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * ApiCaller constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->logger = new \WC_Logger();
    }

    /**
     * @param array $options
     * @return bool
     */
    public function doCall(array $options)
    {
        $this->logger->debug(json_encode($options));
        $curl = curl_init();

        $options[CURLOPT_RETURNTRANSFER] = 1;

        curl_setopt_array($curl, $options);

        $this->logger->debug(json_encode($options));

        $error = curl_error($curl);
        if ($error) {
            $this->logger->error($error);
            throw new \Exception($error, 1);
        }

        $response = curl_exec($curl);
        $this->logger->debug($response);

        $responseDecoded = json_decode($response);

        if (isset($responseDecoded->error)) {
            $logger = new \WC_Logger();
            $logger->info('aaatest');
            $logger->info($response);
            $this->logger->error($error);
            throw new \Exception($responseDecoded->error, 1);
        }

        if (isset($responseDecoded->errors)) {
            foreach ($responseDecoded->errors as $error) {
                $this->logger->error($error);
                throw new \Exception($error->message . ' ' . $error->field, 1);
            }
        }

        return $response;
    }
}
