<?php

namespace  Bliskapaczka\ApiClient\Bliskapaczka;

use PHPUnit\Framework\TestCase;

class GetWithNumbersTest extends TestCase
{
    protected function setUp()
    {
        if (getenv('PACT_MOCK_SERVICE_URL')) {
            $this->host = 'madkom__pact-mock-service:1234';
        } else {
            $this->host = 'localhost:1234';
        }

        $this->reportFile = __DIR__ . '/../../../../../data/pact/Bliskapaczka/ApiClient/Bliskapaczka/Report/report.pdf';
        $this->numbers = '000000001P-0000000001,000000001P-0000000002';

        $this->deleteInteractions();
        $this->setInteraction();
    }

    public function testGetReport()
    {
        $testFile = '/tmp/test_3.pdf';

        $apiClient = new Report('test-test-test-test');
        $apiClient->setApiUrl($this->host);
        $apiClient->setNumbers($this->numbers);

        $response = $apiClient->get();

        // HACK FOR MOCKING!!!
        // base64_decode for file contet
        // In real response we have file content
        file_put_contents($testFile, base64_decode($response));

        $this->assertEquals('application/pdf', mime_content_type($testFile));

        unlink($testFile);
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

        // HACK FOR MOCKING!!!
        // base64_encode for file contet
        // In real response we have file content
        $options[CURLOPT_POSTFIELDS] = '{
  "description": "Get report file with given param numbers",
  "provider_state": "API should return valid pdf file",
  "request": {
    "method": "get",
    "path": "/v2/report/pickupconfirmation",
    "query": "numbers=' . $this->numbers . '"
  },
  "response": {
    "status": 200,
    "headers": {
      "Content-Type": "application/pdf"
    },
    "body": "' . base64_encode(file_get_contents($this->reportFile)) . '"
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
