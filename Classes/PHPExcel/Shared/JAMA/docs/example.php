<?php
include_once "includes/header.php";
include_once "includes/navbar.php";
?>
<h2>Magic Square Example</h2>
<p>
The Jama distribution comes with a magic square example that is used to 
test and benchmark the LU, QR, SVD and symmetric Eig decompositions.  
The example outputs a multi-column table with these column headings:
</p>

<table border='1' cellpadding='5' cellspacing='0' align='center'>
  <tr>
    <td><b>n</b></td>
    <td>Order of magic square.</td>
  </tr>
  <tr>
    <td><b>trace</b></td>
    <td>Diagonal sum, should be the magic sum, (n^3 + n)/2.</td>
  </tr>
  <tr>
    <td><b>max_eig</b></td>
    <td>Maximum eigenvalue of (A + A')/2, should equal trace.</td>
  </tr>
  <tr>
    <td><b>rank</b></td>
    <td>Linear algebraic rank, should equal n if n is odd, be less than n if n is even.</td>
  </tr>
  <tr>
    <td><b>cond</b></td>
    <td>L_2 condition number, ratio of singular values.</td>
  </tr>
  <tr>
    <td><b>lu_res</b></td>
    <td>test of LU factorization, norm1(L*U-A(p,:))/(n*eps).</td>
  </tr>
  <tr>
    <td><b>qr_res</b></td>
    <td>test of QR factorization, norm1(Q*R-A)/(n*eps).</td>
  </tr>
</table>
<p>
Running the Java-based version of the matix square example produces these results:
</p>

<table border='1' align='center'>
  <tr>
    <th> n </th>
    <th> trace </th>       
    <th> max_eig </th>   
    <th> rank </th>        
    <th> cond </th>      
    <th> lu_res </th>      
    <th> qr_res </th>
  </tr>
  <tr>
    <td>3</td><td>15</td><td>15.000</td><td>3</td><td>4.330</td><td>0.000</td><td>11.333</td>
  </tr>
  <tr>
    <td>4</td><td>34</td><td>34.000</td><td>3</td><td> Inf</td><td>0.000</td><td>13.500</td>
  <tr>
    <td>5</td><td>65</td><td>65.000</td><td>5</td><td>5.462</td><td>0.000</td><td>14.400</td>
  </tr>
  <tr>
    <td>6</td><td>111</td><td>111.000</td><td>5</td><td> Inf</td><td>5.333</td><td>16.000</td>
  </tr>
  <tr>
    <td>7</td><td>175</td><td>175.000</td><td>7</td><td>7.111</td><td>2.286</td><td>37.714</td>
  </tr>
  <tr>
    <td>8</td><td>260</td><td>260.000</td><td>3</td><td> Inf</td><td>0.000</td><td>59.000</td>
  </tr>
  <tr>
    <td>9</td><td>369</td><td>369.000</td><td>9</td><td>9.102</td><td>7.111</td><td>53.333</td>
  </tr>
  <tr>
    <td>10</td><td>505</td><td>505.000</td><td>7</td><td> Inf</td><td>3.200</td><td>159.200</td>
  </tr>
  <tr>
    <td>11</td><td>671</td><td>671.000</td><td>11</td><td>11.102</td><td>2.909</td><td>215.273</td>
  </tr>
  <tr>
    <td>12</td><td>870</td><td>870.000</td><td>3</td><td> Inf</td><td>0.000</td><td>185.333</td>
  </tr>
  <tr>
    <td>13</td><td>1105</td><td>1105.000</td><td>13</td><td>13.060</td><td>4.923</td><td>313.846</td>
  </tr>
  <tr>
    <td>14</td><td>1379</td><td>1379.000</td><td>9</td><td> Inf</td><td>4.571</td><td>540.571</td>
  </tr>
  <tr>
    <td>15</td><td>1695</td><td>1695.000</td><td>15</td><td>15.062</td><td>4.267</td><td>242.133</td>
  </tr>
  <tr>
    <td>16</td><td>2056</td><td>2056.000</td><td>3</td><td> Inf</td><td>0.000</td><td>488.500</td>
  </tr>
  <tr>
    <td>17</td><td>2465</td><td>2465.000</td><td>17</td><td>17.042</td><td>7.529</td><td>267.294</td>
  </tr>
  <tr>
    <td>18</td><td>2925</td><td>2925.000</td><td>11</td><td> Inf</td><td>7.111</td><td>520.889</td>
  </tr>
  <tr>
    <td>19</td><td>3439</td><td>3439.000</td><td>19</td><td>19.048</td><td>16.842</td><td>387.368</td>
  </tr>
  <tr>
    <td>20</td><td>4010</td><td>4010.000</td><td>3</td><td> Inf</td><td>14.400</td><td>584.800</td>
  </tr>
  <tr>
    <td>21</td><td>4641</td><td>4641.000</td><td>21</td><td>21.035</td><td>6.095</td><td>1158.095</td>
  </tr>
  <tr>
    <td>22</td><td>5335</td><td>5335.000</td><td>13</td><td> Inf</td><td>6.545</td><td>1132.364</td>
  </tr>
  <tr>
    <td>23</td><td>6095</td><td>6095.000</td><td>23</td><td>23.037</td><td>11.130</td><td>1268.870</td>
  </tr>
  <tr>
    <td>24</td><td>6924</td><td>6924.000</td><td>3</td><td> Inf</td><td>10.667</td><td>827.500</td>
  </tr>
  <tr>
    <td>25</td><td>7825</td><td>7825.000</td><td>25</td><td>25.029</td><td>35.840</td><td>1190.400</td>
  </tr>
  <tr>
    <td>26</td><td>8801</td><td>8801.000</td><td>15</td><td> Inf</td><td>4.923</td><td>1859.077</td>
  </tr>
  <tr>
    <td>27</td><td>9855</td><td>9855.000</td><td>27</td><td>27.032</td><td>37.926</td><td>1365.333</td>
  </tr>
  <tr>
    <td>28</td><td>10990</td><td>10990.000</td><td>3</td><td> Inf</td><td>34.286</td><td>1365.714</td>
  </tr>
  <tr>
    <td>29</td><td>12209</td><td>12209.000</td><td>29</td><td>29.025</td><td>30.897</td><td>1647.448</td>
  </tr>
  <tr>
    <td>30</td><td>13515</td><td>13515.000</td><td>17</td><td> Inf</td><td>8.533</td><td>2571.733</td>
  </tr>
  <tr>
    <td>31</td><td>14911</td><td>14911.000</td><td>31</td><td>31.027</td><td>33.032</td><td>1426.581</td>
  </tr>
  <tr>
    <td>32</td><td>16400</td><td>16400.000</td><td>3</td><td> Inf</td><td>0.000</td><td>1600.125</td>
  </tr>
</table>
<center>Elapsed Time =        0.710 seconds</center>

<p>
The magic square example does not fare well when <a href='../examples/MagicSquareExample.php'>run as a PHP script</a>.  For a 32x32 matrix array 
it takes around a second to complete just the last row of computations in the above table.  
Hopefully this result will spur PHP developers to find optimizations and better attuned algorithms 
to speed things up. Matrix algebra is a great testing ground for ideas about time and memory 
performance optimation.  Keep in perspective that PHP JAMA scripts are still plenty fast for use as 
a tool for learning about matrix algebra and quickly extending your knowledge with new scripts 
to apply knowledge.
</p> 

<p>
To learn more about the subject of magic squares you can visit the <a href='http://mathforum.org/alejandre/magic.square.html'>Drexel Math Forum on Magic Squares</a>.
You can also learn more by carefully examining the <code>MagicSquareExample.php</code> source code below.
</p>

<?php
highlight_file("../examples/MagicSquareExample.php");
include_once "includes/footer.php";	
?>
