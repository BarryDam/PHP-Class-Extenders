<?php
	/**
	 * cString contains string functions which are often used
	 */
	class cString {
		private $str = false;

		// Magic Methods //

		public function __construct($str) 
		{
			$this->str = (string) $str;
		}
		public function __toString() 
		{
			return $this->str;
		}

		// existing string methods (mostly needed in Extra added methods)

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
		public function trim($getCharlist)
		{
			$str = trim($this->str, $getCharlist);
			return new cString($str);
		}

		// Extra added methods

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
			if ($boolRegex) {
				$str = preg_replace($getNeedleOrPattern, $getReplace, $str);
			} else {
				$str = str_replace($getNeedleOrPattern, $getReplace, $str);
			}
			return new cString($str);
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
				->replace("/[\/_|+ -]+/", $getDelimiter)
				->replace(' ', $getDelimiter);
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
			$str = $this->toASCII("'", $getDelimiter);
			return new cString($str);
		}
		/**
		 * truncate a string by length
		 * if possible > the string is truncated on a whole word
		 */
		public function truncate($getIntLength, $getAppend='&hellip;')
		{
			$str = $this;
			if (strlen($str) < $getIntLength) 
				return $this;
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
				// cut at last word
				if ((strlen($strNew) + strlen($strWord)) > $getIntLength) {
					return new cString($strNew.$getAppend);
				}
				$strNew .= (($strNew)? ' ' : '' ).$strWord;
			}
			// no need to truncate
			return new cString($strNew);
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
		public function decode()
		{
			$str = $this
				->base64decode()
				->rot13()
				->gzinflate();
			return new cString($str);
		}

	}
?>