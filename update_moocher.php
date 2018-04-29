<?php
include("top.html");
include("moocher_shared.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Moocher Updating</title>
	<?php update_moocher() ?>
</head>
    <form action="moocher_home.php" method="get">	  
      <div>
        <input type="submit" value="Home" /> 
      </div>
    </form>
<body>

</body>
</html>

<?php
function update_moocher(){
	include('moocher_shared.php');

	$id = $_GET["moocher_id"];
	$contact = $_GET["contact_info"];
	$table = $_GET["table"];
	$sql = "UPDATE moochers SET ".$table." = '".$contact."' WHERE Member_id=".$id;
  	$query = $db->prepare($sql); //prepares the query
	$query->execute();

	print "Assuming valid id, Moocher's contact info has been updated";
}
?>