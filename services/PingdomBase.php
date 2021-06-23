<?php
/**
 * Base for other Pingdom services
 *
 * @author: tuanha
 */
namespace PingdomBuddy;

class PingdomBase
{
    /**
     * @var $httpCLient resorce
     */
    protected $httpCLient;

    /**
     * @var $baseUrl string
     */
    protected $baseUrl;

    /**
     * Initialize new instance
     */
    public function __construct()
    {
        $this->baseUrl = 'https://api.pingdom.com/api/3.1';

        $this->httpCLient = curl_init();
        curl_setopt_array($this->httpCLient, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $_ENV['PD_API_TOKEN'],
                "cache-control: no-cache",
            ]
        ]);
    }

    /**
     * Execute request to Pingdom Endpoint
     * @return array
     */
    protected function execute()
    {
        $result = curl_exec($this->httpCLient);
        $executionError = curl_error($this->httpCLient);
        if ($executionError) {
            return [
                'executionStatus' => false,
                'data' => $executionError
            ];
        } else {
            return [
                'executionStatus' => true,
                'data' => $result
            ];
        }
    }

    public function __destruct()
    {
        curl_close($this->httpCLient);
    }
}
