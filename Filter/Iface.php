<?php
/**
 * Wave HTTP client
 *
 * Copyright (c) 2012 Frengstad Web Teknologi and contributors  
 * All rights reserved
 *
 * Interface for HTTP stream filters
 *
 * @package	  wave.io
 * @version	  0.1
 * @copyright Frengstad Web Teknologi	
 * @author	  Olav Frengstad <olav@fwt.no>
 * @license	  FWT Client license
 */

namespace Wave\IO\Filter;

/**
 * Allow encoding and decoding from a specific contet-type
 *
 */
interface Iface {
	/**
	 * Take a raw input stream and decodes it to php native type
	 *
	 * @param string $raw The raw request
	 * @return array
	 */
	public function decode ($raw);

	/**
	 * Take a PHP array and decode it to a raw string
	 *
	 * @param string $arr The array to encode
	 * @return array
	 */
	public function encode (array $arr);

	/**
	 * Return a list of mime types that this filter handles
	 *
	 * @return array List of supported mime types
	 */
	public function mime ();
}