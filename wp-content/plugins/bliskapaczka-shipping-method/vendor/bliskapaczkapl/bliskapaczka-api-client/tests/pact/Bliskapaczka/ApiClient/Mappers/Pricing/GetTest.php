<?php

namespace Bliskapaczka;

use Bliskapaczka\ApiClient\Mappers\Pricing;
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

    public function testCreateOrder()
    {
        $apiClient = new ApiClient\Bliskapaczka('test-test-test-test');
        $apiClient->setApiUrl($this->host);

        $response = json_decode($apiClient->getPricing($this->pricingData));

        $this->assertEquals('INPOST', $response[0]->operatorName);
        $this->assertTrue($response[0]->availabilityStatus);
        $this->assertEquals('RUCH', $response[1]->operatorName);
        $this->assertTrue($response[1]->availabilityStatus);
        $this->assertEquals('POCZTA', $response[2]->operatorName);
        $this->assertTrue($response[2]->availabilityStatus);
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
  "description": "Get pricing list",
  "provider_state": "Pricing list for all",
  "request": {
    "method": "post",
    "path": "/v1/pricing"
  },
  "response": {
    "status": 200,
    "headers": {
      "Content-Type": "application/json"
    },
    "body": [
      {
        "operatorName" : "INPOST",
        "availabilityStatus" : true,
        "price" : {
          "net" : 8.35,
          "vat" : 1.92,
          "gross" : 10.27
        },
        "unavailabilityReason" : null
      },
      {
        "operatorName" : "RUCH",
        "availabilityStatus" : true,
        "price" : {
          "net" : 4.87,
          "vat" : 1.12,
          "gross" : 5.99
        },
        "unavailabilityReason" : null
      },
      {
        "operatorName" : "POCZTA",
        "availabilityStatus" : true,
        "price" : {
          "net" : 7.31,
          "vat" : 1.68,
          "gross" : 8.99
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
