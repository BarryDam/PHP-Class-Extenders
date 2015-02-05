<?php
	/**
	 * Not so common convenience methods for a multidimensional array
	 *
	 *
	 */
	class cArrayMultidimensional extends ArrayObject {

		/**
		 * Filter the cArrayMultidimensional by search
		 * @param  string  $getStrSearch            SearchPrase
		 * @param  array   $arrSearchableProperties Array with properties to search.
		 *                                          By Default all properties will be searched
		 * @return void
		 */
		public function filterBySearch($getStrSearch = '', $getSearchableProperties = array())
		{
			// no search - do not filter array
			if (empty($getStrSearch))
				return;

			// prepare array to use in filter
			$arrUse = array($getStrSearch, $getSearchableProperties);

			// create a new array and exec the filter
			$arr = array_filter(
				$this->getArrayCopy(),
				function($a) use ($arrUse){
					// re-assign passed arguments of filterBySearch method
					list($getStrSearch, $getSearchableProperties) = $arrUse;
					foreach ($a as $keyProperty => $valProperty) {
						// when properties to search are set check if this property is searchable
						if (count($getSearchableProperties) && ! in_array($keyProperty, $getSearchableProperties))
							continue;
						$o = new cString($valProperty);
						if ($o->hasPhrase($getStrSearch))
							return true;
					}					
				}
			);
			
			// re-assign the cArrayMultidimensional array
			$this->exchangeArray($arr);
		}


		/**
		 * Sorting a multidimensional array
		 * @example
		 * $a = array(
		 * 	array('exampleKey'=>'b'),
		 * 	array('exampleKey'=>'a')
		 * 	array('exampleKey'=>'c')
		 * );
		 * $a = new cArrayMultidimensional($a);
		 * $a->sortBy('exampleKey')
		 * // $a is now 
		 * $a = array(
		 * 	array('exampleKey'=>'a'),
		 * 	array('exampleKey'=>'b'),
		 * 	array('exampleKey'=>'c')
		 * );
		 */
		public function sortBy($getSortBy)
		{
			$sortBy = $getSortBy;
			$items = $this->getArrayCopy();
			// validation
			if(count($items) == 0)
				return;
						
			// if $sortBy was a string, convert it to an assoc array
			if(gettype($sortBy) == "string")
				$sortBy = array($sortBy => SORT_ASC);
			
			foreach($sortBy as $key => $value)
			{
				if(is_numeric($key))
				{
					$sortBy[$value] = SORT_ASC;
					unset($sortBy[$key]);
				}
			}
			
			// make arrays per $sortBy field
			$keySorted	= array();
			foreach($items as $itemKey => $item)
			{
				foreach($sortBy as $sortByKey => $sortByAscOrDesc )
				{
					if(@array_key_exists($sortByKey, $item) )
						$keySorted[$sortByKey][] = (array) strtolower($item[$sortByKey]);
					else
						$keySorted[$sortByKey][] = (array) strtolower($item->row[$sortByKey]);
				}
			}
			
			// bugfix: https://bugs.php.net/bug.php?id=49353
			$sortflags = array(
				SORT_REGULAR => SORT_REGULAR,
				SORT_NUMERIC => SORT_NUMERIC,
				SORT_STRING => SORT_STRING,
				SORT_DESC => SORT_DESC,
				SORT_ASC => SORT_ASC,
				SORT_LOCALE_STRING => SORT_LOCALE_STRING,
				SORT_NATURAL => SORT_NATURAL
			);
			
			// prepare multisort arguments
			$args		= array();
			foreach($sortBy as $sortByKey => $sortByAscOrDesc )
			{
				$args[] = &$keySorted[$sortByKey];
				$args[] = &$sortflags[$sortByAscOrDesc];// &$sortByAscOrDesc;
			}

			$args[] = &$items;

			// do multisort
			call_user_func_array('array_multisort', $args);
			

			// exhange this array
			$this->exchangeArray($items);
		}

	}
?>