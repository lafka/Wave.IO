<?php
/**
 * Wave HTTP client
 *
 * Copyright (c) 2012 Frengstad Web Teknologi and contributors 
 * All rights reserved
 *
 * Client for communicating with HTTP endpoints
 *
 * @package	  wave.io
 * @version	  0.1
 * @copyright Frengstad Web Teknologi	
 * @author	  Olav Frengstad <olav@fwt.no>
 * @license	  BSD 3 Clause
 */

namespace Wave\IO\Client;

use Wave\IO\Filter\Iface as FilterIface, UnexpectedValueException, LogicException;

class HTTP {
	protected $endpoint;

	protected $accepts = array(
		'application/xml'  => '\Wave\IO\Filter\XML',
		'application/json' => '\Wave\IO\Filter\JSON',
	);

	/**
	 * CURL resource to use
	 * @var resource
	 */
	private $curl;

	/**
	 * The stream filter to use for encoding/decoding values
	 *
	 * @var \TM\Solution\Rest\Filter
	 */
	protected $filter;

	/**
	 * Create a new REST client
	 *
	 * @param string $endpoint The server endpoint
	 * @return \Wave\IO\Client\HTTP
	 */
	public function __construct ($endpoint) {
		$this->endpoint = parse_url($endpoint);

		if (!extension_loaded('curl')) {
			throw new LogicException("CURL extension is not installed");
		}

		$this->curl = curl_init();

		curl_setopt_array($this->curl, array(
			CURLOPT_FAILONERROR    => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FAILONERROR    => false,
			CURLOPT_RETURNTRANSFER => true,
		));

		if (array_key_exists('port', $this->endpoint))
			curl_setopt($this->curl, CURLOPT_PORT, $this->endpoint['port']);
	}

	/**
	 * Set the stream filter
	 *
	 * @param string $format The MIME type to use
	 * @return void
	 */
	public function filter ($format) {
		if (!array_key_exists($format, $this->accepts)) {
			throw new UnexpectedValueException("Unable to proccess: no such format: {$format}");
		}

		$this->filter = new $this->accepts[$format];
	}

	/**
	 * Make a query for a given resource
	 *
	 * @param string $resource The resource identifier to query for
	 * @param string $method The HTTP method to use.
	 * @param array  $raw Request parameters
	 * @return \stdClass An object contain result,raw,code properties
	 */
	public function request ($resource, $method = 'GET', $raw = array()) {
		if (!$this->filter instanceof FilterIface) {
			$this->filter('application/json');
		}

		$localhandle = curl_copy_handle($this->curl);

		if (!empty($raw)) {
			$data = $this->filter->encode($raw);
			curl_setopt($localhandle, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($localhandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($localhandle, CURLOPT_HTTPHEADER, array('Accept: ' . implode(',', $this->filter->mime())));
		curl_setopt($localhandle, CURLINFO_HEADER_OUT, true);
		curl_setopt($localhandle, CURLOPT_URL, $this->endpoint['host'] . $resource);
		curl_setopt($localhandle, CURLOPT_RETURNTRANSFER, true);

		$raw  = curl_exec($localhandle);
		$code = curl_getinfo($localhandle, CURLINFO_HTTP_CODE);
		//$type = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);

		$resp = new \stdClass();
		$resp->result   = $this->filter->decode($raw);
		$resp->raw      = $raw;
		$resp->code     = $code;
		$resp->url      = $resource;
		$resp->endpoint = $this->endpoint;
		$resp->method   = strtoupper($method);

		curl_close($localhandle);

		return $resp;
	}

	public function __destruct () {
		unset($this->writer);
		curl_close($this->curl);
	}
}
