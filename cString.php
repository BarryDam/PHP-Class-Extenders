<?php
	/**
	 * cString contains string functions which are often used
	 */
	class cString {
		// the actual string
		private $str = false;

		/*****************
		 * Magic methods *
		 *****************/
		public function __construct($str) 
		{
			$this->str = (string) $str;
		}
		public function __toString() 
		{
			return $this->str;
		}

		/******************************************************************
		 * Existing string methods (mostly needed in Extra added methods) *
		 ******************************************************************/
		public function base64decode()
		{
			$str = base64_decode($this->str);
			return new cString($str);
		}
		public function base64encode()
		{
			$str = base64_encode($this->str);
			return new cString($str);
		}
		public function gzdeflate($getIntLevel = -1, $getIntEncoding = ZLIB_ENCODING_RAW)
		{
			$str = gzdeflate($this->str, $getIntLevel, $getIntEncoding);
			return new cString($str);
		}
		public function gzinflate($getIntLength = 0)
		{
			$str = gzinflate($this->str, $getIntLength);
			return new cString($str);
		}
		public function rot13()
		{
			$str = str_rot13($this->str);
			return new cString($str);
		}
		public function stripTags() 
		{
			$str = strip_tags($this->str);
			return new cString($str);
		}
		public function toLowerCase() 
		{
			$str = strtolower($this->str);
			return new cString($str);
		}
		public function toUpperCase() 
		{
			$str = strtoupper($this->str);
			return new cString($str);
		}
		public function trim($getCharlist = false)
		{
			$str = ($getCharlist)
				? trim($this->str, $getCharlist)
				: trim($this->str) ;
			return new cString($str);
		}

		/*****************
		 * Extra methods *
		 *****************/

		/**
		 * Decode string
		 * @return cString Decoded
		 */
		public function decode()
		{
			$str = $this
				->base64decode()
				->rot13()
				->gzinflate();
			return new cString($str);
		}		
		/**
		 * Encode string
		 * @return cString encoded
		 */
		public function encode()
		{
			$str = $this
				->gzdeflate()
				->rot13()
				->base64encode();
			return new cString($str);				
		}
		/**
		 * do a replace or preg_replace
		 * @param  $getNeedleOrPattern Needle or pattern
		 * @param  $getReplace         replacement
		 * @param  boolean $boolRegex set to true will performa a regex
		 * @return cString 
		 */
		public function replace($getNeedleOrPattern, $getReplace, $boolRegex = false)
		{
			$str = $this->str;
			if (is_array($getNeedleOrPattern) && count($getNeedleOrPattern)) {
				$cString = new cString($str);
				foreach($getNeedleOrPattern as $strNeedleOrPattern)
					$cString = $cString->replace($strNeedleOrPattern, $getReplace, $boolRegex);
				return $cString;
			} else {				
				$str = ($boolRegex)
					? preg_replace($getNeedleOrPattern, $getReplace, $str)
					: str_replace($getNeedleOrPattern, $getReplace, $str);
				return new cString($str);			
			}
			
		}
		/**
		 * format string to a valid ASCII string
		 * @param  mixed or array  $getReplace  all chars passed will be replaced by $getDelimiter    
		 * @param  string $getDelimiter  all whitespaces will be replaced by this 
		 * @return cString
		 */
		public function toASCII($getReplace = array(), $getDelimiter = '-')
		{
			$str = $this->str;
			// replace $getReplace to whitespace
			if ( !empty($getReplace))
				$str = $this->replace((array)$getReplace, ' ');
			// convert utf-8 to asci
			$str = new cString(iconv('UTF-8', 'ASCII//TRANSLIT', $str));
			$str = $str
				->replace("/[^a-zA-Z0-9\/_|+ -]/", '', true)
				->trim('-')
				->toLowerCase()
				->replace("/\s{2,}/", ' ', true) // multiple whitespace to 1 whitespace
				->replace("/[\/_|+ -]+/", $getDelimiter) // underscores to delemiter
				->replace(' ', $getDelimiter); // whitespace to delimiter
			return new cString($str);
		}
		/**
		 * format a string to url-safe string
		 * example : 
		 * This Is the page
		 * will be formatted to:
		 * this-is-the-page		 * 
		 * @param  string $getDelimiter 
		 * @return cString formatted-url-like-this
		 */
		public function toURL($getDelimiter = '-')
		{
			// turns I'll be back to I-ll-be-back
			$str = $this->trim()->toASCII("'", $getDelimiter);
			return new cString($str);
		}
		/**
		 * convert a string sentence to a array with words
		 * @return array 	array('The', 'Quick', 'Brown', 'Fox');
		 */
		public function toWordsArray()
		{
			$arrReturn = array();
			// split words at whitespace
			$arr = array_unique(preg_split('/[\ \n\,]+/', $this->str));
			// create a new cString from every word
			foreach($arr as $k => $v) 
				$arrReturn[$k] = new cString($v);
			return $arrReturn;
		}
		/**
		 * truncate a string by length
		 * if possible > the string is truncated on a whole word
		 * @param 	int 	$getIntLengt maximum string length  
		 * @param 	string 	$getAppend paste this after truncated word
		 * @return 	cString truncated when string is longer then $getIntLength
		 */
		public function truncate($getIntLength, $getAppend='&hellip;')
		{
			$str = $this->replace('/\<br(\s*)?\/?\>/i', PHP_EOL, true)->stripTags();
			if (strlen($str) < $getIntLength) 
				return $str;
			$arrWords 	= explode(' ', $str);
			$strNew 	= null;
			foreach($arrWords as $strWord) {
				// if first word is longer then $getIntLength
				// cut the word at passed length 
				if(
					strlen($strWord) > $getIntLength &&
					$strNew == null
				) {
					return new cString(substr($strWord, 0, $getIntLength).$getAppend);
				}
				// truncate at whole word
				if ((strlen($strNew) + strlen($strWord)) > $getIntLength) {
					return new cString($strNew.$getAppend);
				}
				$strNew .= (($strNew)? ' ' : '' ).$strWord;
			}
			// no truncation needed here
			return new cString($strNew);
		}

		/**
		 * Check if string has phrase / words in it (case insensitive)
		 * @param  string  $getPhase seachphrase to find
		 * @return boolean true when found 
		 */
		public function hasPhrase($getPhase='')
		{
			return (preg_match("/$getPhase/i", $this->str));


			// $a = $o->toLowerCase()->replace('/[^a-zA-Z0-9 @]/','', true)->toWordsArray();

			// $oPhrase = new cString($getPhase);
			// $b = $oPhrase->toLowerCase()->replace('/[^a-zA-Z0-9 @]/','', true)->toWordsArray();

			// return (count(array_intersect($a, $b)) > 0);
		}
		

	}
?>