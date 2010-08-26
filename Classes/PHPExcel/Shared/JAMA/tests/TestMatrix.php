<?php

require_once "../Matrix.php";

class TestMatrix {

  function TestMatrix() {

    // define test variables

    $errorCount   = 0;
    $warningCount = 0;
    $columnwise   = array(1.,2.,3.,4.,5.,6.,7.,8.,9.,10.,11.,12.);
    $rowwise      = array(1.,4.,7.,10.,2.,5.,8.,11.,3.,6.,9.,12.);
    $avals        = array(array(1.,4.,7.,10.),array(2.,5.,8.,11.),array(3.,6.,9.,12.));
    $rankdef      = $avals;
    $tvals        = array(array(1.,2.,3.),array(4.,5.,6.),array(7.,8.,9.),array(10.,11.,12.));
    $subavals     = array(array(5.,8.,11.),array(6.,9.,12.));
    $rvals        = array(array(1.,4.,7.),array(2.,5.,8.,11.),array(3.,6.,9.,12.));
    $pvals        = array(array(1.,1.,1.),array(1.,2.,3.),array(1.,3.,6.));
    $ivals        = array(array(1.,0.,0.,0.),array(0.,1.,0.,0.),array(0.,0.,1.,0.));
    $evals        = array(array(0.,1.,0.,0.),array(1.,0.,2.e-7,0.),array(0.,-2.e-7,0.,1.),array(0.,0.,1.,0.));
    $square       = array(array(166.,188.,210.),array(188.,214.,240.),array(210.,240.,270.));
    $sqSolution   = array(array(13.),array(15.));
    $condmat      = array(array(1.,3.),array(7.,9.));
    $rows         = 3;
    $cols         = 4;
    $invalidID    = 5; /* should trigger bad shape for construction with val        */
    $raggedr      = 0; /* (raggedr,raggedc) should be out of bounds in ragged array */
    $raggedc      = 4;
    $validID      = 3; /* leading dimension of intended test Matrices               */
    $nonconformld = 4; /* leading dimension which is valid, but nonconforming       */
    $ib           = 1; /* index ranges for sub Matrix                               */
    $ie           = 2;
    $jb           = 1;
    $je           = 3;
    $rowindexset       = array(1,2);
    $badrowindexset    = array(1,3);
    $columnindexset    = array(1,2,3);
    $badcolumnindexset = array(1,2,4);
    $columnsummax      = 33.;
    $rowsummax         = 30.;
    $sumofdiagonals    = 15;
    $sumofsquares      = 650;

    /**
    * Test matrix methods
    */

    /**
    * Constructors and constructor-like methods:
    *
    *   Matrix(double[], int)
    *   Matrix(double[][])
    *   Matrix(int, int)
    *   Matrix(int, int, double)
    *   Matrix(int, int, double[][])
    *   constructWithCopy(double[][])
    *   random(int,int)
    *   identity(int)
    */
    echo "<p>Testing constructors and constructor-like methods...</p>";

    $A = new Matrix($columnwise, 3);
    if($A instanceof Matrix) {
      $this->try_success("Column-packed constructor...");
    } else
      $errorCount = $this->try_failure($errorCount, "Column-packed constructor...", "Unable to construct Matrix");

    $T = new Matrix($tvals);
    if($T instanceof Matrix)
      $this->try_success("2D array constructor...");
    else
      $errorCount = $this->try_failure($errorCount, "2D array constructor...", "Unable to construct Matrix");

    $A = new Matrix($columnwise, $validID);
    $B = new Matrix($avals);
    $tmp = $B->get(0,0);
    $avals[0][0] = 0.0;
    $C = $B->minus($A);
    $avals[0][0] = $tmp;
    $B = Matrix::constructWithCopy($avals);
    $tmp = $B->get(0,0);
    $avals[0][0] = 0.0;
    /** check that constructWithCopy behaves properly **/
    if ( ( $tmp - $B->get(0,0) ) != 0.0 )
      $errorCount = $this->try_failure($errorCount,"constructWithCopy... ","copy not effected... data visible outside");
    else
      $this->try_success("constructWithCopy... ","");

    $I = new Matrix($ivals);
    if ( $this->checkMatrices($I,Matrix::identity(3,4)) )
      $this->try_success("identity... ","");
    else
      $errorCount = $this->try_failure($errorCount,"identity... ","identity Matrix not successfully created");

    /**
    * Access Methods:
    *
    *   getColumnDimension()
    *   getRowDimension()
    *   getArray()
    *   getArrayCopy()
    *   getColumnPackedCopy()
    *   getRowPackedCopy()
    *   get(int,int)
    *   getMatrix(int,int,int,int)
    *   getMatrix(int,int,int[])
    *   getMatrix(int[],int,int)
    *   getMatrix(int[],int[])
    *   set(int,int,double)
    *   setMatrix(int,int,int,int,Matrix)
    *   setMatrix(int,int,int[],Matrix)
    *   setMatrix(int[],int,int,Matrix)
    *   setMatrix(int[],int[],Matrix)
    */
    print "<p>Testing access methods...</p>";

	$B = new Matrix($avals);
	if($B->getRowDimension() == $rows)
	  $this->try_success("getRowDimension...");
	else
	  $errorCount = $this->try_failure($errorCount, "getRowDimension...");

	if($B->getColumnDimension() == $cols)
	  $this->try_success("getColumnDimension...");
	else
	  $errorCount = $this->try_failure($errorCount, "getColumnDimension...");

	$barray = $B->getArray();
	if($this->checkArrays($barray, $avals))
	  $this->try_success("getArray...");
	else
	  $errorCount = $this->try_failure($errorCount, "getArray...");

	$bpacked = $B->getColumnPackedCopy();
	if($this->checkArrays($bpacked, $columnwise))
	  $this->try_success("getColumnPackedCopy...");
	else
	  $errorCount = $this->try_failure($errorCount, "getColumnPackedCopy...");

	$bpacked = $B->getRowPackedCopy();
	if($this->checkArrays($bpacked, $rowwise))
	  $this->try_success("getRowPackedCopy...");
	else
	  $errorCount = $this->try_failure($errorCount, "getRowPackedCopy...");

    /**
    * Array-like methods:
    *   minus
    *   minusEquals
    *   plus
    *   plusEquals
    *   arrayLeftDivide
    *   arrayLeftDivideEquals
    *   arrayRightDivide
    *   arrayRightDivideEquals
    *   arrayTimes
    *   arrayTimesEquals
    *   uminus
    */
    print "<p>Testing array-like methods...</p>";

    /**
    * I/O methods:
    *   read
    *   print
    *   serializable:
    *   writeObject
    *   readObject
    */
    print "<p>Testing I/O methods...</p>";

    /**
    * Test linear algebra methods
    */
    echo "<p>Testing linear algebra methods...<p>";

    $A = new Matrix($columnwise, 3);
    if( $this->checkMatrices($A->transpose(), $T) )
      $this->try_success("Transpose check...");
    else
      $errorCount = $this->try_failure($errorCount, "Transpose check...", "Matrices are not equal");

    if($this->checkScalars($A->norm1(), $columnsummax))
      $this->try_success("Maximum column sum...");
    else
      $errorCount = $this->try_failure($errorCount, "Maximum column sum...", "Incorrect: " . $A->norm1() . " != " . $columnsummax);

    if($this->checkScalars($A->normInf(), $rowsummax))
      $this->try_success("Maximum row sum...");
    else
      $errorCount = $this->try_failure($errorCount, "Maximum row sum...", "Incorrect: " . $A->normInf() . " != " . $rowsummax );

    if($this->checkScalars($A->normF(), sqrt($sumofsquares)))
      $this->try_success("Frobenius norm...");
    else
      $errorCount = $this->try_failure($errorCount, "Frobenius norm...", "Incorrect:" . $A->normF() . " != " . sqrt($sumofsquares));

    if($this->checkScalars($A->trace(), $sumofdiagonals))
      $this->try_success("Matrix trace...");
    else
      $errorCount = $this->try_failure($errorCount, "Matrix trace...", "Incorrect: " . $A->trace() . " != " . $sumofdiagonals);

    $B = $A->getMatrix(0, $A->getRowDimension(), 0, $A->getRowDimension());
    if( $B->det() == 0 )
      $this->try_success("Matrix determinant...");
    else
      $errorCount = $this->try_failure($errorCount, "Matrix determinant...", "Incorrect: " . $B->det() . " != " . 0);

    $A = new Matrix($columnwise,3);
    $SQ = new Matrix($square);
    if ($this->checkMatrices($SQ, $A->times($A->transpose())))
      $this->try_success("times(Matrix)...");
    else {
      $errorCount = $this->try_failure($errorCount, "times(Matrix)...", "Unable to multiply matrices");
      $SQ->toHTML();
      $AT->toHTML();
    }

    $A = new Matrix($columnwise, 4);

    $QR = $A->qr();
    $R = $QR->getR();
    $Q = $QR->getQ();
    if($this->checkMatrices($A, $Q->times($R)))
      $this->try_success("QRDecomposition...","");
    else
      $errorCount = $this->try_failure($errorCount,"QRDecomposition...","incorrect qr decomposition calculation");

    $A = new Matrix($columnwise, 4);
    $SVD = $A->svd();
    $U = $SVD->getU();
    $S = $SVD->getS();
    $V = $SVD->getV();
    if ($this->checkMatrices($A, $U->times($S->times($V->transpose()))))
      $this->try_success("SingularValueDecomposition...","");
    else
      $errorCount = $this->try_failure($errorCount,"SingularValueDecomposition...","incorrect singular value decomposition calculation");

    $n = $A->getColumnDimension();
    $A = $A->getMatrix(0,$n-1,0,$n-1);
    $A->set(0,0,0.);

    $LU = $A->lu();
    $L  = $LU->getL();
    if ( $this->checkMatrices($A->getMatrix($LU->getPivot(),0,$n-1), $L->times($LU->getU())) )
      $this->try_success("LUDecomposition...","");
    else
      $errorCount = $this->try_failure($errorCount,"LUDecomposition...","incorrect LU decomposition calculation");

    $X = $A->inverse();
    if ( $this->checkMatrices($A->times($X),Matrix::identity(3,3)) )
       $this->try_success("inverse()...","");
     else
       $errorCount = $this->try_failure($errorCount, "inverse()...","incorrect inverse calculation");

    $DEF = new Matrix($rankdef);
    if($this->checkScalars($DEF->rank(), min($DEF->getRowDimension(), $DEF->getColumnDimension())-1))
      $this->try_success("Rank...");
    else
      $this->try_failure("Rank...", "incorrect rank calculation");

    $B = new Matrix($condmat);
    $SVD = $B->svd();
    $singularvalues = $SVD->getSingularValues();
    if($this->checkScalars($B->cond(), $singularvalues[0]/$singularvalues[min($B->getRowDimension(), $B->getColumnDimension())-1]))
      $this->try_success("Condition number...");
    else
      $this->try_failure("Condition number...", "incorrect condition number calculation");

    $SUB = new Matrix($subavals);
    $O   = new Matrix($SUB->getRowDimension(),1,1.0);
    $SOL = new Matrix($sqSolution);
    $SQ = $SUB->getMatrix(0,$SUB->getRowDimension()-1,0,$SUB->getRowDimension()-1);
    if ( $this->checkMatrices($SQ->solve($SOL),$O) )
      $this->try_success("solve()...","");
    else
     $errorCount = $this->try_failure($errorCount,"solve()...","incorrect lu solve calculation");

    $A = new Matrix($pvals);
    $Chol = $A->chol();
    $L = $Chol->getL();
    if ( $this->checkMatrices($A, $L->times($L->transpose())) )
      $this->try_success("CholeskyDecomposition...","");
    else
      $errorCount = $this->try_failure($errorCount,"CholeskyDecomposition...","incorrect Cholesky decomposition calculation");

    $X = $Chol->solve(Matrix::identity(3,3));
    if ( $this->checkMatrices($A->times($X), Matrix::identity(3,3)) )
      $this->try_success("CholeskyDecomposition solve()...","");
    else
      $errorCount = $this->try_failure($errorCount,"CholeskyDecomposition solve()...","incorrect Choleskydecomposition solve calculation");

    $Eig = $A->eig();
    $D = $Eig->getD();
    $V = $Eig->getV();
    if( $this->checkMatrices($A->times($V),$V->times($D)) )
      $this->try_success("EigenvalueDecomposition (symmetric)...","");
    else
      $errorCount = $this->try_failure($errorCount,"EigenvalueDecomposition (symmetric)...","incorrect symmetric Eigenvalue decomposition calculation");

    $A = new Matrix($evals);
    $Eig = $A->eig();
    $D = $Eig->getD();
    $V = $Eig->getV();
    if ( $this->checkMatrices($A->times($V),$V->times($D)) )
      $this->try_success("EigenvalueDecomposition (nonsymmetric)...","");
    else
      $errorCount = $this->try_failure($errorCount,"EigenvalueDecomposition (nonsymmetric)...","incorrect nonsymmetric Eigenvalue decomposition calculation");

	print("<b>{$errorCount} total errors</b>.");
  }

  /**
  * Print appropriate messages for successful outcome try
  * @param string $s
  * @param string $e
  */
  function try_success($s, $e = "") {
    print "> ". $s ."success<br />";
    if ($e != "")
      print "> Message: ". $e ."<br />";
  }

  /**
  * Print appropriate messages for unsuccessful outcome try
  * @param int $count
  * @param string $s
  * @param string $e
  * @return int incremented counter
  */
  function try_failure($count, $s, $e="") {
    print "> ". $s ."*** failure ***<br />> Message: ". $e ."<br />";
    return ++$count;
  }

  /**
  * Print appropriate messages for unsuccessful outcome try
  * @param int $count
  * @param string $s
  * @param string $e
  * @return int incremented counter
  */
  function try_warning($count, $s, $e="") {
    print "> ". $s ."*** warning ***<br />> Message: ". $e ."<br />";
    return ++$count;
  }

  /**
  * Check magnitude of difference of "scalars".
  * @param float $x
  * @param float $y
  */
  function checkScalars($x, $y) {
    $eps = pow(2.0,-52.0);
    if ($x == 0 & abs($y) < 10*$eps) return;
    if ($y == 0 & abs($x) < 10*$eps) return;
    if (abs($x-$y) > 10 * $eps * max(abs($x),abs($y)))
      return false;
    else
      return true;
  }

  /**
  * Check norm of difference of "vectors".
  * @param float $x[]
  * @param float $y[]
  */
  function checkVectors($x, $y) {
    $nx = count($x);
    $ny = count($y);
    if ($nx == $ny)
      for($i=0; $i < $nx; ++$i)
        $this->checkScalars($x[$i],$y[$i]);
    else
      die("Attempt to compare vectors of different lengths");
  }

  /**
  * Check norm of difference of "arrays".
  * @param float $x[][]
  * @param float $y[][]
  */
  function checkArrays($x, $y) {
    $A = new Matrix($x);
    $B = new Matrix($y);
    return $this->checkMatrices($A,$B);
  }

  /**
  * Check norm of difference of "matrices".
  * @param matrix $X
  * @param matrix $Y
  */
  function checkMatrices($X = null, $Y = null) {
    if( $X == null || $Y == null )
      return false;

    $eps = pow(2.0,-52.0);
    if ($X->norm1() == 0. & $Y->norm1() < 10*$eps) return true;
    if ($Y->norm1() == 0. & $X->norm1() < 10*$eps) return true;

    $A = $X->minus($Y);

    if ($A->norm1() > 1000 * $eps * max($X->norm1(),$Y->norm1()))
      die("The norm of (X-Y) is too large: ".$A->norm1());
    else
      return true;
  }

}

$test = new TestMatrix;
?>
