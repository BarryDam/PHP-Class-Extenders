<?php
	/**
	*	Custom function to date Time
	*	v 1.0.1
	**/
	class cDateTime extends \DateTime
	{
		
		/**
		*	@param (mixed) $getStart 	= unixtimestamp or datetimeobject
		*	@param (mixed) $getEnd 		= unixtimestamp or datetimeobject
		*	@return (array) $arrReturnObjects = array with DateTime Objects in date order
		**/
		public static function getRangeInDays($getStart=false, $getEnd=false){
					echo 'cDateTime.php Line 14:<br /><pre>'.print_r($getEnd,true).'</pre>';
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
	}
?>
