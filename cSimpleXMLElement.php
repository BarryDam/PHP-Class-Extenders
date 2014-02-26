<?php
	/**
	 *	Custom SimipleXMLElement class
	 * 	Contains less common SimipleXMLElement functions 
	 *	@version 1.0
	 * 	@author Barry Dam
	 */
	class cSimpleXMLElement extends \SimpleXMLElement {
		/**
		 * Convert an SimpleXMLElement object to array
		 * @param (SimpleXMLElement) $getXml
		 */
		public static function convertToArray($getXml = false) 
		{	
			if ($getXml instanceof SimpleXMLElement || $getXml instanceof cSimpleXMLElement) {
				$json = json_encode($getXml);
				return json_decode($json, true);
			} 
		}
		/**
		 * Convert a xml string to array
		 * @param (string) xml
		 */
		public static function loadStringToArray($getString = false)
		{
			if (! is_string($getString)) return false;
			$objSimpleXML = simplexml_load_string($getString);
			if ($objSimpleXML) return self::convertToArray($objSimpleXML);
		}
	};
?>