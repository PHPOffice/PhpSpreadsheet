<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Jesus M. Castagnetto <jmcastagnetto@php.net>                |
// +----------------------------------------------------------------------+
//
// $Id: Stats.php,v 1.15 2003/06/01 11:40:30 jmcastagnetto Exp $
//

include_once 'PEAR.php';

/**
* @package Math_Stats
*/

// Constants for defining the statistics to calculate /*{{{*/
/**
* STATS_BASIC to generate the basic descriptive statistics
*/
define('STATS_BASIC', 1);
/**
* STATS_FULL to generate also higher moments, mode, median, etc.
*/
define('STATS_FULL', 2);
/*}}}*/

// Constants describing the data set format /*{{{*/
/**
* STATS_DATA_SIMPLE for an array of numeric values. This is the default.
* e.g. $data = array(2,3,4,5,1,1,6);
*/
define('STATS_DATA_SIMPLE', 0);
/**
* STATS_DATA_CUMMULATIVE for an associative array of frequency values,
* where in each array entry, the index is the data point and the
* value the count (frequency):
* e.g. $data = array(3=>4, 2.3=>5, 1.25=>6, 0.5=>3)
*/
define('STATS_DATA_CUMMULATIVE', 1);
/*}}}*/

// Constants defining how to handle nulls /*{{{*/
/**
* STATS_REJECT_NULL, reject data sets with null values. This is the default.
* Any non-numeric value is considered a null in this context.
*/
define('STATS_REJECT_NULL', -1);
/**
* STATS_IGNORE_NULL, ignore null values and prune them from the data.
* Any non-numeric value is considered a null in this context.
*/
define('STATS_IGNORE_NULL', -2);
/**
* STATS_USE_NULL_AS_ZERO, assign the value of 0 (zero) to null values.
* Any non-numeric value is considered a null in this context.
*/
define('STATS_USE_NULL_AS_ZERO', -3);
/*}}}*/

/**
* A class to calculate descriptive statistics from a data set.
* Data sets can be simple arrays of data, or a cummulative hash.
* The second form is useful when passing large data set,
* for example the data set:
*
* <pre>
* $data1 = array (1,2,1,1,1,1,3,3,4.1,3,2,2,4.1,1,1,2,3,3,2,2,1,1,2,2);
* </pre>
*
* can be epxressed more compactly as:
*
* <pre>
* $data2 = array('1'=>9, '2'=>8, '3'=>5, '4.1'=>2);
* </pre>
*
* Example of use:
*
* <pre>
* include_once 'Math/Stats.php';
* $s = new Math_Stats();
* $s->setData($data1);
* // or
* // $s->setData($data2, STATS_DATA_CUMMULATIVE);
* $stats = $s->calcBasic();
* echo 'Mean: '.$stats['mean'].' StDev: '.$stats['stdev'].' <br />\n';
*
* // using data with nulls
* // first ignoring them:
* $data3 = array(1.2, 'foo', 2.4, 3.1, 4.2, 3.2, null, 5.1, 6.2);
* $s->setNullOption(STATS_IGNORE_NULL);
* $s->setData($data3);
* $stats3 = $s->calcFull();
*
* // and then assuming nulls == 0
* $s->setNullOption(STATS_USE_NULL_AS_ZERO);
* $s->setData($data3);
* $stats3 = $s->calcFull();
* </pre>
*
* Originally this class was part of NumPHP (Numeric PHP package)
*
* @author  Jesus M. Castagnetto <jmcastagnetto@php.net>
* @version 0.8
* @access  public
* @package Math_Stats
*/
class Base {/*{{{*/
    // properties /*{{{*/

    /**
     * The simple or cummulative data set.
     * Null by default.
     *
     * @access  private
     * @var array
     */
    public $_data = null;

    /**
     * Expanded data set. Only set when cummulative data
     * is being used. Null by default.
     *
     * @access  private
     * @var array
     */
    public $_dataExpanded = null;

    /**
     * Flag for data type, one of STATS_DATA_SIMPLE or
     * STATS_DATA_CUMMULATIVE. Null by default.
     *
     * @access  private
     * @var int
     */
    public $_dataOption = null;

    /**
     * Flag for null handling options. One of STATS_REJECT_NULL,
     * STATS_IGNORE_NULL or STATS_USE_NULL_AS_ZERO
     *
     * @access  private
     * @var int
     */
    public $_nullOption;

    /**
     * Array for caching result values, should be reset
     * when using setData()
     *
     * @access private
     * @var array
     */
    public $_calculatedValues = array();

    /*}}}*/

    /**
     * Constructor for the class
     *
     * @access  public
     * @param   optional    int $nullOption how to handle null values
     * @return  object  Math_Stats
     */
    function Math_Stats($nullOption=STATS_REJECT_NULL) {/*{{{*/
        $this->_nullOption = $nullOption;
    }/*}}}*/

    /**
     * Sets and verifies the data, checking for nulls and using
     * the current null handling option
     *
     * @access public
     * @param   array   $arr    the data set
     * @param   optional    int $opt    data format: STATS_DATA_CUMMULATIVE or STATS_DATA_SIMPLE (default)
     * @return  mixed   true on success, a PEAR_Error object otherwise
     */
    function setData($arr, $opt=STATS_DATA_SIMPLE) {/*{{{*/
        if (!is_array($arr)) {
            return PEAR::raiseError('invalid data, an array of numeric data was expected');
        }
        $this->_data = null;
        $this->_dataExpanded = null;
        $this->_dataOption = null;
        $this->_calculatedValues = array();
        if ($opt == STATS_DATA_SIMPLE) {
            $this->_dataOption = $opt;
            $this->_data = array_values($arr);
        } else if ($opt == STATS_DATA_CUMMULATIVE) {
            $this->_dataOption = $opt;
            $this->_data = $arr;
            $this->_dataExpanded = array();
        }
        return $this->_validate();
    }/*}}}*/

    /**
     * Returns the data which might have been modified
     * according to the current null handling options.
     *
     * @access  public
     * @param boolean $expanded whether to return a expanded list, default is false
     * @return  mixed   array of data on success, a PEAR_Error object otherwise
     * @see _validate()
     */
    function getData($expanded=false) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE && $expanded) {
            return $this->_dataExpanded;
        } else {
            return $this->_data;
        }
    }/*}}}*/

    /**
     * Sets the null handling option.
     * Must be called before assigning a new data set containing null values
     *
     * @access  public
     * @return  mixed   true on success, a PEAR_Error object otherwise
     * @see _validate()
     */
    function setNullOption($nullOption) {/*{{{*/
        if ($nullOption == STATS_REJECT_NULL
            || $nullOption == STATS_IGNORE_NULL
            || $nullOption == STATS_USE_NULL_AS_ZERO) {
            $this->_nullOption = $nullOption;
            return true;
        } else {
            return PEAR::raiseError('invalid null handling option expecting: '.
                        'STATS_REJECT_NULL, STATS_IGNORE_NULL or STATS_USE_NULL_AS_ZERO');
        }
    }/*}}}*/

    /**
     * Transforms the data by substracting each entry from the mean and
     * dividing by its standard deviation. This will reset all pre-calculated
     * values to their original (unset) defaults.
     *
     * @access public
     * @return mixed true on success, a PEAR_Error object otherwise
     * @see mean()
     * @see stDev()
     * @see setData()
     */
    function studentize() {/*{{{*/
        $mean = $this->mean();
        if (PEAR::isError($mean)) {
            return $mean;
        }
        $std = $this->stDev();
        if (PEAR::isError($std)) {
            return $std;
        }
        if ($std == 0) {
            return PEAR::raiseError('cannot studentize data, standard deviation is zero.');
        }
        $arr  = array();
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            foreach ($this->_data as $val=>$freq) {
                $newval = ($val - $mean) / $std;
                $arr["$newval"] = $freq;
            }
        } else {
            foreach ($this->_data as $val) {
                $newval = ($val - $mean) / $std;
                $arr[] = $newval;
            }
        }
        return $this->setData($arr, $this->_dataOption);
    }/*}}}*/

    /**
     * Transforms the data by substracting each entry from the mean.
     * This will reset all pre-calculated values to their original (unset) defaults.
     *
     * @access public
     * @return mixed true on success, a PEAR_Error object otherwise
     * @see mean()
     * @see setData()
     */
    function center() {/*{{{*/
        $mean = $this->mean();
        if (PEAR::isError($mean)) {
            return $mean;
        }
        $arr  = array();
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            foreach ($this->_data as $val=>$freq) {
                $newval = $val - $mean;
                $arr["$newval"] = $freq;
            }
        } else {
            foreach ($this->_data as $val) {
                $newval = $val - $mean;
                $arr[] = $newval;
            }
        }
        return $this->setData($arr, $this->_dataOption);
    }/*}}}*/

    /**
     * Calculates the basic or full statistics for the data set
     *
     * @access  public
     * @param   int $mode   one of STATS_BASIC or STATS_FULL
     * @param boolean $returnErrorObject whether the raw PEAR_Error (when true, default),
     *                  or only the error message will be returned (when false), if an error happens.
     * @return  mixed   an associative array of statistics on success, a PEAR_Error object otherwise
     * @see calcBasic()
     * @see calcFull()
     */
    function calc($mode, $returnErrorObject=true) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if ($mode == STATS_BASIC) {
            return $this->calcBasic($returnErrorObject);
        } elseif ($mode == STATS_FULL) {
            return $this->calcFull($returnErrorObject);
        } else {
            return PEAR::raiseError('incorrect mode, expected STATS_BASIC or STATS_FULL');
        }
    }/*}}}*/

    /**
     * Calculates a basic set of statistics
     *
     * @access  public
     * @param boolean $returnErrorObject whether the raw PEAR_Error (when true, default),
     *                  or only the error message will be returned (when false), if an error happens.
     * @return  mixed   an associative array of statistics on success, a PEAR_Error object otherwise
     * @see calc()
     * @see calcFull()
     */
    function calcBasic($returnErrorObject=true) {/*{{{*/
            return array (
                'min' => $this->__format($this->min(), $returnErrorObject),
                'max' => $this->__format($this->max(), $returnErrorObject),
                'sum' => $this->__format($this->sum(), $returnErrorObject),
                'sum2' => $this->__format($this->sum2(), $returnErrorObject),
                'count' => $this->__format($this->count(), $returnErrorObject),
                'mean' => $this->__format($this->mean(), $returnErrorObject),
                'stdev' => $this->__format($this->stDev(), $returnErrorObject),
                'variance' => $this->__format($this->variance(), $returnErrorObject),
                'range' => $this->__format($this->range(), $returnErrorObject)
            );
    }/*}}}*/

    /**
     * Calculates a full set of statistics
     *
     * @access  public
     * @param boolean $returnErrorObject whether the raw PEAR_Error (when true, default),
     *                  or only the error message will be returned (when false), if an error happens.
     * @return  mixed   an associative array of statistics on success, a PEAR_Error object otherwise
     * @see calc()
     * @see calcBasic()
     */
    function calcFull($returnErrorObject=true) {/*{{{*/
            return array (
                'min' => $this->__format($this->min(), $returnErrorObject),
                'max' => $this->__format($this->max(), $returnErrorObject),
                'sum' => $this->__format($this->sum(), $returnErrorObject),
                'sum2' => $this->__format($this->sum2(), $returnErrorObject),
                'count' => $this->__format($this->count(), $returnErrorObject),
                'mean' => $this->__format($this->mean(), $returnErrorObject),
                'median' => $this->__format($this->median(), $returnErrorObject),
                'mode' => $this->__format($this->mode(), $returnErrorObject),
                'midrange' => $this->__format($this->midrange(), $returnErrorObject),
                'geometric_mean' => $this->__format($this->geometricMean(), $returnErrorObject),
                'harmonic_mean' => $this->__format($this->harmonicMean(), $returnErrorObject),
                'stdev' => $this->__format($this->stDev(), $returnErrorObject),
                'absdev' => $this->__format($this->absDev(), $returnErrorObject),
                'variance' => $this->__format($this->variance(), $returnErrorObject),
                'range' => $this->__format($this->range(), $returnErrorObject),
                'std_error_of_mean' => $this->__format($this->stdErrorOfMean(), $returnErrorObject),
                'skewness' => $this->__format($this->skewness(), $returnErrorObject),
                'kurtosis' => $this->__format($this->kurtosis(), $returnErrorObject),
                'coeff_of_variation' => $this->__format($this->coeffOfVariation(), $returnErrorObject),
                'sample_central_moments' => array (
                            1 => $this->__format($this->sampleCentralMoment(1), $returnErrorObject),
                            2 => $this->__format($this->sampleCentralMoment(2), $returnErrorObject),
                            3 => $this->__format($this->sampleCentralMoment(3), $returnErrorObject),
                            4 => $this->__format($this->sampleCentralMoment(4), $returnErrorObject),
                            5 => $this->__format($this->sampleCentralMoment(5), $returnErrorObject)
                            ),
                'sample_raw_moments' => array (
                            1 => $this->__format($this->sampleRawMoment(1), $returnErrorObject),
                            2 => $this->__format($this->sampleRawMoment(2), $returnErrorObject),
                            3 => $this->__format($this->sampleRawMoment(3), $returnErrorObject),
                            4 => $this->__format($this->sampleRawMoment(4), $returnErrorObject),
                            5 => $this->__format($this->sampleRawMoment(5), $returnErrorObject)
                            ),
                'frequency' => $this->__format($this->frequency(), $returnErrorObject),
                'quartiles' => $this->__format($this->quartiles(), $returnErrorObject),
                'interquartile_range' => $this->__format($this->interquartileRange(), $returnErrorObject),
                'interquartile_mean' => $this->__format($this->interquartileMean(), $returnErrorObject),
                'quartile_deviation' => $this->__format($this->quartileDeviation(), $returnErrorObject),
                'quartile_variation_coefficient' => $this->__format($this->quartileVariationCoefficient(), $returnErrorObject),
                'quartile_skewness_coefficient' => $this->__format($this->quartileSkewnessCoefficient(), $returnErrorObject)
            );
    }/*}}}*/

    /**
     * Calculates the minimum of a data set.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the minimum value on success, a PEAR_Error object otherwise
     * @see calc()
     * @see max()
     */
    function min() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('min', $this->_calculatedValues)) {
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                $min = min(array_keys($this->_data));
            } else {
                $min = min($this->_data);
            }
            $this->_calculatedValues['min'] = $min;
        }
        return $this->_calculatedValues['min'];
    }/*}}}*/

    /**
     * Calculates the maximum of a data set.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the maximum value on success, a PEAR_Error object otherwise
     * @see calc()
     * @see min()
     */
    function max() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('max', $this->_calculatedValues)) {
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                $max = max(array_keys($this->_data));
            } else {
                $max = max($this->_data);
            }
            $this->_calculatedValues['max'] = $max;
        }
        return $this->_calculatedValues['max'];
    }/*}}}*/

    /**
     * Calculates SUM { xi }
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the sum on success, a PEAR_Error object otherwise
     * @see calc()
     * @see sum2()
     * @see sumN()
     */
    function sum() {/*{{{*/
        if (!array_key_exists('sum', $this->_calculatedValues)) {
            $sum = $this->sumN(1);
            if (PEAR::isError($sum)) {
                return $sum;
            } else {
                $this->_calculatedValues['sum'] = $sum;
            }
        }
        return $this->_calculatedValues['sum'];
    }/*}}}*/

    /**
     * Calculates SUM { (xi)^2 }
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the sum on success, a PEAR_Error object otherwise
     * @see calc()
     * @see sum()
     * @see sumN()
     */
    function sum2() {/*{{{*/
        if (!array_key_exists('sum2', $this->_calculatedValues)) {
            $sum2 = $this->sumN(2);
            if (PEAR::isError($sum2)) {
                return $sum2;
            } else {
                $this->_calculatedValues['sum2'] = $sum2;
            }
        }
        return $this->_calculatedValues['sum2'];
    }/*}}}*/

    /**
     * Calculates SUM { (xi)^n }
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @param   numeric $n  the exponent
     * @return  mixed   the sum on success, a PEAR_Error object otherwise
     * @see calc()
     * @see sum()
     * @see sum2()
     */
    function sumN($n) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        $sumN = 0;
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            foreach($this->_data as $val=>$freq) {
                $sumN += $freq * pow((double)$val, (double)$n);
            }
        } else {
            foreach($this->_data as $val) {
                $sumN += pow((double)$val, (double)$n);
            }
        }
        return $sumN;
    }/*}}}*/

    /**
     * Calculates PROD { (xi) }, (the product of all observations)
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the product on success, a PEAR_Error object otherwise
     * @see productN()
     */
    function product() {/*{{{*/
        if (!array_key_exists('product', $this->_calculatedValues)) {
            $product = $this->productN(1);
            if (PEAR::isError($product)) {
                return $product;
            } else {
                $this->_calculatedValues['product'] = $product;
            }
        }
        return $this->_calculatedValues['product'];
    }/*}}}*/

    /**
     * Calculates PROD { (xi)^n }, which is the product of all observations
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @param   numeric $n  the exponent
     * @return  mixed   the product on success, a PEAR_Error object otherwise
     * @see product()
     */
    function productN($n) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        $prodN = 1.0;
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            foreach($this->_data as $val=>$freq) {
                if ($val == 0) {
                    return 0.0;
                }
                $prodN *= $freq * pow((double)$val, (double)$n);
            }
        } else {
            foreach($this->_data as $val) {
                if ($val == 0) {
                    return 0.0;
                }
                $prodN *= pow((double)$val, (double)$n);
            }
        }
        return $prodN;

    }/*}}}*/

    /**
     * Calculates the number of data points in the set
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the count on success, a PEAR_Error object otherwise
     * @see calc()
     */
    function count() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('count', $this->_calculatedValues)) {
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                $count = count($this->_dataExpanded);
            } else {
                $count = count($this->_data);
            }
            $this->_calculatedValues['count'] = $count;
        }
        return $this->_calculatedValues['count'];
    }/*}}}*/

    /**
     * Calculates the mean (average) of the data points in the set
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the mean value on success, a PEAR_Error object otherwise
     * @see calc()
     * @see sum()
     * @see count()
     */
    function mean() {/*{{{*/
        if (!array_key_exists('mean', $this->_calculatedValues)) {
            $sum = $this->sum();
            if (PEAR::isError($sum)) {
                return $sum;
            }
            $count = $this->count();
            if (PEAR::isError($count)) {
                return $count;
            }
            $this->_calculatedValues['mean'] = $sum / $count;
        }
        return $this->_calculatedValues['mean'];
    }/*}}}*/

    /**
     * Calculates the range of the data set = max - min
     *
     * @access public
     * @return mixed the value of the range on success, a PEAR_Error object otherwise.
     */
    function range() {/*{{{*/
        if (!array_key_exists('range', $this->_calculatedValues)) {
            $min = $this->min();
            if (PEAR::isError($min)) {
                return $min;
            }
            $max = $this->max();
            if (PEAR::isError($max)) {
                return $max;
            }
            $this->_calculatedValues['range'] = $max - $min;
        }
        return $this->_calculatedValues['range'];

    }/*}}}*/

    /**
     * Calculates the variance (unbiased) of the data points in the set
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the variance value on success, a PEAR_Error object otherwise
     * @see calc()
     * @see __sumdiff()
     * @see count()
     */
    function variance() {/*{{{*/
        if (!array_key_exists('variance', $this->_calculatedValues)) {
            $variance = $this->__calcVariance();
            if (PEAR::isError($variance)) {
                return $variance;
            }
            $this->_calculatedValues['variance'] = $variance;
        }
        return $this->_calculatedValues['variance'];
    }/*}}}*/

    /**
     * Calculates the standard deviation (unbiased) of the data points in the set
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the standard deviation on success, a PEAR_Error object otherwise
     * @see calc()
     * @see variance()
     */
    function stDev() {/*{{{*/
        if (!array_key_exists('stDev', $this->_calculatedValues)) {
            $variance = $this->variance();
            if (PEAR::isError($variance)) {
                return $variance;
            }
            $this->_calculatedValues['stDev'] = sqrt($variance);
        }
        return $this->_calculatedValues['stDev'];
    }/*}}}*/

    /**
     * Calculates the variance (unbiased) of the data points in the set
     * given a fixed mean (average) value. Not used in calcBasic(), calcFull()
     * or calc().
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @param   numeric $mean   the fixed mean value
     * @return  mixed   the variance on success, a PEAR_Error object otherwise
     * @see __sumdiff()
     * @see count()
     * @see variance()
     */
    function varianceWithMean($mean) {/*{{{*/
        return $this->__calcVariance($mean);
    }/*}}}*/

    /**
     * Calculates the standard deviation (unbiased) of the data points in the set
     * given a fixed mean (average) value. Not used in calcBasic(), calcFull()
     * or calc().
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @param   numeric $mean   the fixed mean value
     * @return  mixed   the standard deviation on success, a PEAR_Error object otherwise
     * @see varianceWithMean()
     * @see stDev()
     */
    function stDevWithMean($mean) {/*{{{*/
        $varianceWM = $this->varianceWithMean($mean);
        if (PEAR::isError($varianceWM)) {
            return $varianceWM;
        }
        return sqrt($varianceWM);
    }/*}}}*/

    /**
     * Calculates the absolute deviation of the data points in the set
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the absolute deviation on success, a PEAR_Error object otherwise
     * @see calc()
     * @see __sumabsdev()
     * @see count()
     * @see absDevWithMean()
     */
    function absDev() {/*{{{*/
        if (!array_key_exists('absDev', $this->_calculatedValues)) {
            $absDev = $this->__calcAbsoluteDeviation();
            if (PEAR::isError($absdev)) {
                return $absdev;
            }
            $this->_calculatedValues['absDev'] = $absDev;
        }
        return $this->_calculatedValues['absDev'];
    }/*}}}*/

    /**
     * Calculates the absolute deviation of the data points in the set
     * given a fixed mean (average) value. Not used in calcBasic(), calcFull()
     * or calc().
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @param   numeric $mean   the fixed mean value
     * @return  mixed   the absolute deviation on success, a PEAR_Error object otherwise
     * @see __sumabsdev()
     * @see absDev()
     */
    function absDevWithMean($mean) {/*{{{*/
        return $this->__calcAbsoluteDeviation($mean);
    }/*}}}*/

    /**
     * Calculates the skewness of the data distribution in the set
     * The skewness measures the degree of asymmetry of a distribution,
     * and is related to the third central moment of a distribution.
     * A normal distribution has a skewness = 0
     * A distribution with a tail off towards the high end of the scale
     * (positive skew) has a skewness > 0
     * A distribution with a tail off towards the low end of the scale
     * (negative skew) has a skewness < 0
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the skewness value on success, a PEAR_Error object otherwise
     * @see __sumdiff()
     * @see count()
     * @see stDev()
     * @see calc()
     */
    function skewness() {/*{{{*/
        if (!array_key_exists('skewness', $this->_calculatedValues)) {
            $count = $this->count();
            if (PEAR::isError($count)) {
                return $count;
            }
            $stDev = $this->stDev();
            if (PEAR::isError($stDev)) {
                return $stDev;
            }
            $sumdiff3 = $this->__sumdiff(3);
            if (PEAR::isError($sumdiff3)) {
                return $sumdiff3;
            }
            $this->_calculatedValues['skewness'] = ($sumdiff3 / ($count * pow($stDev, 3)));
        }
        return $this->_calculatedValues['skewness'];
    }/*}}}*/

    /**
     * Calculates the kurtosis of the data distribution in the set
     * The kurtosis measures the degrees of peakedness of a distribution.
     * It is also called the "excess" or "excess coefficient", and is
     * a normalized form of the fourth central moment of a distribution.
     * A normal distributions has kurtosis = 0
     * A narrow and peaked (leptokurtic) distribution has a
     * kurtosis > 0
     * A flat and wide (platykurtic) distribution has a kurtosis < 0
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the kurtosis value on success, a PEAR_Error object otherwise
     * @see __sumdiff()
     * @see count()
     * @see stDev()
     * @see calc()
     */
    function kurtosis() {/*{{{*/
        if (!array_key_exists('kurtosis', $this->_calculatedValues)) {
            $count = $this->count();
            if (PEAR::isError($count)) {
                return $count;
            }
            $stDev = $this->stDev();
            if (PEAR::isError($stDev)) {
                return $stDev;
            }
            $sumdiff4 = $this->__sumdiff(4);
            if (PEAR::isError($sumdiff4)) {
                return $sumdiff4;
            }
            $this->_calculatedValues['kurtosis'] = ($sumdiff4 / ($count * pow($stDev, 4))) - 3;
        }
        return $this->_calculatedValues['kurtosis'];
    }/*}}}*/

    /**
     * Calculates the median of a data set.
     * The median is the value such that half of the points are below it
     * in a sorted data set.
     * If the number of values is odd, it is the middle item.
     * If the number of values is even, is the average of the two middle items.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the median value on success, a PEAR_Error object otherwise
     * @see count()
     * @see calc()
     */
    function median() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('median', $this->_calculatedValues)) {
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                $arr =& $this->_dataExpanded;
            } else {
                $arr =& $this->_data;
            }
            $n = $this->count();
            if (PEAR::isError($n)) {
                return $n;
            }
            $h = intval($n / 2);
            if ($n % 2 == 0) {
                $median = ($arr[$h] + $arr[$h - 1]) / 2;
            } else {
                $median = $arr[$h + 1];
            }
            $this->_calculatedValues['median'] = $median;
        }
        return $this->_calculatedValues['median'];
    }/*}}}*/

    /**
     * Calculates the mode of a data set.
     * The mode is the value with the highest frequency in the data set.
     * There can be more than one mode.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   an array of mode value on success, a PEAR_Error object otherwise
     * @see frequency()
     * @see calc()
     */
    function mode() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('mode', $this->_calculatedValues)) {
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                $arr = $this->_data;
            } else {
                $arr = $this->frequency();
            }
            arsort($arr);
            $mcount = 1;
            foreach ($arr as $val=>$freq) {
                if ($mcount == 1) {
                    $mode = array($val);
                    $mfreq = $freq;
                    ++$mcount;
                    continue;
                }
                if ($mfreq == $freq)
                    $mode[] = $val;
                if ($mfreq > $freq)
                    break;
            }
            $this->_calculatedValues['mode'] = $mode;
        }
        return $this->_calculatedValues['mode'];
    }/*}}}*/

    /**
     * Calculates the midrange of a data set.
     * The midrange is the average of the minimum and maximum of the data set.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the midrange value on success, a PEAR_Error object otherwise
     * @see min()
     * @see max()
     * @see calc()
     */
    function midrange() {/*{{{*/
        if (!array_key_exists('midrange', $this->_calculatedValues)) {
            $min = $this->min();
            if (PEAR::isError($min)) {
                return $min;
            }
            $max = $this->max();
            if (PEAR::isError($max)) {
                return $max;
            }
            $this->_calculatedValues['midrange'] = (($max + $min) / 2);
        }
        return $this->_calculatedValues['midrange'];
    }/*}}}*/

    /**
     * Calculates the geometrical mean of the data points in the set
     * Handles cummulative data sets correctly
     *
     * @access public
     * @return mixed the geometrical mean value on success, a PEAR_Error object otherwise
     * @see calc()
     * @see product()
     * @see count()
     */
    function geometricMean() {/*{{{*/
        if (!array_key_exists('geometricMean', $this->_calculatedValues)) {
            $count = $this->count();
            if (PEAR::isError($count)) {
                return $count;
            }
            $prod = $this->product();
            if (PEAR::isError($prod)) {
                return $prod;
            }
            if ($prod == 0.0) {
                return 0.0;
            }
            if ($prod < 0) {
                return PEAR::raiseError('The product of the data set is negative, geometric mean undefined.');
            }
            $this->_calculatedValues['geometricMean'] = pow($prod , 1 / $count);
        }
        return $this->_calculatedValues['geometricMean'];
    }/*}}}*/

    /**
     * Calculates the harmonic mean of the data points in the set
     * Handles cummulative data sets correctly
     *
     * @access public
     * @return mixed the harmonic mean value on success, a PEAR_Error object otherwise
     * @see calc()
     * @see count()
     */
    function harmonicMean() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('harmonicMean', $this->_calculatedValues)) {
            $count = $this->count();
            if (PEAR::isError($count)) {
                return $count;
            }
            $invsum = 0.0;
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                foreach($this->_data as $val=>$freq) {
                    if ($val == 0) {
                        return PEAR::raiseError('cannot calculate a '.
                                'harmonic mean with data values of zero.');
                    }
                    $invsum += $freq / $val;
                }
            } else {
                foreach($this->_data as $val) {
                    if ($val == 0) {
                        return PEAR::raiseError('cannot calculate a '.
                                'harmonic mean with data values of zero.');
                    }
                    $invsum += 1 / $val;
                }
            }
            $this->_calculatedValues['harmonicMean'] = $count / $invsum;
        }
        return $this->_calculatedValues['harmonicMean'];
    }/*}}}*/

    /**
     * Calculates the nth central moment (m{n}) of a data set.
     *
     * The definition of a sample central moment is:
     *
     *     m{n} = 1/N * SUM { (xi - avg)^n }
     *
     * where: N = sample size, avg = sample mean.
     *
     * @access public
     * @param integer $n moment to calculate
     * @return mixed the numeric value of the moment on success, PEAR_Error otherwise
     */
    function sampleCentralMoment($n) {/*{{{*/
        if (!is_int($n) || $n < 1) {
            return PEAR::isError('moment must be a positive integer >= 1.');
        }

        if ($n == 1) {
            return 0;
        }
        $count = $this->count();
        if (PEAR::isError($count)) {
            return $count;
        }
        if ($count == 0) {
            return PEAR::raiseError("Cannot calculate {$n}th sample moment, ".
                    'there are zero data entries');
        }
        $sum = $this->__sumdiff($n);
        if (PEAR::isError($sum)) {
            return $sum;
        }
        return ($sum / $count);
    }/*}}}*/

    /**
     * Calculates the nth raw moment (m{n}) of a data set.
     *
     * The definition of a sample central moment is:
     *
     *     m{n} = 1/N * SUM { xi^n }
     *
     * where: N = sample size, avg = sample mean.
     *
     * @access public
     * @param integer $n moment to calculate
     * @return mixed the numeric value of the moment on success, PEAR_Error otherwise
     */
    function sampleRawMoment($n) {/*{{{*/
        if (!is_int($n) || $n < 1) {
            return PEAR::isError('moment must be a positive integer >= 1.');
        }

        $count = $this->count();
        if (PEAR::isError($count)) {
            return $count;
        }
        if ($count == 0) {
            return PEAR::raiseError("Cannot calculate {$n}th raw moment, ".
                    'there are zero data entries.');
        }
        $sum = $this->sumN($n);
        if (PEAR::isError($sum)) {
            return $sum;
        }
        return ($sum / $count);
    }/*}}}*/


    /**
     * Calculates the coefficient of variation of a data set.
     * The coefficient of variation measures the spread of a set of data
     * as a proportion of its mean. It is often expressed as a percentage.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   the coefficient of variation on success, a PEAR_Error object otherwise
     * @see stDev()
     * @see mean()
     * @see calc()
     */
    function coeffOfVariation() {/*{{{*/
        if (!array_key_exists('coeffOfVariation', $this->_calculatedValues)) {
            $mean = $this->mean();
            if (PEAR::isError($mean)) {
                return $mean;
            }
            if ($mean == 0.0) {
                return PEAR::raiseError('cannot calculate the coefficient '.
                        'of variation, mean of sample is zero');
            }
            $stDev = $this->stDev();
            if (PEAR::isError($stDev)) {
                return $stDev;
            }

            $this->_calculatedValues['coeffOfVariation'] = $stDev / $mean;
        }
        return $this->_calculatedValues['coeffOfVariation'];
    }/*}}}*/

    /**
     * Calculates the standard error of the mean.
     * It is the standard deviation of the sampling distribution of
     * the mean. The formula is:
     *
     * S.E. Mean = SD / (N)^(1/2)
     *
     * This formula does not assume a normal distribution, and shows
     * that the size of the standard error of the mean is inversely
     * proportional to the square root of the sample size.
     *
     * @access  public
     * @return  mixed   the standard error of the mean on success, a PEAR_Error object otherwise
     * @see stDev()
     * @see count()
     * @see calc()
     */
    function stdErrorOfMean() {/*{{{*/
        if (!array_key_exists('stdErrorOfMean', $this->_calculatedValues)) {
            $count = $this->count();
            if (PEAR::isError($count)) {
                return $count;
            }
            $stDev = $this->stDev();
            if (PEAR::isError($stDev)) {
                return $stDev;
            }
            $this->_calculatedValues['stdErrorOfMean'] = $stDev / sqrt($count);
        }
        return $this->_calculatedValues['stdErrorOfMean'];
    }/*}}}*/

    /**
     * Calculates the value frequency table of a data set.
     * Handles cummulative data sets correctly
     *
     * @access  public
     * @return  mixed   an associative array of value=>frequency items on success, a PEAR_Error object otherwise
     * @see min()
     * @see max()
     * @see calc()
     */
    function frequency() {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (!array_key_exists('frequency', $this->_calculatedValues)) {
            if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
                $freq = $this->_data;
            } else {
                $freq = array();
                foreach ($this->_data as $val) {
                    $freq["$val"]++;
                }
                ksort($freq);
            }
            $this->_calculatedValues['frequency'] = $freq;
        }
        return $this->_calculatedValues['frequency'];
    }/*}}}*/

    /**
     * The quartiles are defined as the values that divide a sorted
     * data set into four equal-sized subsets, and correspond to the
     * 25th, 50th, and 75th percentiles.
     *
     * @access public
     * @return mixed an associative array of quartiles on success, a PEAR_Error otherwise
     * @see percentile()
     */
    function quartiles() {/*{{{*/
        if (!array_key_exists('quartiles', $this->_calculatedValues)) {
            $q1 = $this->percentile(25);
            if (PEAR::isError($q1)) {
                return $q1;
            }
            $q2 = $this->percentile(50);
            if (PEAR::isError($q2)) {
                return $q2;
            }
            $q3 = $this->percentile(75);
            if (PEAR::isError($q3)) {
                return $q3;
            }
            $this->_calculatedValues['quartiles'] = array (
                                        '25' => $q1,
                                        '50' => $q2,
                                        '75' => $q3
                                        );
        }
        return $this->_calculatedValues['quartiles'];
    }/*}}}*/

    /**
     * The interquartile mean is defined as the mean of the values left
     * after discarding the lower 25% and top 25% ranked values, i.e.:
     *
     *  interquart mean = mean(<P(25),P(75)>)
     *
     *  where: P = percentile
     *
     * @todo need to double check the equation
     * @access public
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see quartiles()
     */
    function interquartileMean() {/*{{{*/
        if (!array_key_exists('interquartileMean', $this->_calculatedValues)) {
            $quart = $this->quartiles();
            if (PEAR::isError($quart)) {
                return $quart;
            }
            $q3 = $quart['75'];
            $q1 = $quart['25'];
            $sum = 0;
            $n = 0;
            foreach ($this->getData(true) as $val) {
                if ($val >= $q1 && $val <= $q3) {
                    $sum += $val;
                    ++$n;
                }
            }
            if ($n == 0) {
                return PEAR::raiseError('error calculating interquartile mean, '.
                                        'empty interquartile range of values.');
            }
            $this->_calculatedValues['interquartileMean'] = $sum / $n;
        }
        return $this->_calculatedValues['interquartileMean'];
    }/*}}}*/

    /**
     * The interquartile range is the distance between the 75th and 25th
     * percentiles. Basically the range of the middle 50% of the data set,
     * and thus is not affected by outliers or extreme values.
     *
     *  interquart range = P(75) - P(25)
     *
     *  where: P = percentile
     *
     * @access public
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see quartiles()
     */
    function interquartileRange() {/*{{{*/
        if (!array_key_exists('interquartileRange', $this->_calculatedValues)) {
            $quart = $this->quartiles();
            if (PEAR::isError($quart)) {
                return $quart;
            }
            $q3 = $quart['75'];
            $q1 = $quart['25'];
            $this->_calculatedValues['interquartileRange'] = $q3 - $q1;
        }
        return $this->_calculatedValues['interquartileRange'];
    }/*}}}*/

    /**
     * The quartile deviation is half of the interquartile range value
     *
     *  quart dev = (P(75) - P(25)) / 2
     *
     *  where: P = percentile
     *
     * @access public
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see quartiles()
     * @see interquartileRange()
     */
    function quartileDeviation() {/*{{{*/
        if (!array_key_exists('quartileDeviation', $this->_calculatedValues)) {
            $iqr = $this->interquartileRange();
            if (PEAR::isError($iqr)) {
                return $iqr;
            }
            $this->_calculatedValues['quartileDeviation'] = $iqr / 2;
        }
        return $this->_calculatedValues['quartileDeviation'];
    }/*}}}*/

    /**
     * The quartile variation coefficient is defines as follows:
     *
     *  quart var coeff = 100 * (P(75) - P(25)) / (P(75) + P(25))
     *
     *  where: P = percentile
     *
     * @todo need to double check the equation
     * @access public
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see quartiles()
     */
    function quartileVariationCoefficient() {/*{{{*/
        if (!array_key_exists('quartileVariationCoefficient', $this->_calculatedValues)) {
            $quart = $this->quartiles();
            if (PEAR::isError($quart)) {
                return $quart;
            }
            $q3 = $quart['75'];
            $q1 = $quart['25'];
            $d = $q3 - $q1;
            $s = $q3 + $q1;
            $this->_calculatedValues['quartileVariationCoefficient'] = 100 * $d / $s;
        }
        return $this->_calculatedValues['quartileVariationCoefficient'];
    }/*}}}*/

    /**
     * The quartile skewness coefficient (also known as Bowley Skewness),
     * is defined as follows:
     *
     *  quart skewness coeff = (P(25) - 2*P(50) + P(75)) / (P(75) - P(25))
     *
     *  where: P = percentile
     *
     * @todo need to double check the equation
     * @access public
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see quartiles()
     */
    function quartileSkewnessCoefficient() {/*{{{*/
        if (!array_key_exists('quartileSkewnessCoefficient', $this->_calculatedValues)) {
            $quart = $this->quartiles();
            if (PEAR::isError($quart)) {
                return $quart;
            }
            $q3 = $quart['75'];
            $q2 = $quart['50'];
            $q1 = $quart['25'];
            $d = $q3 - 2*$q2 + $q1;
            $s = $q3 - $q1;
            $this->_calculatedValues['quartileSkewnessCoefficient'] = $d / $s;
        }
        return $this->_calculatedValues['quartileSkewnessCoefficient'];
    }/*}}}*/

    /**
     * The pth percentile is the value such that p% of the a sorted data set
     * is smaller than it, and (100 - p)% of the data is larger.
     *
     * A quick algorithm to pick the appropriate value from a sorted data
     * set is as follows:
     *
     * - Count the number of values: n
     * - Calculate the position of the value in the data list: i = p * (n + 1)
     * - if i is an integer, return the data at that position
     * - if i < 1, return the minimum of the data set
     * - if i > n, return the maximum of the data set
     * - otherwise, average the entries at adjacent positions to i
     *
     * The median is the 50th percentile value.
     *
     * @todo need to double check generality of the algorithm
     *
     * @access public
     * @param numeric $p the percentile to estimate, e.g. 25 for 25th percentile
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see quartiles()
     * @see median()
     */
    function percentile($p) {/*{{{*/
        $count = $this->count();
        if (PEAR::isError($count)) {
            return $count;
        }
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            $data =& $this->_dataExpanded;
        } else {
            $data =& $this->_data;
        }
        $obsidx = $p * ($count + 1) / 100;
        if (intval($obsidx) == $obsidx) {
            return $data[($obsidx - 1)];
        } elseif ($obsidx < 1) {
            return $data[0];
        } elseif ($obsidx > $count) {
            return $data[($count - 1)];
        } else {
            $left = floor($obsidx - 1);
            $right = ceil($obsidx - 1);
            return ($data[$left] + $data[$right]) / 2;
        }
    }/*}}}*/

    // private methods

    /**
     * Utility function to calculate: SUM { (xi - mean)^n }
     *
     * @access private
     * @param   numeric $power  the exponent
     * @param   optional    double   $mean   the data set mean value
     * @return  mixed   the sum on success, a PEAR_Error object otherwise
     *
     * @see stDev()
     * @see variaceWithMean();
     * @see skewness();
     * @see kurtosis();
     */
    function __sumdiff($power, $mean=null) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (is_null($mean)) {
            $mean = $this->mean();
            if (PEAR::isError($mean)) {
                return $mean;
            }
        }
        $sdiff = 0;
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            foreach ($this->_data as $val=>$freq) {
                $sdiff += $freq * pow((double)($val - $mean), (double)$power);
            }
        } else {
            foreach ($this->_data as $val)
                $sdiff += pow((double)($val - $mean), (double)$power);
        }
        return $sdiff;
    }/*}}}*/

    /**
     * Utility function to calculate the variance with or without
     * a fixed mean
     *
     * @access private
     * @param $mean the fixed mean to use, null as default
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see variance()
     * @see varianceWithMean()
     */
    function __calcVariance($mean = null) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        $sumdiff2 = $this->__sumdiff(2, $mean);
        if (PEAR::isError($sumdiff2)) {
            return $sumdiff2;
        }
        $count = $this->count();
        if (PEAR::isError($count)) {
            return $count;
        }
        if ($count == 1) {
            return PEAR::raiseError('cannot calculate variance of a singe data point');
        }
        return  ($sumdiff2 / ($count - 1));
    }/*}}}*/

    /**
     * Utility function to calculate the absolute deviation with or without
     * a fixed mean
     *
     * @access private
     * @param $mean the fixed mean to use, null as default
     * @return mixed a numeric value on success, a PEAR_Error otherwise
     * @see absDev()
     * @see absDevWithMean()
     */
    function __calcAbsoluteDeviation($mean = null) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        $count = $this->count();
        if (PEAR::isError($count)) {
            return $count;
        }
        $sumabsdev = $this->__sumabsdev($mean);
        if (PEAR::isError($sumabsdev)) {
            return $sumabsdev;
        }
        return $sumabsdev / $count;
    }/*}}}*/

    /**
     * Utility function to calculate: SUM { | xi - mean | }
     *
     * @access  private
     * @param   optional    double   $mean   the mean value for the set or population
     * @return  mixed   the sum on success, a PEAR_Error object otherwise
     *
     * @see absDev()
     * @see absDevWithMean()
     */
    function __sumabsdev($mean=null) {/*{{{*/
        if ($this->_data == null) {
            return PEAR::raiseError('data has not been set');
        }
        if (is_null($mean)) {
            $mean = $this->mean();
        }
        $sdev = 0;
        if ($this->_dataOption == STATS_DATA_CUMMULATIVE) {
            foreach ($this->_data as $val=>$freq) {
                $sdev += $freq * abs($val - $mean);
            }
        } else {
            foreach ($this->_data as $val) {
                $sdev += abs($val - $mean);
            }
        }
        return $sdev;
    }/*}}}*/

    /**
     * Utility function to format a PEAR_Error to be used by calc(),
     * calcBasic() and calcFull()
     *
     * @access private
     * @param mixed $v value to be formatted
     * @param boolean $returnErrorObject whether the raw PEAR_Error (when true, default),
     *                  or only the error message will be returned (when false)
     * @return mixed if the value is a PEAR_Error object, and $useErrorObject
     *              is false, then a string with the error message will be returned,
     *              otherwise the value will not be modified and returned as passed.
     */
    function __format($v, $useErrorObject=true) {/*{{{*/
        if (PEAR::isError($v) && $useErrorObject == false) {
            return $v->getMessage();
        } else {
            return $v;
        }
    }/*}}}*/

    /**
     * Utility function to validate the data and modify it
     * according to the current null handling option
     *
     * @access  private
     * @return  mixed true on success, a PEAR_Error object otherwise
     *
     * @see setData()
     */
    function _validate() {/*{{{*/
        $flag = ($this->_dataOption == STATS_DATA_CUMMULATIVE);
        foreach ($this->_data as $key=>$value) {
            $d = ($flag) ? $key : $value;
            $v = ($flag) ? $value : $key;
            if (!is_numeric($d)) {
                switch ($this->_nullOption) {
                    case STATS_IGNORE_NULL :
                        unset($this->_data["$key"]);
                        break;
                    case STATS_USE_NULL_AS_ZERO:
                        if ($flag) {
                            unset($this->_data["$key"]);
                            $this->_data[0] += $v;
                        } else {
                            $this->_data[$key] = 0;
                        }
                        break;
                    case STATS_REJECT_NULL :
                    default:
                        return PEAR::raiseError('data rejected, contains NULL values');
                        break;
                }
            }
        }
        if ($flag) {
            ksort($this->_data);
            $this->_dataExpanded = array();
            foreach ($this->_data as $val=>$freq) {
                $this->_dataExpanded = array_pad($this->_dataExpanded, count($this->_dataExpanded) + $freq, $val);
            }
            sort($this->_dataExpanded);
        } else {
            sort($this->_data);
        }
        return true;
    }/*}}}*/

}/*}}}*/

// vim: ts=4:sw=4:et:
// vim6: fdl=1: fdm=marker:

?>
