<?php
/**
 * Wave HTTP client
 *
 * Copyright (c) 2012 Frengstad Web Teknologi and contributors 
 * All rights reserved
 *
 * CSV filter for HTTP input stream
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
class CSV implements Iface {

	protected $mime = array(
		'text/csv',
	);


	/**
	 * Take a raw CSV input stream and decodes it to php native type
	 *
	 * @param string $raw The raw request
	 * @return array
	 */
	public function decode ($raw) {
		throw new \LogicException("No support for CSV decoding.");
	}

	/**
	 * Take a PHP array and decode it to a raw CSV string
	 *
	 * @param string $arr The array to encode
	 * @return array
	 */
	public function encode (array $arr) {
		if (isset($arr['error'])) {
			$ret = "code,message\r\n";
			foreach ($arr['error'] as $err) {
				$ret .= "{$err['code']},{$err['message']}\r\n";
			}

			return $ret;
		}

		$key = key($arr);
		// Enfore single dimension
		$k   = array_keys(is_int($key) ? $arr[$key] : reset($arr[$key]));
		$ret = implode(',', $k) . "\r\n";

		foreach ((is_int($key) ? $arr : $arr[$key]) as $v) {
			$ret .= implode(',', $v) . "\r\n";
		}

		return $ret;
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
