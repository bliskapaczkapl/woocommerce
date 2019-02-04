<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Pos;

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

        $this->operator = 'inpost';
        $this->pointCode = 'GRU340';

        $this->deleteInteractions();
        $this->setInteraction();
    }

    public function testGetPos()
    {

        $apiClientPos = new \Bliskapaczka\ApiClient\Bliskapaczka\Pos('test-test-test-test');
        $apiClientPos->setApiUrl($this->host . '/api');
        $apiClientPos->setOperator($this->operator);
        $apiClientPos->setPointCode($this->pointCode);

        $response = json_decode($apiClientPos->get());

        $this->assertEquals('INPOST', $response->operator);
        $this->assertEquals('GRU340', $response->code);
        $this->assertEquals('Przy markecie KAUFLAND', $response->description);
        $this->assertEquals("Piłsudskiego 10", $response->street);
        $this->assertEquals('86-300', $response->postalCode);
        $this->assertEquals("Grudziądz", $response->city);
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
  "description": "Get pos info",
  "provider_state": "Return all information about existing point",
  "request": {
    "method": "get",
    "path": "/api/v1/pos/' . $this->operator . '/' . $this->pointCode . '"
  },
  "response": {
    "status": 200,
    "headers": {
      "Content-Type": "application/json"
    },
    "body": {
      "operator": "INPOST",
      "operatorPretty": "InPost",
      "brand": "INPOST",
      "brandPretty": "Punkt sieci InPost",
      "postingPoint": true,
      "deliveryPoint": true,
      "cod": true,
      "code": "GRU340",
      "street": "Pi\u0142sudskiego 10",
      "city": "Grudzi\u0105dz",
      "postalCode": "86-300",
      "district": null,
      "province": "Pomorskie",
      "longitude": 18.75643,
      "latitude": 53.48867,
      "openingHoursMap": {
        "WEDNESDAY": {
          "from": "00:00",
          "to": "23:59"
        },
        "MONDAY": {
          "from": "00:00",
          "to": "23:59"
        },
        "THURSDAY": {
          "from": "00:00",
          "to": "23:59"
        },
        "SUNDAY": {
          "from": "00:00",
          "to": "23:59"
        },
        "FRIDAY": {
          "from": "00:00",
          "to": "23:59"
        },
        "TUESDAY": {
          "from": "00:00",
          "to": "23:59"
        },
        "SATURDAY": {
          "from": "00:00",
          "to": "23:59"
        }
      },
      "description": "Przy markecie KAUFLAND",
      "available": true,
      "paymentPointDesc": "P\u0142atno\u015b\u0107 kart\u0105 wy\u0142\u0105cznie w paczkomacie.' .
        'Dost\u0119pno\u015b\u0107: 24\/7"
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
