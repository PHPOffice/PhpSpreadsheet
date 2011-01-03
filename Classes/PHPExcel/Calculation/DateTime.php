<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		##VERSION##, ##DATE##
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/**
 * PHPExcel_Calculation_DateTime
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_DateTime {

	public static function _isLeapYear($year) {
		return ((($year % 4) == 0) && (($year % 100) != 0) || (($year % 400) == 0));
	}	//	function _isLeapYear()


	private static function _dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $methodUS) {
		if ($startDay == 31) {
			--$startDay;
		} elseif ($methodUS && ($startMonth == 2 && ($startDay == 29 || ($startDay == 28 && !self::_isLeapYear($startYear))))) {
			$startDay = 30;
		}
		if ($endDay == 31) {
			if ($methodUS && $startDay != 30) {
				$endDay = 1;
				if ($endMonth == 12) {
					++$endYear;
					$endMonth = 1;
				} else {
					++$endMonth;
				}
			} else {
				$endDay = 30;
			}
		}

		return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
	}	//	function _dateDiff360()


	/**
	 * _getDateValue
	 *
	 * @param	string	$dateValue
	 * @return	mixed	Excel date/time serial value, or string if error
	 */
	public static function _getDateValue($dateValue) {
		if (!is_numeric($dateValue)) {
			if ((is_string($dateValue)) && (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
			if ((is_object($dateValue)) && ($dateValue instanceof PHPExcel_Shared_Date::$dateTimeObjectType)) {
				$dateValue = PHPExcel_Shared_Date::PHPToExcel($dateValue);
			} else {
				$saveReturnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
				PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
				$dateValue = self::DATEVALUE($dateValue);
				PHPExcel_Calculation_Functions::setReturnDateType($saveReturnDateType);
			}
		}
		return $dateValue;
	}	//	function _getDateValue()


	/**
	 * _getTimeValue
	 *
	 * @param	string	$timeValue
	 * @return	mixed	Excel date/time serial value, or string if error
	 */
	private static function _getTimeValue($timeValue) {
		$saveReturnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$timeValue = self::TIMEVALUE($timeValue);
		PHPExcel_Calculation_Functions::setReturnDateType($saveReturnDateType);
		return $timeValue;
	}	//	function _getTimeValue()


	private static function _adjustDateByMonths($dateValue = 0, $adjustmentMonths = 0) {
		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
		$oMonth = (int) $PHPDateObject->format('m');
		$oYear = (int) $PHPDateObject->format('Y');

		$adjustmentMonthsString = (string) $adjustmentMonths;
		if ($adjustmentMonths > 0) {
			$adjustmentMonthsString = '+'.$adjustmentMonths;
		}
		if ($adjustmentMonths != 0) {
			$PHPDateObject->modify($adjustmentMonthsString.' months');
		}
		$nMonth = (int) $PHPDateObject->format('m');
		$nYear = (int) $PHPDateObject->format('Y');

		$monthDiff = ($nMonth - $oMonth) + (($nYear - $oYear) * 12);
		if ($monthDiff != $adjustmentMonths) {
			$adjustDays = (int) $PHPDateObject->format('d');
			$adjustDaysString = '-'.$adjustDays.' days';
			$PHPDateObject->modify($adjustDaysString);
		}
		return $PHPDateObject;
	}	//	function _adjustDateByMonths()


	/**
	 * DATETIMENOW
	 *
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATETIMENOW() {
		$saveTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$retValue = False;
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					$retValue = (float) PHPExcel_Shared_Date::PHPToExcel(time());
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					$retValue = (integer) time();
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					$retValue = new DateTime();
					break;
		}
		date_default_timezone_set($saveTimeZone);

		return $retValue;
	}	//	function DATETIMENOW()


	/**
	 * DATENOW
	 *
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATENOW() {
		$saveTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$retValue = False;
		$excelDateTime = floor(PHPExcel_Shared_Date::PHPToExcel(time()));
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					$retValue = (float) $excelDateTime;
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					$retValue = (integer) PHPExcel_Shared_Date::ExcelToPHP($excelDateTime) - 3600;
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					$retValue = PHPExcel_Shared_Date::ExcelToPHPObject($excelDateTime);
					break;
		}
		date_default_timezone_set($saveTimeZone);

		return $retValue;
	}	//	function DATENOW()


	/**
	 * DATE
	 *
	 * @param	long	$year
	 * @param	long	$month
	 * @param	long	$day
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATE($year = 0, $month = 1, $day = 1) {
		$year	= (integer) PHPExcel_Calculation_Functions::flattenSingleValue($year);
		$month	= (integer) PHPExcel_Calculation_Functions::flattenSingleValue($month);
		$day	= (integer) PHPExcel_Calculation_Functions::flattenSingleValue($day);

		$baseYear = PHPExcel_Shared_Date::getExcelCalendar();
		// Validate parameters
		if ($year < ($baseYear-1900)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if ((($baseYear-1900) != 0) && ($year < $baseYear) && ($year >= 1900)) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		if (($year < $baseYear) && ($year >= ($baseYear-1900))) {
			$year += 1900;
		}

		if ($month < 1) {
			//	Handle year/month adjustment if month < 1
			--$month;
			$year += ceil($month / 12) - 1;
			$month = 13 - abs($month % 12);
		} elseif ($month > 12) {
			//	Handle year/month adjustment if month > 12
			$year += floor($month / 12);
			$month = ($month % 12);
		}

		// Re-validate the year parameter after adjustments
		if (($year < $baseYear) || ($year >= 10000)) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year, $month, $day);
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) $excelDateValue;
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP($excelDateValue);
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return PHPExcel_Shared_Date::ExcelToPHPObject($excelDateValue);
					break;
		}
	}	//	function DATE()


	/**
	 * TIME
	 *
	 * @param	long	$hour
	 * @param	long	$minute
	 * @param	long	$second
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function TIME($hour = 0, $minute = 0, $second = 0) {
		$hour	= PHPExcel_Calculation_Functions::flattenSingleValue($hour);
		$minute	= PHPExcel_Calculation_Functions::flattenSingleValue($minute);
		$second	= PHPExcel_Calculation_Functions::flattenSingleValue($second);

		if ($hour == '') { $hour = 0; }
		if ($minute == '') { $minute = 0; }
		if ($second == '') { $second = 0; }

		if ((!is_numeric($hour)) || (!is_numeric($minute)) || (!is_numeric($second))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$hour	= (integer) $hour;
		$minute	= (integer) $minute;
		$second	= (integer) $second;

		if ($second < 0) {
			$minute += floor($second / 60);
			$second = 60 - abs($second % 60);
			if ($second == 60) { $second = 0; }
		} elseif ($second >= 60) {
			$minute += floor($second / 60);
			$second = $second % 60;
		}
		if ($minute < 0) {
			$hour += floor($minute / 60);
			$minute = 60 - abs($minute % 60);
			if ($minute == 60) { $minute = 0; }
		} elseif ($minute >= 60) {
			$hour += floor($minute / 60);
			$minute = $minute % 60;
		}

		if ($hour > 23) {
			$hour = $hour % 24;
		} elseif ($hour < 0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					$date = 0;
					$calendar = PHPExcel_Shared_Date::getExcelCalendar();
					if ($calendar != PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900) {
						$date = 1;
					}
					return (float) PHPExcel_Shared_Date::FormattedPHPToExcel($calendar, 1, $date, $hour, $minute, $second);
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::FormattedPHPToExcel(1970, 1, 1, $hour-1, $minute, $second));	// -2147468400; //	-2147472000 + 3600
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					$dayAdjust = 0;
					if ($hour < 0) {
						$dayAdjust = floor($hour / 24);
						$hour = 24 - abs($hour % 24);
						if ($hour == 24) { $hour = 0; }
					} elseif ($hour >= 24) {
						$dayAdjust = floor($hour / 24);
						$hour = $hour % 24;
					}
					$phpDateObject = new DateTime('1900-01-01 '.$hour.':'.$minute.':'.$second);
					if ($dayAdjust != 0) {
						$phpDateObject->modify($dayAdjust.' days');
					}
					return $phpDateObject;
					break;
		}
	}	//	function TIME()


	/**
	 * DATEVALUE
	 *
	 * @param	string	$dateValue
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function DATEVALUE($dateValue = 1) {
		$dateValue = trim(PHPExcel_Calculation_Functions::flattenSingleValue($dateValue),'"');
		//	Strip any ordinals because they're allowed in Excel (English only)
		$dateValue = preg_replace('/(\d)(st|nd|rd|th)([ -\/])/Ui','$1$3',$dateValue);
		//	Convert separators (/ . or space) to hyphens (should also handle dot used for ordinals in some countries, e.g. Denmark, Germany)
		$dateValue	= str_replace(array('/','.','-','  '),array(' ',' ',' ',' '),$dateValue);

		$yearFound = false;
		$t1 = explode(' ',$dateValue);
		foreach($t1 as &$t) {
			if ((is_numeric($t)) && ($t > 31)) {
				if ($yearFound) {
					return PHPExcel_Calculation_Functions::VALUE();
				} else {
					if ($t < 100) { $t += 1900; }
					$yearFound = true;
				}
			}
		}
		if ((count($t1) == 1) && (strpos($t,':') != false)) {
			//	We've been fed a time value without any date
			return 0.0;
		} elseif (count($t1) == 2) {
			//	We only have two parts of the date: either day/month or month/year
			if ($yearFound) {
				array_unshift($t1,1);
			} else {
				array_push($t1,date('Y'));
			}
		}
		unset($t);
		$dateValue = implode(' ',$t1);

		$PHPDateArray = date_parse($dateValue);
		if (($PHPDateArray === False) || ($PHPDateArray['error_count'] > 0)) {
			$testVal1 = strtok($dateValue,'- ');
			if ($testVal1 !== False) {
				$testVal2 = strtok('- ');
				if ($testVal2 !== False) {
					$testVal3 = strtok('- ');
					if ($testVal3 === False) {
						$testVal3 = strftime('%Y');
					}
				} else {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
			$PHPDateArray = date_parse($testVal1.'-'.$testVal2.'-'.$testVal3);
			if (($PHPDateArray === False) || ($PHPDateArray['error_count'] > 0)) {
				$PHPDateArray = date_parse($testVal2.'-'.$testVal1.'-'.$testVal3);
				if (($PHPDateArray === False) || ($PHPDateArray['error_count'] > 0)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
		}

		if (($PHPDateArray !== False) && ($PHPDateArray['error_count'] == 0)) {
			// Execute function
			if ($PHPDateArray['year'] == '')	{ $PHPDateArray['year'] = strftime('%Y'); }
			if ($PHPDateArray['month'] == '')	{ $PHPDateArray['month'] = strftime('%m'); }
			if ($PHPDateArray['day'] == '')		{ $PHPDateArray['day'] = strftime('%d'); }
			$excelDateValue = floor(PHPExcel_Shared_Date::FormattedPHPToExcel($PHPDateArray['year'],$PHPDateArray['month'],$PHPDateArray['day'],$PHPDateArray['hour'],$PHPDateArray['minute'],$PHPDateArray['second']));

			switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
				case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
						return (float) $excelDateValue;
						break;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
						return (integer) PHPExcel_Shared_Date::ExcelToPHP($excelDateValue);
						break;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
						return new DateTime($PHPDateArray['year'].'-'.$PHPDateArray['month'].'-'.$PHPDateArray['day'].' 00:00:00');
						break;
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function DATEVALUE()


	/**
	 * TIMEVALUE
	 *
	 * @param	string	$timeValue
	 * @return	mixed	Excel date/time serial value, PHP date/time serial value or PHP date/time object,
	 *						depending on the value of the ReturnDateType flag
	 */
	public static function TIMEVALUE($timeValue) {
		$timeValue = trim(PHPExcel_Calculation_Functions::flattenSingleValue($timeValue),'"');
		$timeValue	= str_replace(array('/','.'),array('-','-'),$timeValue);

		$PHPDateArray = date_parse($timeValue);
		if (($PHPDateArray !== False) && ($PHPDateArray['error_count'] == 0)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel($PHPDateArray['year'],$PHPDateArray['month'],$PHPDateArray['day'],$PHPDateArray['hour'],$PHPDateArray['minute'],$PHPDateArray['second']);
			} else {
				$excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel(1900,1,1,$PHPDateArray['hour'],$PHPDateArray['minute'],$PHPDateArray['second']) - 1;
			}

			switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
				case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
						return (float) $excelDateValue;
						break;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
						return (integer) $phpDateValue = PHPExcel_Shared_Date::ExcelToPHP($excelDateValue+25569) - 3600;;
						break;
				case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
						return new DateTime('1900-01-01 '.$PHPDateArray['hour'].':'.$PHPDateArray['minute'].':'.$PHPDateArray['second']);
						break;
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function TIMEVALUE()


	/**
	 * DATEDIF
	 *
	 * @param	long	$startDate		Excel date serial value or a standard date string
	 * @param	long	$endDate		Excel date serial value or a standard date string
	 * @param	string	$unit
	 * @return	long	Interval between the dates
	 */
	public static function DATEDIF($startDate = 0, $endDate = 0, $unit = 'D') {
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
		$unit		= strtoupper(PHPExcel_Calculation_Functions::flattenSingleValue($unit));

		if (is_string($startDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($endDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Validate parameters
		if ($startDate >= $endDate) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$difference = $endDate - $startDate;

		$PHPStartDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($startDate);
		$startDays = $PHPStartDateObject->format('j');
		$startMonths = $PHPStartDateObject->format('n');
		$startYears = $PHPStartDateObject->format('Y');

		$PHPEndDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
		$endDays = $PHPEndDateObject->format('j');
		$endMonths = $PHPEndDateObject->format('n');
		$endYears = $PHPEndDateObject->format('Y');

		$retVal = PHPExcel_Calculation_Functions::NaN();
		switch ($unit) {
			case 'D':
				$retVal = intval($difference);
				break;
			case 'M':
				$retVal = intval($endMonths - $startMonths) + (intval($endYears - $startYears) * 12);
				//	We're only interested in full months
				if ($endDays < $startDays) {
					--$retVal;
				}
				break;
			case 'Y':
				$retVal = intval($endYears - $startYears);
				//	We're only interested in full months
				if ($endMonths < $startMonths) {
					--$retVal;
				} elseif (($endMonths == $startMonths) && ($endDays < $startDays)) {
					--$retVal;
				}
				break;
			case 'MD':
				if ($endDays < $startDays) {
					$retVal = $endDays;
					$PHPEndDateObject->modify('-'.$endDays.' days');
					$adjustDays = $PHPEndDateObject->format('j');
					if ($adjustDays > $startDays) {
						$retVal += ($adjustDays - $startDays);
					}
				} else {
					$retVal = $endDays - $startDays;
				}
				break;
			case 'YM':
				$retVal = intval($endMonths - $startMonths);
				if ($retVal < 0) $retVal = 12 + $retVal;
				//	We're only interested in full months
				if ($endDays < $startDays) {
					--$retVal;
				}
				break;
			case 'YD':
				$retVal = intval($difference);
				if ($endYears > $startYears) {
					while ($endYears > $startYears) {
						$PHPEndDateObject->modify('-1 year');
						$endYears = $PHPEndDateObject->format('Y');
					}
					$retVal = $PHPEndDateObject->format('z') - $PHPStartDateObject->format('z');
					if ($retVal < 0) { $retVal += 365; }
				}
				break;
		}
		return $retVal;
	}	//	function DATEDIF()


	/**
	 * DAYS360
	 *
	 * @param	long	$startDate		Excel date serial value or a standard date string
	 * @param	long	$endDate		Excel date serial value or a standard date string
	 * @param	boolean	$method			US or European Method
	 * @return	long	PHP date/time serial
	 */
	public static function DAYS360($startDate = 0, $endDate = 0, $method = false) {
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);

		if (is_string($startDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($endDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Execute function
		$PHPStartDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($startDate);
		$startDay = $PHPStartDateObject->format('j');
		$startMonth = $PHPStartDateObject->format('n');
		$startYear = $PHPStartDateObject->format('Y');

		$PHPEndDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
		$endDay = $PHPEndDateObject->format('j');
		$endMonth = $PHPEndDateObject->format('n');
		$endYear = $PHPEndDateObject->format('Y');

		return self::_dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, !$method);
	}	//	function DAYS360()


	/**
	 *	YEARFRAC
	 *
	 *	Calculates the fraction of the year represented by the number of whole days between two dates (the start_date and the
	 *	end_date). Use the YEARFRAC worksheet function to identify the proportion of a whole year's benefits or obligations
	 *	to assign to a specific term.
	 *
	 *	@param	mixed	$startDate		Excel date serial value (float), PHP date timestamp (integer) or date object, or a standard date string
	 *	@param	mixed	$endDate		Excel date serial value (float), PHP date timestamp (integer) or date object, or a standard date string
	 *	@param	integer	$method			Method used for the calculation
	 *										0 or omitted	US (NASD) 30/360
	 *										1				Actual/actual
	 *										2				Actual/360
	 *										3				Actual/365
	 *										4				European 30/360
	 *	@return	float	fraction of the year
	 */
	public static function YEARFRAC($startDate = 0, $endDate = 0, $method = 0) {
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
		$method		= PHPExcel_Calculation_Functions::flattenSingleValue($method);

		if (is_string($startDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		if (is_string($endDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (((is_numeric($method)) && (!is_string($method))) || ($method == '')) {
			switch($method) {
				case 0	:
					return self::DAYS360($startDate,$endDate) / 360;
					break;
				case 1	:
					$days = self::DATEDIF($startDate,$endDate);
					$startYear = self::YEAR($startDate);
					$endYear = self::YEAR($endDate);
					$years = $endYear - $startYear + 1;
					$leapDays = 0;
					if ($years == 1) {
						if (self::_isLeapYear($endYear)) {
							$startMonth = self::MONTHOFYEAR($startDate);
							$endMonth = self::MONTHOFYEAR($endDate);
							$endDay = self::DAYOFMONTH($endDate);
							if (($startMonth < 3) ||
								(($endMonth * 100 + $endDay) >= (2 * 100 + 29))) {
				     			$leapDays += 1;
							}
						}
					} else {
						for($year = $startYear; $year <= $endYear; ++$year) {
							if ($year == $startYear) {
								$startMonth = self::MONTHOFYEAR($startDate);
								$startDay = self::DAYOFMONTH($startDate);
								if ($startMonth < 3) {
									$leapDays += (self::_isLeapYear($year)) ? 1 : 0;
								}
							} elseif($year == $endYear) {
								$endMonth = self::MONTHOFYEAR($endDate);
								$endDay = self::DAYOFMONTH($endDate);
								if (($endMonth * 100 + $endDay) >= (2 * 100 + 29)) {
									$leapDays += (self::_isLeapYear($year)) ? 1 : 0;
								}
							} else {
								$leapDays += (self::_isLeapYear($year)) ? 1 : 0;
							}
						}
						if ($years == 2) {
							if (($leapDays == 0) && (self::_isLeapYear($startYear)) && ($days > 365)) {
								$leapDays = 1;
							} elseif ($days < 366) {
								$years = 1;
							}
						}
						$leapDays /= $years;
					}
					return $days / (365 + $leapDays);
					break;
				case 2	:
					return self::DATEDIF($startDate,$endDate) / 360;
					break;
				case 3	:
					return self::DATEDIF($startDate,$endDate) / 365;
					break;
				case 4	:
					return self::DAYS360($startDate,$endDate,True) / 360;
					break;
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function YEARFRAC()


	/**
	 * NETWORKDAYS
	 *
	 * @param	mixed				Start date
	 * @param	mixed				End date
	 * @param	array of mixed		Optional Date Series
	 * @return	long	Interval between the dates
	 */
	public static function NETWORKDAYS($startDate,$endDate) {
		//	Retrieve the mandatory start and end date that are referenced in the function definition
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDate	= PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
		//	Flush the mandatory start and end date that are referenced in the function definition, and get the optional days
		$dateArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		array_shift($dateArgs);
		array_shift($dateArgs);

		//	Validate the start and end dates
		if (is_string($startDate = $sDate = self::_getDateValue($startDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$startDate = (float) floor($startDate);
		if (is_string($endDate = $eDate = self::_getDateValue($endDate))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$endDate = (float) floor($endDate);

		if ($sDate > $eDate) {
			$startDate = $eDate;
			$endDate = $sDate;
		}

		// Execute function
		$startDoW = 6 - self::DAYOFWEEK($startDate,2);
		if ($startDoW < 0) { $startDoW = 0; }
		$endDoW = self::DAYOFWEEK($endDate,2);
		if ($endDoW >= 6) { $endDoW = 0; }

		$wholeWeekDays = floor(($endDate - $startDate) / 7) * 5;
		$partWeekDays = $endDoW + $startDoW;
		if ($partWeekDays > 5) {
			$partWeekDays -= 5;
		}

		//	Test any extra holiday parameters
		$holidayCountedArray = array();
		foreach ($dateArgs as $holidayDate) {
			if (is_string($holidayDate = self::_getDateValue($holidayDate))) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
			if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
				if ((self::DAYOFWEEK($holidayDate,2) < 6) && (!in_array($holidayDate,$holidayCountedArray))) {
					--$partWeekDays;
					$holidayCountedArray[] = $holidayDate;
				}
			}
		}

		if ($sDate > $eDate) {
			return 0 - ($wholeWeekDays + $partWeekDays);
		}
		return $wholeWeekDays + $partWeekDays;
	}	//	function NETWORKDAYS()


	/**
	 * WORKDAY
	 *
	 * @param	mixed				Start date
	 * @param	mixed				number of days for adjustment
	 * @param	array of mixed		Optional Date Series
	 * @return	long	Interval between the dates
	 */
	public static function WORKDAY($startDate,$endDays) {
		//	Retrieve the mandatory start date and days that are referenced in the function definition
		$startDate	= PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
		$endDays	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($endDays);
		//	Flush the mandatory start date and days that are referenced in the function definition, and get the optional days
		$dateArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		array_shift($dateArgs);
		array_shift($dateArgs);

		if ((is_string($startDate = self::_getDateValue($startDate))) || (!is_numeric($endDays))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$startDate = (float) floor($startDate);
		//	If endDays is 0, we always return startDate
		if ($endDays == 0) { return $startDate; }

		$decrementing = ($endDays < 0) ? True : False;

		//	Adjust the start date if it falls over a weekend

		$startDoW = self::DAYOFWEEK($startDate,3);
		if (self::DAYOFWEEK($startDate,3) >= 5) {
			$startDate += ($decrementing) ? -$startDoW + 4: 7 - $startDoW;
			($decrementing) ? $endDays++ : $endDays--;
		}

		//	Add endDays
		$endDate = (float) $startDate + (intval($endDays / 5) * 7) + ($endDays % 5);

		//	Adjust the calculated end date if it falls over a weekend
		$endDoW = self::DAYOFWEEK($endDate,3);
		if ($endDoW >= 5) {
			$endDate += ($decrementing) ? -$endDoW + 4: 7 - $endDoW;
		}

		//	Test any extra holiday parameters
		if (count($dateArgs) > 0) {
			$holidayCountedArray = $holidayDates = array();
			foreach ($dateArgs as $holidayDate) {
				if ((!is_null($holidayDate)) && (trim($holidayDate) > '')) {
					if (is_string($holidayDate = self::_getDateValue($holidayDate))) {
						return PHPExcel_Calculation_Functions::VALUE();
					}
					if (self::DAYOFWEEK($holidayDate,3) < 5) {
						$holidayDates[] = $holidayDate;
					}
				}
			}
			if ($decrementing) {
				rsort($holidayDates, SORT_NUMERIC);
			} else {
				sort($holidayDates, SORT_NUMERIC);
			}
			foreach ($holidayDates as $holidayDate) {
				if ($decrementing) {
					if (($holidayDate <= $startDate) && ($holidayDate >= $endDate)) {
						if (!in_array($holidayDate,$holidayCountedArray)) {
							--$endDate;
							$holidayCountedArray[] = $holidayDate;
						}
					}
				} else {
					if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
						if (!in_array($holidayDate,$holidayCountedArray)) {
							++$endDate;
							$holidayCountedArray[] = $holidayDate;
						}
					}
				}
				//	Adjust the calculated end date if it falls over a weekend
				$endDoW = self::DAYOFWEEK($endDate,3);
				if ($endDoW >= 5) {
					$endDate += ($decrementing) ? -$endDoW + 4: 7 - $endDoW;
				}

			}
		}

		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) $endDate;
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP($endDate);
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
					break;
		}
	}	//	function WORKDAY()


	/**
	 * DAYOFMONTH
	 *
	 * @param	long	$dateValue		Excel date serial value or a standard date string
	 * @return	int		Day
	 */
	public static function DAYOFMONTH($dateValue = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue == 0.0) {
			return 0;
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);

		return (int) $PHPDateObject->format('j');
	}	//	function DAYOFMONTH()


	/**
	 * DAYOFWEEK
	 *
	 * @param	long	$dateValue		Excel date serial value or a standard date string
	 * @return	int		Day
	 */
	public static function DAYOFWEEK($dateValue = 1, $style = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$style		= floor(PHPExcel_Calculation_Functions::flattenSingleValue($style));

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
		$DoW = $PHPDateObject->format('w');

		$firstDay = 1;
		switch ($style) {
			case 1: ++$DoW;
					break;
			case 2: if ($DoW == 0) { $DoW = 7; }
					break;
			case 3: if ($DoW == 0) { $DoW = 7; }
					$firstDay = 0;
					--$DoW;
					break;
			default:
		}
		if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL) {
			//	Test for Excel's 1900 leap year, and introduce the error as required
			if (($PHPDateObject->format('Y') == 1900) && ($PHPDateObject->format('n') <= 2)) {
				--$DoW;
				if ($DoW < $firstDay) {
					$DoW += 7;
				}
			}
		}

		return (int) $DoW;
	}	//	function DAYOFWEEK()


	/**
	 * WEEKOFYEAR
	 *
	 * @param	long	$dateValue		Excel date serial value or a standard date string
	 * @param	boolean	$method			Week begins on Sunday or Monday
	 * @return	int		Week Number
	 */
	public static function WEEKOFYEAR($dateValue = 1, $method = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$method		= floor(PHPExcel_Calculation_Functions::flattenSingleValue($method));

		if (!is_numeric($method)) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif (($method < 1) || ($method > 2)) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
		$dayOfYear = $PHPDateObject->format('z');
		$dow = $PHPDateObject->format('w');
		$PHPDateObject->modify('-'.$dayOfYear.' days');
		$dow = $PHPDateObject->format('w');
		$daysInFirstWeek = 7 - (($dow + (2 - $method)) % 7);
		$dayOfYear -= $daysInFirstWeek;
		$weekOfYear = ceil($dayOfYear / 7) + 1;

		return (int) $weekOfYear;
	}	//	function WEEKOFYEAR()


	/**
	 * MONTHOFYEAR
	 *
	 * @param	long	$dateValue		Excel date serial value or a standard date string
	 * @return	int		Month
	 */
	public static function MONTHOFYEAR($dateValue = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);

		return (int) $PHPDateObject->format('n');
	}	//	function MONTHOFYEAR()


	/**
	 * YEAR
	 *
	 * @param	long	$dateValue		Excel date serial value or a standard date string
	 * @return	int		Year
	 */
	public static function YEAR($dateValue = 1) {
		$dateValue	= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($dateValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		// Execute function
		$PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);

		return (int) $PHPDateObject->format('Y');
	}	//	function YEAR()


	/**
	 * HOUROFDAY
	 *
	 * @param	mixed	$timeValue		Excel time serial value or a standard time string
	 * @return	int		Hour
	 */
	public static function HOUROFDAY($timeValue = 0) {
		$timeValue	= PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);

		if (!is_numeric($timeValue)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$testVal = strtok($timeValue,'/-: ');
				if (strlen($testVal) < strlen($timeValue)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
			$timeValue = self::_getTimeValue($timeValue);
			if (is_string($timeValue)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		// Execute function
		if ($timeValue >= 1) {
			$timeValue = fmod($timeValue,1);
		} elseif ($timeValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);

		return (int) gmdate('G',$timeValue);
	}	//	function HOUROFDAY()


	/**
	 * MINUTEOFHOUR
	 *
	 * @param	long	$timeValue		Excel time serial value or a standard time string
	 * @return	int		Minute
	 */
	public static function MINUTEOFHOUR($timeValue = 0) {
		$timeValue = $timeTester	= PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);

		if (!is_numeric($timeValue)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$testVal = strtok($timeValue,'/-: ');
				if (strlen($testVal) < strlen($timeValue)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
			$timeValue = self::_getTimeValue($timeValue);
			if (is_string($timeValue)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		// Execute function
		if ($timeValue >= 1) {
			$timeValue = fmod($timeValue,1);
		} elseif ($timeValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);

		return (int) gmdate('i',$timeValue);
	}	//	function MINUTEOFHOUR()


	/**
	 * SECONDOFMINUTE
	 *
	 * @param	long	$timeValue		Excel time serial value or a standard time string
	 * @return	int		Second
	 */
	public static function SECONDOFMINUTE($timeValue = 0) {
		$timeValue	= PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);

		if (!is_numeric($timeValue)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
				$testVal = strtok($timeValue,'/-: ');
				if (strlen($testVal) < strlen($timeValue)) {
					return PHPExcel_Calculation_Functions::VALUE();
				}
			}
			$timeValue = self::_getTimeValue($timeValue);
			if (is_string($timeValue)) {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		// Execute function
		if ($timeValue >= 1) {
			$timeValue = fmod($timeValue,1);
		} elseif ($timeValue < 0.0) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);

		return (int) gmdate('s',$timeValue);
	}	//	function SECONDOFMINUTE()


	/**
	 * EDATE
	 *
	 * Returns the serial number that represents the date that is the indicated number of months before or after a specified date
	 * (the start_date). Use EDATE to calculate maturity dates or due dates that fall on the same day of the month as the date of issue.
	 *
	 * @param	long	$dateValue				Excel date serial value or a standard date string
	 * @param	int		$adjustmentMonths		Number of months to adjust by
	 * @return	long	Excel date serial value
	 */
	public static function EDATE($dateValue = 1, $adjustmentMonths = 0) {
		$dateValue			= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$adjustmentMonths	= floor(PHPExcel_Calculation_Functions::flattenSingleValue($adjustmentMonths));

		if (!is_numeric($adjustmentMonths)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Execute function
		$PHPDateObject = self::_adjustDateByMonths($dateValue,$adjustmentMonths);

		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) PHPExcel_Shared_Date::PHPToExcel($PHPDateObject);
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::PHPToExcel($PHPDateObject));
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return $PHPDateObject;
					break;
		}
	}	//	function EDATE()


	/**
	 * EOMONTH
	 *
	 * Returns the serial number for the last day of the month that is the indicated number of months before or after start_date.
	 * Use EOMONTH to calculate maturity dates or due dates that fall on the last day of the month.
	 *
	 * @param	long	$dateValue			Excel date serial value or a standard date string
	 * @param	int		$adjustmentMonths	Number of months to adjust by
	 * @return	long	Excel date serial value
	 */
	public static function EOMONTH($dateValue = 1, $adjustmentMonths = 0) {
		$dateValue			= PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
		$adjustmentMonths	= floor(PHPExcel_Calculation_Functions::flattenSingleValue($adjustmentMonths));

		if (!is_numeric($adjustmentMonths)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (is_string($dateValue = self::_getDateValue($dateValue))) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// Execute function
		$PHPDateObject = self::_adjustDateByMonths($dateValue,$adjustmentMonths+1);
		$adjustDays = (int) $PHPDateObject->format('d');
		$adjustDaysString = '-'.$adjustDays.' days';
		$PHPDateObject->modify($adjustDaysString);

		switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
			case PHPExcel_Calculation_Functions::RETURNDATE_EXCEL :
					return (float) PHPExcel_Shared_Date::PHPToExcel($PHPDateObject);
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC :
					return (integer) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::PHPToExcel($PHPDateObject));
					break;
			case PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT :
					return $PHPDateObject;
					break;
		}
	}	//	function EOMONTH()

}	//	class PHPExcel_Calculation_DateTime
