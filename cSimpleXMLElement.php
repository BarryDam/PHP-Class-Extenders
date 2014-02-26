<?php
	/**
	 *	Custom SimipleXMLElement class
	 * 	Contains less common SimipleXMLElement functions 
	 *	@version 1.0
	 * 	@author Barry Dam
	 * 	@see http://www.php.net/manual/en/class.simplexmlelement.php
	 */
	class cSimpleXMLElement extends \SimpleXMLElement {
		/**
		 * Convert an SimpleXMLElement object to array
		 * @param (SimpleXMLElement) $getXml
		 */
		public static function convertToArray($getXml = false) 
		{
			$objChildren = false;
			$return 	 = null;
			if ($getXml instanceof SimpleXMLElement || $getXml instanceof cSimpleXMLElement) {
				$objChildren = $getXml->children();
			}
			if (! $objChildren) return false;
			foreach ($objChildren as $key => $value) {
				$arrValues = (array)$value->children();
				if (count($arrValues) > 0) {
					$return[$key] = self::convertToArray($value);
				} else {
					if (! isset($return[$key])) {
						$return[$key] = (string)$value;
					} else {
						if (! is_array($return[$key])) {
							$return[$key] = array($return[$key], (string)$value);
						} else {
							$return[$key][] = (string)$value;
						}
					}
				}
			}
			return $return;
		}
	};
?>