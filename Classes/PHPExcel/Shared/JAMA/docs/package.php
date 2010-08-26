<?php
include_once "includes/header.php";
include_once "includes/navbar.php";
?>
<p>
Source Listing:
</p>
<ul>
  <?php
  chdir("../");
  $files = glob("*.php");
  $files = array_merge($files, glob("util/*.php"));
  foreach ($files as $fileName) {
    ?>
  	<li><a href="package.php?view=<?php echo sha1($fileName);?>"><?php echo $fileName;?></a>&nbsp;-&nbsp;<?php echo date ("F d Y - g:i a", filemtime($fileName));?></li>
    <?php
  }
  ?>
</ul>
<?php
if( isset($_REQUEST['view']) ) {
	$hash = $_REQUEST['view'];
	$n = array_search($hash, array_map(sha1, $files));
	$fileName = $files[$n];
  ?>
  <hr />  
	Viewing: <?php echo $fileName;?>	
	<hr />
	<?php
	highlight_file($fileName);
	?>
	<hr />
<?php
}
include_once "includes/footer.php";	
?>

