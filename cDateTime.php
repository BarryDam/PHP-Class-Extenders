<?php
	/**
	 *	Custom DateTime class
	 * 	Contains less common DateTime functions 
	 *	@version 1.0.4
	 * 	@author Barry Dam
	 *  @see http://www.php.net/manual/en/class.datetime.php
	 */
	class cDateTime extends \DateTime
	{
		/**
		 *  Extra functions 
		 */
			
			/**
			 * @param (string) $getFormat is a date time format @example d-m-Y
			 * @param (string) $getLocale @example nl_NL
			 * @return (string) date formated by locale @example $cDateTimeObject->formatByLocale('l', 'nl_NL'); will return Maandag
			 */
			public function formatByLocale($getFormat = false, $getLocale = false)
			{
				/* func cuz setlocale && strtime doesn't work */
				if(! $getFormat) return false;				
				/**
				 * Locales 
				 */
				$arrLocales = array();
				switch($getLocale) {
					case 'nl_NL' :
						$arrLocales['D'] = array('', 'Ma.', 'Di.', 'Wo.', 'Do.', 'Vrij.', 'Za.', 'Zo.') ;
						$arrLocales['l'] = array('', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag');
						$arrLocales['M'] = array('', 'Jan.', 'Feb.', 'Maart', 'Apr.', 'Mei', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Okt.', 'Nov.', 'Dec.');
						$arrLocales['F'] = array('', 'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');
						break;
					case 'en_EN' :
					default :
						return $this->format($getFormat);
						//return before break
						break;
				}
				/**
				 * Get The replacements
				 */
				$arrReplacements = array();
				/* Days first short then full */
					if ( strpos($getFormat, 'l') !== false) {
						$arrReplacements[$this->format('l')] = $arrLocales['l'][$this->format('N')];
					}
					if ( strpos($getFormat, 'D') !== false) {
						$arrReplacements[$this->format('D')] = $arrLocales['D'][$this->format('N')];
					}
				/* Months first short then full */
					if ( strpos($getFormat, 'M') !== false) {
						$arrReplacements[$this->format('M')] = $arrLocales['M'][$this->format('n')];
					}
					if ( strpos($getFormat, 'F') !== false) {
						$arrReplacements[$this->format('F')] = $arrLocales['F'][$this->format('n')];
					}					
				$strFormattedDatetime = $this->format($getFormat);
				if ($arrReplacements) {
					foreach ($arrReplacements as $key => $value) {
						$strFormattedDatetime =  preg_replace('/\b'.$key.'\b/', $value, $strFormattedDatetime);													
					}
				}				
				return $strFormattedDatetime;
			}


			/**
			 * @param  string $getLocale  @example nl_NL   # not required     
			 * @param  string  date time format  @example nl_NL
			 * @return string @example 1 minute ago @example 15 hours ago
			 */
			public function formatRelativeTime($getLocale = false, $strDefaultFormat = 'd-m-Y H:i')
			{
				$arrLocales = array();
				switch($getLocale) {
					case 'nl_NL' :
					case 'nl' :
						$arrLocales = array(
							0 => 'Minder dan een minuut geleden',
							1 => 'Ongeveer een minuut geleden',
							2 => 'minuten geleden',
							3 => 'Ongeveer een uur geleden',
							4 => 'uren geleden'							
						);
						break;
					case 'en_EN' :
					case 'en' :
					default:
						$arrLocales = array(
							0 => 'Less than a minute ago',
							1 => 'About a minute ago',
							2 => 'minutes ago',
							3 => 'About an hour ago',
							4 => 'hours ago'
						);
						break;
				}
				$intTimestamp 	= $this->getTimestamp();
				$intDelta		= time()-$intTimestamp;
				if ($intDelta < 60)
					return $arrLocales[0];
				elseif ($intDelta > 60 && $intDelta < 120)	
					return $arrLocales[1];
				elseif ($intDelta > 120 && $intDelta < (60*60))
					return strval(round(($intDelta/60),0)).' '.$arrLocales[2];
				elseif ($intDelta > (60*60) && $intDelta < (120*60))
					return $arrLocales[3];
				elseif ($intDelta > (120*60) && $intDelta < (24*60*60))
					return strval(round(($intDelta/3600),0)).' '.$arrLocales[4];
				else
					return $this->format($strDefaultFormat);			
			}

		/**
		 * Static functions 
		 */
			/**
			*	@param (mixed) $getStart 	= unixtimestamp or datetimeobject
			*	@param (mixed) $getEnd 		= unixtimestamp or datetimeobject
			*	@return (array) $arrReturnObjects = array with DateTime Objects in date order
			**/
			public static function getRangeInDays($getStart=false, $getEnd=false){
				$dateStart 		= self::getRange_GetValidDateTime($getStart);
				$dateEnd 		= self::getRange_GetValidDateTime($getEnd);					
				if($dateStart == $dateEnd) return array($dateStart);
				$dateInterval 	= new DateInterval('P1D');
				//$dateEnd->add($dateInterval); // to add the last day of the month
				$objDatePeriod 	= new DatePeriod($dateStart, $dateInterval, $dateEnd);					
				if(!$objDatePeriod) return array($dateStart) ;
				$arrReturnObjects = array();
				/**
				* 	there is a bug in DatePeriod php < 5.5 causing the day to increment by one when using getTimestamp();
				*	(https://bugs.php.net/bug.php?id=52454 && https://bugs.php.net/bug.php?id=53340)
				*	Therefore recreate the timestamp by unix time by format('U');
				**/
				foreach($objDatePeriod as $objDateTime) $arrReturnObjects[] = self::createFromTimestamp($objDateTime->format('U'));	
				$arrReturnObjects[] = $dateEnd;
				return $arrReturnObjects;
			}
			private static function getRange_GetValidDateTime($get=false){
				if (is_a($get,'DateTime') || is_a($get,'cDateTime')){
					//return when it's the correct object
					return $get;
				} elseif( is_numeric($get) ){
					// unix timestamp 
					return self::createFromTimestamp($get);
				} else {
					return false ;
				}
			}

			/**
			 * @param (int) $getIntWeekNr the week number  not se? > this week
			 * @param (int) $getIntYear the FULL year.. not set?  > default this year
			 */
			public static function getRangeByWeekNr($getIntWeekNr = false, $getIntYear = false)
			{
				if (! is_numeric($getIntWeekNr)) $getIntWeekNr  = date('W');
				if (! is_numeric($getIntYear)) $getIntYear = date('Y');
				$dateTime = new cDateTime();
				$dateTime->setTime(00, 00);
				$dateTime->setISOdate($getIntYear, $getIntWeekNr);
				$datTimeFirstDayOfWeek = clone $dateTime->modify(('Sunday' == $dateTime->format('l')) ? 'Monday last week' : 'Monday this week');
				$dateTimeLastDayOfWeek = clone $dateTime->modify('Sunday this week');  
				return self::getRangeInDays($datTimeFirstDayOfWeek, $dateTimeLastDayOfWeek);
			}

			/**
			*	@return (dateTime object) today
			**/
			public static function getToday(){
				return new cDateTime(date('Y-m-d 00:00:00'));			
			}

			public static function createFromTimestamp($getTimestamp){
				if(!$getTimestamp || !is_numeric($getTimestamp)) return false ;
				$dateTime = new cDateTime();
				//$dateTime->setTimeZone(new DateTimeZone('Europe/Amsterdam'));
				$dateTime->setTimestamp($getTimestamp);
				return $dateTime;
			}

			/**
			 * Create a DateTime object from a Atom Format and convert it to the current time zone
			 * @param (DateTime::ATOM) exmple 2014-01-26T09:00:00Z
			 * @return (datetime object) in current time zone (default time zone)
			 */
			public static function createAndConvertFromAtom($getAtom)
			{
				if (! $getAtom) return false;
				$dateTime = cDateTime::createFromFormat(DateTime::ATOM, $getAtom);
				$dateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				return $dateTime;
			}
		/* End static*/
	};
?>
