<?php
/**
 * PingdomCheck
 *
 * @author: tuanha
 */
namespace PingdomBuddy;

use PingdomBuddy\PingdomBase;

class PingdomCheck extends PingdomBase
{
	/**
	 * @var $url string
	 */
	protected $url = '/checks';

	/**
	 * Create new instance
	 */
	public function __construct()
	{
		parent::__construct();
		curl_setopt($this->httpCLient, CURLOPT_URL, $this->baseUrl . $this->url);
	}

	/**
	 * Get all Pingdom checks
	 */
	public function getChecks()
	{
		curl_setopt($this->httpCLient, CURLOPT_CUSTOMREQUEST, 'GET');
		return $this->execute();
	}
}