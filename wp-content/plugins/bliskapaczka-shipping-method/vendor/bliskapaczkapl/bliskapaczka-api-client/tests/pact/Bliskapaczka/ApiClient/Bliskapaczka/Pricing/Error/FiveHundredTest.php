<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order\Pricing\Error;

use PHPUnit\Framework\TestCase;

class FiveHundredTest extends TestCase
{
    protected function setUp()
    {
        if (getenv('PACT_MOCK_SERVICE_URL')) {
            $this->host = 'madkom__pact-mock-service:1234';
        } else {
            $this->host = 'localhost:1234';
        }

        $this->pricingData = [
            "dimensions" => [
                "height" => 20,
                "length" => 20,
                "width" => 20,
                "weight" => 2
            ]
        ];

        $this->deleteInteractions();
        $this->setInteraction();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Internal Server Error
     */
    public function testGetPricing()
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing('test-test-test-test');
        $apiClient->setApiUrl($this->host);

        var_dump($apiClient->get($this->pricingData));
    }

    /**
     * Delete interactions
     */
    protected function deleteInteractions()
    {
        $curl = curl_init();

        // build Authorization header
        $headers[] = 'X-Pact-Mock-Service: true';
        
        // set options
        $options[CURLOPT_URL] = $this->host . '/interactions';
        $options[CURLOPT_TIMEOUT] = 60;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';

        curl_setopt_array($curl, $options);
        curl_exec($curl);
    }

    protected function setInteraction()
    {
        $curl = curl_init();

        // build Authorization header
        $headers[] = 'X-Pact-Mock-Service: true';
        $headers[] = 'Content-Type: application/json';
        
        // set options
        $options[CURLOPT_URL] = $this->host . '/interactions';
        $options[CURLOPT_TIMEOUT] = 60;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = '{
  "description": "Get pricing list for error 500",
  "provider_state": "API return 500 error",
  "request": {
    "method": "post",
    "path": "/v2/pricing"
  },
  "response": {
    "status": 200,
    "headers": {
      "Content-Type": "application/json"
    },
    "body": {
	  "timestamp": "2018-06-04T11:11:59.279+0000",
	  "status": 500,
	  "error": "Internal Server Error",
	  "exception": "java.lang.NullPointerException",
	  "message": "No message available",
	  "path": "/api//v1/pricing"
	}
  }
}';

        curl_setopt_array($curl, $options);
        curl_exec($curl);
    }

    /**
     * Delete interactions
     */
    protected function verification()
    {
        $curl = curl_init();

        // build Authorization header
        $headers[] = 'X-Pact-Mock-Service: true';
        
        // set options
        $options[CURLOPT_URL] = $this->host . '/interactions/verification';
        $options[CURLOPT_TIMEOUT] = 60;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($curl, $options);
        curl_exec($curl);
    }
}
