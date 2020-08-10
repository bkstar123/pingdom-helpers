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
     * @var $result mixed (False | API response)
     */
    public $result;

    /**
     * @var $executionError string
     */
    public $executionError;

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
     * @return mixed
     */
    protected function execute()
    {
        $this->result = curl_exec($this->httpCLient);
        $this->executionError = curl_error($this->httpCLient);
        curl_close($this->httpCLient);
        if ($this->executionError) {
            return false;
        } else {
            return true;
        }
    }
}
