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
 * Allow encoding and decoding of a input stream from a XML
 *
 * @todo lafka 2012-03-19; Seperate encoding logic from the interface by creating external helper class for converting arr -> xml
 */
class HTML implements Iface {

	protected $data;
	protected $linkattrs;

	public function __construct (array $linkattrs = array()) {
		define('ENV_CLI', false);
		define('ENV_WEB', true);

		$this->linkattrs = $linkattrs;

		include 'TSA/lib/functions.php';
	}

	/**
	 * Take a raw input stream and decodes it to php native type
	 *
	 * @param string $raw The raw request
	 * @return array
	 */
	public function decode ($raw) {
		$this->data = $raw;
		$this->data = $raw;
		return array();
	}

	/**
	 * Take a PHP array and decode it to a raw xml string
	 *
	 * @param string $arr The array to encode
	 * @return array
	 */
	public function encode (array $arr) {
		return \gimle\common\var_dump($arr, true, 'export', 'auto', $this->linkattrs);
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