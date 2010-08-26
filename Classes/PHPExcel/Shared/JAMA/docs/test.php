<?php
include_once "includes/header.php";
include_once "includes/navbar.php";
?>
<p>
The first script your should run when you install Jama is the TestMatrix.php script.
</p>
<p>
This will run the unit tests for methods in the <code>Matrix.php</code> class.  Because
the Matrix.php class can be used to invoke all the decomposition methods the <code>TestMatrix.php</code> 
script is a test suite for the whole Jama package.
</p>
<p>
The original <code>TestMatrix.java</code> code uses try/catch error handling.  We will 
eventually create a build of JAMA that will take advantage of PHP5's new try/catch error 
handling capabilities.  This will improve our ability to replicate all the unit tests that 
appeared in the original (except for some print methods that may not be worth porting).
</p>
<p>
You can <a href='../test/TestMatrix.php'>run the TestMatrix.php script</a> to see what 
unit tests are currently implemented.  The source of the <code>TestMatrix.php</code> script 
is provided below.  It is worth studying carefully for an example of how to do matrix algebra
programming with Jama.
</p>
<?php
highlight_file("../test/TestMatrix.php");
include_once "includes/footer.php";	
?>
