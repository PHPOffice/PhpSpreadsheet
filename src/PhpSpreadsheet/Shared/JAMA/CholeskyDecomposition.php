<?php

namespace PhpOffice\PhpSpreadsheet\Shared\JAMA;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;

/**
 *    Cholesky decomposition class.
 *
 *    For a symmetric, positive definite matrix A, the Cholesky decomposition
 *    is an lower triangular matrix L so that A = L*L'.
 *
 *    If the matrix is not symmetric or positive definite, the constructor
 *    returns a partial decomposition and sets an internal flag that may
 *    be queried by the isSPD() method.
 *
 *    @author Paul Meagher
 *    @author Michael Bommarito
 *
 *    @version 1.2
 */
class CholeskyDecomposition
{
    /**
     * Decomposition storage.
     *
     * @var array
     */
    private $L = [];

    /**
     * Matrix row and column dimension.
     *
     * @var int
     */
    private $m;

    /**
     * Symmetric positive definite flag.
     *
     * @var bool
     */
    private $isspd = true;

    /**
     * CholeskyDecomposition.
     *
     *    Class constructor - decomposes symmetric positive definite matrix
     *
     * @param Matrix $A Matrix square symmetric positive definite matrix
     */
    public function __construct(Matrix $A)
    {
        $this->L = $A->getArray();
        $this->m = $A->getRowDimension();

        for ($i = 0; $i < $this->m; ++$i) {
            for ($j = $i; $j < $this->m; ++$j) {
                for ($sum = $this->L[$i][$j], $k = $i - 1; $k >= 0; --$k) {
                    $sum -= $this->L[$i][$k] * $this->L[$j][$k];
                }
                if ($i == $j) {
                    if ($sum >= 0) {
                        $this->L[$i][$i] = sqrt($sum);
                    } else {
                        $this->isspd = false;
                    }
                } else {
                    if ($this->L[$i][$i] != 0) {
                        $this->L[$j][$i] = $sum / $this->L[$i][$i];
                    }
                }
            }

            for ($k = $i + 1; $k < $this->m; ++$k) {
                $this->L[$i][$k] = 0.0;
            }
        }
    }

    /**
     *    Is the matrix symmetric and positive definite?
     *
     * @return bool
     */
    public function isSPD()
    {
        return $this->isspd;
    }

    /**
     * getL.
     *
     * Return triangular factor.
     *
     * @return Matrix Lower triangular matrix
     */
    public function getL()
    {
        return new Matrix($this->L);
    }

    /**
     * Solve A*X = B.
     *
     * @param $B Row-equal matrix
     *
     * @return Matrix L * L' * X = B
     */
    public function solve(Matrix $B)
    {
        if ($B->getRowDimension() == $this->m) {
            if ($this->isspd) {
                $X = $B->getArrayCopy();
                $nx = $B->getColumnDimension();

                for ($k = 0; $k < $this->m; ++$k) {
                    for ($i = $k + 1; $i < $this->m; ++$i) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X[$i][$j] -= $X[$k][$j] * $this->L[$i][$k];
                        }
                    }
                    for ($j = 0; $j < $nx; ++$j) {
                        $X[$k][$j] /= $this->L[$k][$k];
                    }
                }

                for ($k = $this->m - 1; $k >= 0; --$k) {
                    for ($j = 0; $j < $nx; ++$j) {
                        $X[$k][$j] /= $this->L[$k][$k];
                    }
                    for ($i = 0; $i < $k; ++$i) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X[$i][$j] -= $X[$k][$j] * $this->L[$k][$i];
                        }
                    }
                }

                return new Matrix($X, $this->m, $nx);
            }

            throw new CalculationException(Matrix::MATRIX_SPD_EXCEPTION);
        }

        throw new CalculationException(Matrix::MATRIX_DIMENSION_EXCEPTION);
    }
}
