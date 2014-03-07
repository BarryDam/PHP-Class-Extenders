<?php
	/**
	 *	Custom DateTime class
	 * 	Contains less common DateTime functions 
	 *	@version 1.0.2
	 * 	@author Barry Dam
	 *  @see http://www.php.net/manual/en/class.datetime.php
	 */
	class cDateTime extends \DateTime
	{
		
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
		*	@return (dateTime object) today
		**/
		public static function getToday(){
			return new DateTime(date('Y-m-d 00:00:00'));			
		}

		public static function createFromTimestamp($getTimestamp){
			if(!$getTimestamp || !is_numeric($getTimestamp)) return false ;
			$dateTime = new DateTime();
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
			$dateTime = DateTime::createFromFormat(DateTime::ATOM);
			$dateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			return $dateTime;
		}
	};
?>
