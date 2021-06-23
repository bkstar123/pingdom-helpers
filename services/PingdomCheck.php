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
	 * Get overview of all Pingdom checks
	 *
	 * @return false||array|throw Exception
	 */
	public function getChecks()
	{
		$path = '/checks?include_tags=true';
		curl_setopt($this->httpCLient, CURLOPT_URL, $this->baseUrl . $path);
		curl_setopt($this->httpCLient, CURLOPT_CUSTOMREQUEST, 'GET');
		$result = $this->execute();
		if ($result['executionStatus']) {
			if (property_exists(json_decode($result['data']), 'error')) {
				$error =  json_decode($result['data'])->error;
				throw new \Exception($error->errormessage);
			} else {
				return json_decode($result['data'])->checks;
			}
		} else {
			return false;
		}
	}

	/**
	 * Get a check's summary average
	 *
	 * @param string  $checkID
	 * @param string  $from
	 * @param string  $to
	 * @return false|array|throw Exception
	 */
	public function getCheckSummaryAvg($checkID, $from, $to)
	{
		$path = "/summary.average/{$checkID}?from={$from}&to={$to}&includeuptime=true";
		curl_setopt($this->httpCLient, CURLOPT_URL, $this->baseUrl . $path);
		curl_setopt($this->httpCLient, CURLOPT_CUSTOMREQUEST, 'GET');
		$result = $this->execute();
		if ($result['executionStatus']) {
			if (property_exists(json_decode($result['data']), 'error')) {
				$error =  json_decode($result['data'])->error;
				throw new \Exception($error->errormessage);
			} else {
				return json_decode($result['data'])->summary;
			}
		} else {
			return false;
		}
	}
}