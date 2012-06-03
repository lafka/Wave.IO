<?php
/**
 * Wave HTTP client
 *
 * Copyright (c) 2012 Frengstad Web Teknologi and contributors 
 * All rights reserved
 *
 * XML filter for HTTP input stream
 *
 * @package	  wave.io
 * @version	  0.1
 * @copyright Frengstad Web Teknologi	
 * @author	  Olav Frengstad <olav@fwt.no>
 * @license	  BSD 3 Clause
 */

namespace Wave\IO\Filter;

/**
 * Allow encoding and decoding of a input stream from JSON
 */
class JSON implements Iface {

	protected $mime = array(
		'application/json',
	);


	/**
	 * Take a raw json input stream and decodes it to php native type
	 *
	 * @param string $raw The raw request
	 * @return array
	 * @throws InvalidArgumentException If XML is not valid
	 */
	public function decode ($raw) {
		return json_decode($raw);
	}

	/**
	 * Take a PHP array and decode it to a raw json string
	 *
	 * @param string $arr The array to encode
	 * @return array
	 */
	public function encode (array $arr) {
		return json_encode($arr);
	}

	/**
	 * Return a list of mime types that this filter handles
	 *
	 * @return array List of supported mime types
	 */
	public function mime () {
		return $this->mime;
	}
}