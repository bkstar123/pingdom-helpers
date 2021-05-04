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
	protected $url = '/checks?include_tags=true';

	/**
	 * Get all Pingdom checks
	 */
	public function getChecks()
	{
		curl_setopt($this->httpCLient, CURLOPT_URL, $this->baseUrl . $this->url);
		curl_setopt($this->httpCLient, CURLOPT_CUSTOMREQUEST, 'GET');
		return $this->execute();
	}
}