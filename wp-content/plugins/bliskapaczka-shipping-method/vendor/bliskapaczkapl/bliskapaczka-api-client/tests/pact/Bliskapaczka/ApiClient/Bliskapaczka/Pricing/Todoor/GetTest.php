<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Order\Pricing\Todoor;

use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
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

    public function testGetPricing()
    {
        $apiClient = new \Bliskapaczka\ApiClient\Bliskapaczka\Pricing('test-test-test-test');
        $apiClient->setApiUrl($this->host);

        $response = json_decode($apiClient->get($this->pricingData));

        $this->assertEquals('DPD', $response[0]->operatorName);
        $this->assertTrue($response[0]->availabilityStatus);
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
  "description": "Get pricing list for courier",
  "provider_state": "Pricing list for all",
  "request": {
    "method": "post",
    "path": "/v2/pricing"
  },
  "response": {
    "status": 200,
    "headers": {
      "Content-Type": "application/json"
    },
    "body": [
      {
        "operatorName" : "DPD",
        "availabilityStatus" : true,
        "price" : {
          "net" : 8.35,
          "vat" : 1.92,
          "gross" : 10.27
        },
        "unavailabilityReason" : null
      }
    ]
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
