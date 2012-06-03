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

use InvalidArgumentException, SimpleXMLElement, Exception, DOMDocument, DOMXpath, DOMElement;

/**
 * Allow encoding and decoding of a input stream from a XML
 *
 * @todo lafka 2012-03-19; Seperate encoding logic from the interface by creating external helper class for converting arr -> xml
 */
class XML extends DOMDocument implements Iface {

	protected $mime = array(
		'application/xml',
	);

	/**
	 * The Xpath object to use
	 * @var \DOMXpath
	 */
 	private $xpath;

 	/**
 	 * The name of the root element
 	 * @var string
 	 */
 	private $root;

 	/**
 	 * The name of child elements
 	 * @var string
 	 */
 	private $listname;

 	/**
 	 * Set up DOM enviroment
 	 *
 	 * @param string $rootname optional The name of the root node
 	 * @param string $listname optional The name to map numeric keys to
 	 * @return \TSA\Xml
 	 */
 	public function __construct ($rootname = 'root', $listname = 'node') {
 		parent::__construct('1.0', 'utf-8');
 		$this->formatOutput       = true;
 		$this->preserveWhiteSpace = true;
 		$this->listname           = $listname;
 		$this->root               = $this->appendChild($this->createElement($rootname));

 		$this->xpath              = new DOMXpath($this);
 	}

	/**
	 * Take a raw xml input stream and decodes it to php native type
	 *
	 * @param string $raw The raw request
	 * @return array
	 * @throws InvalidArgumentException If XML is not valid
	 */
	public function decode ($raw) {
		try {
			$xml = new SimpleXMLElement($raw);
		} catch (Exception $e) {
			throw new InvalidArgumentException(__METHOD__ . " first argument could not be interpreted as XML ({$e->getMessage()})");
		}

		return $this->xmlWalker($xml);
	}

	/**
	 * Take a PHP array and decode it to a raw xml string
	 *
	 * @param string $arr The array to encode
	 * @return array
	 */
	public function encode (array $arr) {
		$this->arrayWalker($arr);
		return $this->__toString();
	}

	/**
	 * Recursive array walker
	 *
	 * Convert array to a XML document
	 *
	 * @param array $arr The array to walk through
	 * @param DOMElement $node The DOMElement to append to
	 * @return void
	 * @todo lafka 2012-03-19; Can we do this with array_walk_recurisve?
	 */
	private function arrayWalker (array $arr, $node = null) {
		$node = $node instanceof DOMElement ? $node : $this->root;

 		$k = array_keys($arr);
 		for ($i = 0, $c = count($k); $i < $c; $i++) {
 			$index = is_numeric($k[$i]) ? $this->listname : $k[$i];
 			
 			if ( is_array($arr[$k[$i]]) ) {
 				$child = $this->createElement($index, null);
 			} else {
				$child = $this->createElement($index, $arr[$k[$i]]);
			}

			$node->appendChild($child);

 			if (is_array($arr[$k[$i]])) {
 				$this->arrayWalker($arr[$k[$i]], $child);
 			}

			unset($child);
 		}
	}

	/**
	 * Recursive XML walker
	 *
	 * Converts a SimpleXMLElement to a PHP array
	 *
	 * @param SimpleXMLElement $element The XML object
	 * @return array Processed array
	 * @todo lafka 2012-03-19; Any point in handling float?
	 */
	private function xmlWalker (SimpleXMLElement $element) {
		if (0 === count($element)) {
			return is_int($element) ? (int) $element : (string) $element;
		} else {
			$t     = array();
			$first = true;
			foreach ($element as $k => $v) {
				if (array_key_exists($k, $t)) {
					if (true === $first) {
						$t[$k] = array($t[$k]);
						$first = false;
					}
					$t[$k][] = $this->xmlWalker($v);
				} else {
					$t[$k] = $this->xmlWalker($v);
				}
			}
		}

		return $t;
	}

	/**
	 * Return a list of mime types that this filter handles
	 *
	 * @return array List of supported mime types
	 */
	public function mime () {
		return $this->mime;
	}

 	/**
 	 * Return string representation of xml
 	 *
 	 * @return string XML data as string
 	 */
 	public function __toString () {
 		return $this->saveXML();
 	}

 	public function __destruct () {
	 	unset ($this->xpath, $this->root);
 	}
}