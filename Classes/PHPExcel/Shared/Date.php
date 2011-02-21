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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Shared_Date
 *
 * @category   PHPExcel
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Date
{
	/** constants */
	const CALENDAR_WINDOWS_1900 = 1900;		//	Base date of 1st Jan 1900 = 1.0
	const CALENDAR_MAC_1904 = 1904;			//	Base date of 2nd Jan 1904 = 1.0

	private static $ExcelBaseDate	= self::CALENDAR_WINDOWS_1900;

	public static $dateTimeObjectType	= 'DateTime';


	/**
	 * Set the Excel calendar (Windows 1900 or Mac 1904)
	 *
	 * @param	 integer	$baseDate			Excel base date
	 * @return	 boolean						Success or failure
	 */
	public static function setExcelCalendar($baseDate) {
		if (($baseDate == self::CALENDAR_WINDOWS_1900) ||
			($baseDate == self::CALENDAR_MAC_1904)) {
			self::$ExcelBaseDate = $baseDate;
			return True;
		}
		return False;
	}	//	function setExcelCalendar()


	/**
	 * Return the Excel calendar (Windows 1900 or Mac 1904)
	 *
	 * @return	 integer	$baseDate			Excel base date
	 */
	public static function getExcelCalendar() {
		return self::$ExcelBaseDate;
	}	//	function getExcelCalendar()


	/**
	 * Convert a date from Excel to PHP
	 *
	 * @param	 long	 $dateValue		Excel date/time value
	 * @return	 long					PHP serialized date/time
	 */
	public static function ExcelToPHP($dateValue = 0) {
		if (self::$ExcelBaseDate == self::CALENDAR_WINDOWS_1900) {
			$myExcelBaseDate = 25569;
			//	Adjust for the spurious 29-Feb-1900 (Day 60)
			if ($dateValue < 60) {
				--$myExcelBaseDate;
			}
		} else {
			$myExcelBaseDate = 24107;
		}

		// Perform conversion
		if ($dateValue >= 1) {
			$utcDays = $dateValue - $myExcelBaseDate;
			$returnValue = round($utcDays * 24 * 60 * 60);
			if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
				$returnValue = (integer) $returnValue;
			}
		} else {
			$hours = round($dateValue * 24);
			$mins = round($dateValue * 24 * 60) - round($hours * 60);
			$secs = round($dateValue * 24 * 60 * 60) - round($hours * 60 * 60) - round($mins * 60);
			$returnValue = (integer) gmmktime($hours, $mins, $secs);
		}

		// Return
		return $returnValue;
	}	//	function ExcelToPHP()


	/**
	 * Convert a date from Excel to a PHP Date/Time object
	 *
	 * @param	 long	 $dateValue		Excel date/time value
	 * @return	 long					PHP date/time object
	 */
	public static function ExcelToPHPObject($dateValue = 0) {
		$dateTime = self::ExcelToPHP($dateValue);
		$days = floor($dateTime / 86400);
		$time = round((($dateTime / 86400) - $days) * 86400);
		$hours = round($time / 3600);
		$minutes = round($time / 60) - ($hours * 60);
		$seconds = round($time) - ($hours * 3600) - ($minutes * 60);

		$dateObj = date_create('1-Jan-1970+'.$days.' days');
		$dateObj->setTime($hours,$minutes,$seconds);

		return $dateObj;
	}	//	function ExcelToPHPObject()


	/**
	 * Convert a date from PHP to Excel
	 *
	 * @param	 mixed		$dateValue	PHP serialized date/time or date object
	 * @return	 mixed					Excel date/time value
	 *										or boolean False on failure
	 */
	public static function PHPToExcel($dateValue = 0) {
		$saveTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$retValue = False;
		if ((is_object($dateValue)) && ($dateValue instanceof self::$dateTimeObjectType)) {
			$retValue = self::FormattedPHPToExcel( $dateValue->format('Y'), $dateValue->format('m'), $dateValue->format('d'),
												   $dateValue->format('H'), $dateValue->format('i'), $dateValue->format('s')
												 );
		} elseif (is_numeric($dateValue)) {
			$retValue = self::FormattedPHPToExcel( date('Y',$dateValue), date('m',$dateValue), date('d',$dateValue),
												   date('H',$dateValue), date('i',$dateValue), date('s',$dateValue)
												 );
		}
		date_default_timezone_set($saveTimeZone);

		return $retValue;
	}	//	function PHPToExcel()


	/**
	 * FormattedPHPToExcel
	 *
	 * @param	long	$year
	 * @param	long	$month
	 * @param	long	$day
	 * @param	long	$hours
	 * @param	long	$minutes
	 * @param	long	$seconds
	 * @return  long				Excel date/time value
	 */
	public static function FormattedPHPToExcel($year, $month, $day, $hours=0, $minutes=0, $seconds=0) {
		if (self::$ExcelBaseDate == self::CALENDAR_WINDOWS_1900) {
			//
			//	Fudge factor for the erroneous fact that the year 1900 is treated as a Leap Year in MS Excel
			//	This affects every date following 28th February 1900
			//
			$excel1900isLeapYear = True;
			if (($year == 1900) && ($month <= 2)) { $excel1900isLeapYear = False; }
			$myExcelBaseDate = 2415020;
		} else {
			$myExcelBaseDate = 2416481;
			$excel1900isLeapYear = False;
		}

		//	Julian base date Adjustment
		if ($month > 2) {
			$month = $month - 3;
		} else {
			$month = $month + 9;
			--$year;
		}

		//	Calculate the Julian Date, then subtract the Excel base date (JD 2415020 = 31-Dec-1899 Giving Excel Date of 0)
		$century = substr($year,0,2);
		$decade = substr($year,2,2);
		$excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $myExcelBaseDate + $excel1900isLeapYear;

		$excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

		return (float) $excelDate + $excelTime;
	}	//	function FormattedPHPToExcel()


	/**
	 * Is a given cell a date/time?
	 *
	 * @param	 PHPExcel_Cell	$pCell
	 * @return	 boolean
	 */
	public static function isDateTime(PHPExcel_Cell $pCell) {
		return self::isDateTimeFormat($pCell->getParent()->getStyle($pCell->getCoordinate())->getNumberFormat());
	}	//	function isDateTime()


	/**
	 * Is a given number format a date/time?
	 *
	 * @param	 PHPExcel_Style_NumberFormat	$pFormat
	 * @return	 boolean
	 */
	public static function isDateTimeFormat(PHPExcel_Style_NumberFormat $pFormat) {
		return self::isDateTimeFormatCode($pFormat->getFormatCode());
	}	//	function isDateTimeFormat()


	private static	$possibleDateFormatCharacters = 'ymdHs';

	/**
	 * Is a given number format code a date/time?
	 *
	 * @param	 string	$pFormatCode
	 * @return	 boolean
	 */
	public static function isDateTimeFormatCode($pFormatCode = '') {
		// Switch on formatcode
		switch ($pFormatCode) {
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_MYMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME1:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME6:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME7:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME8:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX16:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX22:
				return true;
		}

		//	Typically number, currency or accounting (or occasionally fraction) formats
		if ((substr($pFormatCode,0,1) == '_') || (substr($pFormatCode,0,2) == '0 ')) {
			return false;
		}
		// Try checking for any of the date formatting characters that don't appear within square braces
		if (preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i',$pFormatCode)) {
			//	We might also have a format mask containing quoted strings...
			//		we don't want to test for any of our characters within the quoted blocks
			if (strpos($pFormatCode,'"') !== false) {
				$i = false;
				foreach(explode('"',$pFormatCode) as $subVal) {
					//	Only test in alternate array entries (the non-quoted blocks)
					if (($i = !$i) && (preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i',$subVal))) {
						return true;
					}
				}
				return false;
			}
			return true;
		}

		// No date...
		return false;
	}	//	function isDateTimeFormatCode()


	/**
	 * Convert a date/time string to Excel time
	 *
	 * @param	string	$dateValue		Examples: '2009-12-31', '2009-12-31 15:59', '2009-12-31 15:59:10'
	 * @return	float|false		Excel date/time serial value
	 */
	public static function stringToExcel($dateValue = '') {
		if (strlen($dateValue) < 2)
			return false;
		if (!preg_match('/^(\d{1,4}[ \.\/\-][A-Z]{3,9}([ \.\/\-]\d{1,4})?|[A-Z]{3,9}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?|\d{1,4}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?)( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu', $dateValue))
			return false;

		$dateValueNew = PHPExcel_Calculation_DateTime::DATEVALUE($dateValue);

		if ($dateValueNew === PHPExcel_Calculation_Functions::VALUE()) {
			return false;
		} else {
			if (strpos($dateValue, ':') !== false) {
				$timeValue = PHPExcel_Calculation_DateTime::TIMEVALUE($dateValue);
				if ($timeValue === PHPExcel_Calculation_Functions::VALUE()) {
					return false;
				}
				$dateValueNew += $timeValue;
			}
			return $dateValueNew;
		}


	}

}
