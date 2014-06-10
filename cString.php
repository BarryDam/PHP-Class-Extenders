<?php
	/**
	 * cString contains string functions which are often needed
	 */
	class cString {
		private $str = false;

		public function __construct($str) 
		{
			$this->str = (string) $str;
		}
		public function __toString() 
		{
			return $this->str;
		}

		public function trim($getCharlist)
		{
			$str = trim($this->str, $getCharlist);
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

		public function stripTags() 
		{
			$str = strip_tags($this->str);
			return new cString($str);
		}

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
		 * @param  string $getDelimiter [description]
		 * @return cString formatted-url-like-this
		 */
		public function toURL($getDelimiter = '-')
		{
			// turns I'll be back to I-ll-be-back
			$str = $this->toASCII("'", $getDelimiter);
			return new cString($str);
		}
	}
?>