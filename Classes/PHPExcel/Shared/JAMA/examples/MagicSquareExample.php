<?php
/**
* @package JAMA
*/

require_once "../Matrix.php";

/**
* Example of use of Matrix Class, featuring magic squares.
*/
class MagicSquareExample {

  /**
  * Generate magic square test matrix.
  * @param int n dimension of matrix
  */
  function magic($n) {

    // Odd order

    if (($n % 2) == 1) {
      $a = ($n+1)/2;
      $b = ($n+1);
      for ($j = 0; $j < $n; ++$j)
        for ($i = 0; $i < $n; ++$i)
          $M[$i][$j] = $n*(($i+$j+$a) % $n) + (($i+2*$j+$b) % $n) + 1;

    // Doubly Even Order

    } else if (($n % 4) == 0) {
      for ($j = 0; $j < $n; ++$j) {
        for ($i = 0; $i < $n; ++$i) {
          if ((($i+1)/2)%2 == (($j+1)/2)%2)
            $M[$i][$j] = $n*$n-$n*$i-$j;
          else
            $M[$i][$j] = $n*$i+$j+1;
        }
      }

    // Singly Even Order

    } else {

      $p = $n/2;
      $k = ($n-2)/4;
      $A = $this->magic($p);
      $M = array();
      for ($j = 0; $j < $p; ++$j) {
        for ($i = 0; $i < $p; ++$i) {
          $aij = $A->get($i,$j);
          $M[$i][$j]       = $aij;
          $M[$i][$j+$p]    = $aij + 2*$p*$p;
          $M[$i+$p][$j]    = $aij + 3*$p*$p;
          $M[$i+$p][$j+$p] = $aij + $p*$p;
        }
      }

      for ($i = 0; $i < $p; ++$i) {
        for ($j = 0; $j < $k; ++$j) {
          $t = $M[$i][$j];
          $M[$i][$j] = $M[$i+$p][$j];
          $M[$i+$p][$j] = $t;
        }
        for ($j = $n-$k+1; $j < $n; ++$j) {
          $t = $M[$i][$j];
          $M[$i][$j] = $M[$i+$p][$j];
          $M[$i+$p][$j] = $t;
        }
      }

      $t = $M[$k][0];  $M[$k][0]  = $M[$k+$p][0];  $M[$k+$p][0]  = $t;
      $t = $M[$k][$k]; $M[$k][$k] = $M[$k+$p][$k]; $M[$k+$p][$k] = $t;

    }

    return new Matrix($M);

  }

  /**
  * Simple function to replicate PHP 5 behaviour
  */
  function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  /**
  * Tests LU, QR, SVD and symmetric Eig decompositions.
  *
  *   n       = order of magic square.
  *   trace   = diagonal sum, should be the magic sum, (n^3 + n)/2.
  *   max_eig = maximum eigenvalue of (A + A')/2, should equal trace.
  *   rank    = linear algebraic rank, should equal n if n is odd,
  *             be less than n if n is even.
  *   cond    = L_2 condition number, ratio of singular values.
  *   lu_res  = test of LU factorization, norm1(L*U-A(p,:))/(n*eps).
  *   qr_res  = test of QR factorization, norm1(Q*R-A)/(n*eps).
  */
  function main() {
    ?>
    <p>Test of Matrix Class, using magic squares.</p>
    <p>See MagicSquareExample.main() for an explanation.</p>
    <table border='1' cellspacing='0' cellpadding='4'>
      <tr>
        <th>n</th>
        <th>trace</th>
        <th>max_eig</th>
        <th>rank</th>
        <th>cond</th>
        <th>lu_res</th>
        <th>qr_res</th>
      </tr>
      <?php
      $start_time = $this->microtime_float();
      $eps = pow(2.0,-52.0);
      for ($n = 3; $n <= 6; ++$n) {
        echo "<tr>";

        echo "<td align='right'>$n</td>";

        $M = $this->magic($n);
        $t = (int) $M->trace();

        echo "<td align='right'>$t</td>";

        $O = $M->plus($M->transpose());
        $E = new EigenvalueDecomposition($O->times(0.5));
        $d = $E->getRealEigenvalues();

        echo "<td align='right'>".$d[$n-1]."</td>";

        $r = $M->rank();

        echo "<td align='right'>".$r."</td>";

        $c = $M->cond();

        if ($c < 1/$eps)
          echo "<td align='right'>".sprintf("%.3f",$c)."</td>";
        else
          echo "<td align='right'>Inf</td>";

        $LU = new LUDecomposition($M);
        $L = $LU->getL();
        $U = $LU->getU();
        $p = $LU->getPivot();
        // Java version: R = L.times(U).minus(M.getMatrix(p,0,n-1));
        $S = $L->times($U);
        $R = $S->minus($M->getMatrix($p,0,$n-1));
        $res = $R->norm1()/($n*$eps);

        echo "<td align='right'>".sprintf("%.3f",$res)."</td>";

        $QR = new QRDecomposition($M);
        $Q = $QR->getQ();
        $R = $QR->getR();
        $S = $Q->times($R);
        $R = $S->minus($M);
        $res = $R->norm1()/($n*$eps);

        echo "<td align='right'>".sprintf("%.3f",$res)."</td>";

        echo "</tr>";

     }
     echo "<table>";
     echo "<br />";

     $stop_time = $this->microtime_float();
     $etime = $stop_time - $start_time;

     echo "<p>Elapsed time is ". sprintf("%.4f",$etime) ." seconds.</p>";

  }

}

$magic = new MagicSquareExample();
$magic->main();

?>
