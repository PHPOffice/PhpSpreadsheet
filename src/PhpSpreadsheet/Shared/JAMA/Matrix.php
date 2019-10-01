<?php

namespace PhpOffice\PhpSpreadsheet\Shared\JAMA;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

/**
 * Matrix class.
 *
 * @author Paul Meagher
 * @author Michael Bommarito
 * @author Lukasz Karapuda
 * @author Bartek Matosiuk
 *
 * @version 1.8
 *
 * @see https://math.nist.gov/javanumerics/jama/
 */
class Matrix
{
    const POLYMORPHIC_ARGUMENT_EXCEPTION = 'Invalid argument pattern for polymorphic function.';
    const ARGUMENT_TYPE_EXCEPTION = 'Invalid argument type.';
    const ARGUMENT_BOUNDS_EXCEPTION = 'Invalid argument range.';
    const MATRIX_DIMENSION_EXCEPTION = 'Matrix dimensions are not equal.';
    const ARRAY_LENGTH_EXCEPTION = 'Array length must be a multiple of m.';
    const MATRIX_SPD_EXCEPTION = 'Can only perform operation on symmetric positive definite matrix.';

    /**
     * Matrix storage.
     *
     * @var array
     */
    public $A = [];

    /**
     * Matrix row dimension.
     *
     * @var int
     */
    private $m;

    /**
     * Matrix column dimension.
     *
     * @var int
     */
    private $n;

    /**
     * Polymorphic constructor.
     *
     * As PHP has no support for polymorphic constructors, we use tricks to make our own sort of polymorphism using func_num_args, func_get_arg, and gettype. In essence, we're just implementing a simple RTTI filter and calling the appropriate constructor.
     */
    public function __construct(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                //Rectangular matrix - m x n initialized from 2D array
                case 'array':
                    $this->m = count($args[0]);
                    $this->n = count($args[0][0]);
                    $this->A = $args[0];

                    break;
                //Square matrix - n x n
                case 'integer':
                    $this->m = $args[0];
                    $this->n = $args[0];
                    $this->A = array_fill(0, $this->m, array_fill(0, $this->n, 0));

                    break;
                //Rectangular matrix - m x n
                case 'integer,integer':
                    $this->m = $args[0];
                    $this->n = $args[1];
                    $this->A = array_fill(0, $this->m, array_fill(0, $this->n, 0));

                    break;
                //Rectangular matrix - m x n initialized from packed array
                case 'array,integer':
                    $this->m = $args[1];
                    if ($this->m != 0) {
                        $this->n = count($args[0]) / $this->m;
                    } else {
                        $this->n = 0;
                    }
                    if (($this->m * $this->n) == count($args[0])) {
                        for ($i = 0; $i < $this->m; ++$i) {
                            for ($j = 0; $j < $this->n; ++$j) {
                                $this->A[$i][$j] = $args[0][$i + $j * $this->m];
                            }
                        }
                    } else {
                        throw new CalculationException(self::ARRAY_LENGTH_EXCEPTION);
                    }

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
        } else {
            throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
        }
    }

    /**
     * getArray.
     *
     * @return array Matrix array
     */
    public function getArray()
    {
        return $this->A;
    }

    /**
     * getRowDimension.
     *
     * @return int Row dimension
     */
    public function getRowDimension()
    {
        return $this->m;
    }

    /**
     * getColumnDimension.
     *
     * @return int Column dimension
     */
    public function getColumnDimension()
    {
        return $this->n;
    }

    /**
     * get.
     *
     * Get the i,j-th element of the matrix.
     *
     * @param int $i Row position
     * @param int $j Column position
     *
     * @return mixed Element (int/float/double)
     */
    public function get($i = null, $j = null)
    {
        return $this->A[$i][$j];
    }

    /**
     * getMatrix.
     *
     *    Get a submatrix
     *
     * @param int $i0 Initial row index
     * @param int $iF Final row index
     * @param int $j0 Initial column index
     * @param int $jF Final column index
     *
     * @return Matrix Submatrix
     */
    public function getMatrix(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                //A($i0...; $j0...)
                case 'integer,integer':
                    [$i0, $j0] = $args;
                    if ($i0 >= 0) {
                        $m = $this->m - $i0;
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    if ($j0 >= 0) {
                        $n = $this->n - $j0;
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    $R = new self($m, $n);
                    for ($i = $i0; $i < $this->m; ++$i) {
                        for ($j = $j0; $j < $this->n; ++$j) {
                            $R->set($i, $j, $this->A[$i][$j]);
                        }
                    }

                    return $R;

                    break;
                //A($i0...$iF; $j0...$jF)
                case 'integer,integer,integer,integer':
                    [$i0, $iF, $j0, $jF] = $args;
                    if (($iF > $i0) && ($this->m >= $iF) && ($i0 >= 0)) {
                        $m = $iF - $i0;
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    if (($jF > $j0) && ($this->n >= $jF) && ($j0 >= 0)) {
                        $n = $jF - $j0;
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    $R = new self($m + 1, $n + 1);
                    for ($i = $i0; $i <= $iF; ++$i) {
                        for ($j = $j0; $j <= $jF; ++$j) {
                            $R->set($i - $i0, $j - $j0, $this->A[$i][$j]);
                        }
                    }

                    return $R;

                    break;
                //$R = array of row indices; $C = array of column indices
                case 'array,array':
                    [$RL, $CL] = $args;
                    if (count($RL) > 0) {
                        $m = count($RL);
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    if (count($CL) > 0) {
                        $n = count($CL);
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    $R = new self($m, $n);
                    for ($i = 0; $i < $m; ++$i) {
                        for ($j = 0; $j < $n; ++$j) {
                            $R->set($i, $j, $this->A[$RL[$i]][$CL[$j]]);
                        }
                    }

                    return $R;

                    break;
                //A($i0...$iF); $CL = array of column indices
                case 'integer,integer,array':
                    [$i0, $iF, $CL] = $args;
                    if (($iF > $i0) && ($this->m >= $iF) && ($i0 >= 0)) {
                        $m = $iF - $i0;
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    if (count($CL) > 0) {
                        $n = count($CL);
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    $R = new self($m, $n);
                    for ($i = $i0; $i < $iF; ++$i) {
                        for ($j = 0; $j < $n; ++$j) {
                            $R->set($i - $i0, $j, $this->A[$i][$CL[$j]]);
                        }
                    }

                    return $R;

                    break;
                //$RL = array of row indices
                case 'array,integer,integer':
                    [$RL, $j0, $jF] = $args;
                    if (count($RL) > 0) {
                        $m = count($RL);
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    if (($jF >= $j0) && ($this->n >= $jF) && ($j0 >= 0)) {
                        $n = $jF - $j0;
                    } else {
                        throw new CalculationException(self::ARGUMENT_BOUNDS_EXCEPTION);
                    }
                    $R = new self($m, $n + 1);
                    for ($i = 0; $i < $m; ++$i) {
                        for ($j = $j0; $j <= $jF; ++$j) {
                            $R->set($i, $j - $j0, $this->A[$RL[$i]][$j]);
                        }
                    }

                    return $R;

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
        } else {
            throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
        }
    }

    /**
     * checkMatrixDimensions.
     *
     *    Is matrix B the same size?
     *
     * @param Matrix $B Matrix B
     *
     * @return bool
     */
    public function checkMatrixDimensions($B = null)
    {
        if ($B instanceof self) {
            if (($this->m == $B->getRowDimension()) && ($this->n == $B->getColumnDimension())) {
                return true;
            }

            throw new CalculationException(self::MATRIX_DIMENSION_EXCEPTION);
        }

        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
    }

    //    function checkMatrixDimensions()

    /**
     * set.
     *
     * Set the i,j-th element of the matrix.
     *
     * @param int $i Row position
     * @param int $j Column position
     * @param mixed $c Int/float/double value
     *
     * @return mixed Element (int/float/double)
     */
    public function set($i = null, $j = null, $c = null)
    {
        // Optimized set version just has this
        $this->A[$i][$j] = $c;
    }

    //    function set()

    /**
     * identity.
     *
     * Generate an identity matrix.
     *
     * @param int $m Row dimension
     * @param int $n Column dimension
     *
     * @return Matrix Identity matrix
     */
    public function identity($m = null, $n = null)
    {
        return $this->diagonal($m, $n, 1);
    }

    /**
     * diagonal.
     *
     *    Generate a diagonal matrix
     *
     * @param int $m Row dimension
     * @param int $n Column dimension
     * @param mixed $c Diagonal value
     *
     * @return Matrix Diagonal matrix
     */
    public function diagonal($m = null, $n = null, $c = 1)
    {
        $R = new self($m, $n);
        for ($i = 0; $i < $m; ++$i) {
            $R->set($i, $i, $c);
        }

        return $R;
    }

    /**
     * getMatrixByRow.
     *
     *    Get a submatrix by row index/range
     *
     * @param int $i0 Initial row index
     * @param int $iF Final row index
     *
     * @return Matrix Submatrix
     */
    public function getMatrixByRow($i0 = null, $iF = null)
    {
        if (is_int($i0)) {
            if (is_int($iF)) {
                return $this->getMatrix($i0, 0, $iF + 1, $this->n);
            }

            return $this->getMatrix($i0, 0, $i0 + 1, $this->n);
        }

        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
    }

    /**
     * getMatrixByCol.
     *
     *    Get a submatrix by column index/range
     *
     * @param int $j0 Initial column index
     * @param int $jF Final column index
     *
     * @return Matrix Submatrix
     */
    public function getMatrixByCol($j0 = null, $jF = null)
    {
        if (is_int($j0)) {
            if (is_int($jF)) {
                return $this->getMatrix(0, $j0, $this->m, $jF + 1);
            }

            return $this->getMatrix(0, $j0, $this->m, $j0 + 1);
        }

        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
    }

    /**
     * transpose.
     *
     *    Tranpose matrix
     *
     * @return Matrix Transposed matrix
     */
    public function transpose()
    {
        $R = new self($this->n, $this->m);
        for ($i = 0; $i < $this->m; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                $R->set($j, $i, $this->A[$i][$j]);
            }
        }

        return $R;
    }

    //    function transpose()

    /**
     * trace.
     *
     *    Sum of diagonal elements
     *
     * @return float Sum of diagonal elements
     */
    public function trace()
    {
        $s = 0;
        $n = min($this->m, $this->n);
        for ($i = 0; $i < $n; ++$i) {
            $s += $this->A[$i][$i];
        }

        return $s;
    }

    /**
     * uminus.
     *
     *    Unary minus matrix -A
     *
     * @return Matrix Unary minus matrix
     */
    public function uminus()
    {
    }

    /**
     * plus.
     *
     *    A + B
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Sum
     */
    public function plus(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $M->set($i, $j, $M->get($i, $j) + $this->A[$i][$j]);
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * plusEquals.
     *
     *    A = A + B
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Sum
     */
    public function plusEquals(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $validValues = true;
                    $value = $M->get($i, $j);
                    if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
                        $this->A[$i][$j] = trim($this->A[$i][$j], '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($this->A[$i][$j]);
                    }
                    if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
                        $value = trim($value, '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($value);
                    }
                    if ($validValues) {
                        $this->A[$i][$j] += $value;
                    } else {
                        $this->A[$i][$j] = Functions::NAN();
                    }
                }
            }

            return $this;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * minus.
     *
     *    A - B
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Sum
     */
    public function minus(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $M->set($i, $j, $M->get($i, $j) - $this->A[$i][$j]);
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * minusEquals.
     *
     *    A = A - B
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Sum
     */
    public function minusEquals(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $validValues = true;
                    $value = $M->get($i, $j);
                    if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
                        $this->A[$i][$j] = trim($this->A[$i][$j], '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($this->A[$i][$j]);
                    }
                    if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
                        $value = trim($value, '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($value);
                    }
                    if ($validValues) {
                        $this->A[$i][$j] -= $value;
                    } else {
                        $this->A[$i][$j] = Functions::NAN();
                    }
                }
            }

            return $this;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * arrayTimes.
     *
     *    Element-by-element multiplication
     *    Cij = Aij * Bij
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Matrix Cij
     */
    public function arrayTimes(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $M->set($i, $j, $M->get($i, $j) * $this->A[$i][$j]);
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * arrayTimesEquals.
     *
     *    Element-by-element multiplication
     *    Aij = Aij * Bij
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Matrix Aij
     */
    public function arrayTimesEquals(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $validValues = true;
                    $value = $M->get($i, $j);
                    if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
                        $this->A[$i][$j] = trim($this->A[$i][$j], '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($this->A[$i][$j]);
                    }
                    if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
                        $value = trim($value, '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($value);
                    }
                    if ($validValues) {
                        $this->A[$i][$j] *= $value;
                    } else {
                        $this->A[$i][$j] = Functions::NAN();
                    }
                }
            }

            return $this;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * arrayRightDivide.
     *
     *    Element-by-element right division
     *    A / B
     *
     * @param Matrix $B Matrix B
     *
     * @return Matrix Division result
     */
    public function arrayRightDivide(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $validValues = true;
                    $value = $M->get($i, $j);
                    if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
                        $this->A[$i][$j] = trim($this->A[$i][$j], '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($this->A[$i][$j]);
                    }
                    if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
                        $value = trim($value, '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($value);
                    }
                    if ($validValues) {
                        if ($value == 0) {
                            //    Trap for Divide by Zero error
                            $M->set($i, $j, '#DIV/0!');
                        } else {
                            $M->set($i, $j, $this->A[$i][$j] / $value);
                        }
                    } else {
                        $M->set($i, $j, Functions::NAN());
                    }
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * arrayRightDivideEquals.
     *
     *    Element-by-element right division
     *    Aij = Aij / Bij
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Matrix Aij
     */
    public function arrayRightDivideEquals(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $this->A[$i][$j] = $this->A[$i][$j] / $M->get($i, $j);
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * arrayLeftDivide.
     *
     *    Element-by-element Left division
     *    A / B
     *
     * @param Matrix $B Matrix B
     *
     * @return Matrix Division result
     */
    public function arrayLeftDivide(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $M->set($i, $j, $M->get($i, $j) / $this->A[$i][$j]);
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * arrayLeftDivideEquals.
     *
     *    Element-by-element Left division
     *    Aij = Aij / Bij
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Matrix Aij
     */
    public function arrayLeftDivideEquals(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $this->A[$i][$j] = $M->get($i, $j) / $this->A[$i][$j];
                }
            }

            return $M;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * times.
     *
     *    Matrix multiplication
     *
     * @param mixed $n Matrix/Array/Scalar
     *
     * @return Matrix Product
     */
    public function times(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $B = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }
                    if ($this->n == $B->m) {
                        $C = new self($this->m, $B->n);
                        for ($j = 0; $j < $B->n; ++$j) {
                            $Bcolj = [];
                            for ($k = 0; $k < $this->n; ++$k) {
                                $Bcolj[$k] = $B->A[$k][$j];
                            }
                            for ($i = 0; $i < $this->m; ++$i) {
                                $Arowi = $this->A[$i];
                                $s = 0;
                                for ($k = 0; $k < $this->n; ++$k) {
                                    $s += $Arowi[$k] * $Bcolj[$k];
                                }
                                $C->A[$i][$j] = $s;
                            }
                        }

                        return $C;
                    }

                    throw new CalculationException(self::MATRIX_DIMENSION_EXCEPTION);
                case 'array':
                    $B = new self($args[0]);
                    if ($this->n == $B->m) {
                        $C = new self($this->m, $B->n);
                        for ($i = 0; $i < $C->m; ++$i) {
                            for ($j = 0; $j < $C->n; ++$j) {
                                $s = '0';
                                for ($k = 0; $k < $C->n; ++$k) {
                                    $s += $this->A[$i][$k] * $B->A[$k][$j];
                                }
                                $C->A[$i][$j] = $s;
                            }
                        }

                        return $C;
                    }

                    throw new CalculationException(self::MATRIX_DIMENSION_EXCEPTION);
                case 'integer':
                    $C = new self($this->A);
                    for ($i = 0; $i < $C->m; ++$i) {
                        for ($j = 0; $j < $C->n; ++$j) {
                            $C->A[$i][$j] *= $args[0];
                        }
                    }

                    return $C;
                case 'double':
                    $C = new self($this->m, $this->n);
                    for ($i = 0; $i < $C->m; ++$i) {
                        for ($j = 0; $j < $C->n; ++$j) {
                            $C->A[$i][$j] = $args[0] * $this->A[$i][$j];
                        }
                    }

                    return $C;
                case 'float':
                    $C = new self($this->A);
                    for ($i = 0; $i < $C->m; ++$i) {
                        for ($j = 0; $j < $C->n; ++$j) {
                            $C->A[$i][$j] *= $args[0];
                        }
                    }

                    return $C;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
            }
        } else {
            throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
        }
    }

    /**
     * power.
     *
     *    A = A ^ B
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Sum
     */
    public function power(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $validValues = true;
                    $value = $M->get($i, $j);
                    if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
                        $this->A[$i][$j] = trim($this->A[$i][$j], '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($this->A[$i][$j]);
                    }
                    if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
                        $value = trim($value, '"');
                        $validValues &= StringHelper::convertToNumberIfFraction($value);
                    }
                    if ($validValues) {
                        $this->A[$i][$j] = pow($this->A[$i][$j], $value);
                    } else {
                        $this->A[$i][$j] = Functions::NAN();
                    }
                }
            }

            return $this;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * concat.
     *
     *    A = A & B
     *
     * @param mixed $B Matrix/Array
     *
     * @return Matrix Sum
     */
    public function concat(...$args)
    {
        if (count($args) > 0) {
            $match = implode(',', array_map('gettype', $args));

            switch ($match) {
                case 'object':
                    if ($args[0] instanceof self) {
                        $M = $args[0];
                    } else {
                        throw new CalculationException(self::ARGUMENT_TYPE_EXCEPTION);
                    }

                    break;
                case 'array':
                    $M = new self($args[0]);

                    break;
                default:
                    throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);

                    break;
            }
            $this->checkMatrixDimensions($M);
            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = 0; $j < $this->n; ++$j) {
                    $this->A[$i][$j] = trim($this->A[$i][$j], '"') . trim($M->get($i, $j), '"');
                }
            }

            return $this;
        }

        throw new CalculationException(self::POLYMORPHIC_ARGUMENT_EXCEPTION);
    }

    /**
     * Solve A*X = B.
     *
     * @param Matrix $B Right hand side
     *
     * @return Matrix ... Solution if A is square, least squares solution otherwise
     */
    public function solve($B)
    {
        if ($this->m == $this->n) {
            $LU = new LUDecomposition($this);

            return $LU->solve($B);
        }
        $QR = new QRDecomposition($this);

        return $QR->solve($B);
    }

    /**
     * Matrix inverse or pseudoinverse.
     *
     * @return Matrix ... Inverse(A) if A is square, pseudoinverse otherwise.
     */
    public function inverse()
    {
        return $this->solve($this->identity($this->m, $this->m));
    }

    /**
     * det.
     *
     *    Calculate determinant
     *
     * @return float Determinant
     */
    public function det()
    {
        $L = new LUDecomposition($this);

        return $L->det();
    }
}
