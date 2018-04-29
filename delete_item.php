<?php
include("top.html");
include("moocher_shared.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Item Deletion</title>
	<?php delete_item() ?>
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
function delete_item(){
	include('moocher_shared.php');

	$item_no = $_GET["item_no"];
	$sql = "INSERT INTO archived_rent_log
			SELECT *
			FROM rent_log
			WHERE rent_log.Item_no=".$item_no;
  	$query = $db->prepare($sql); //prepares the query
	$query->execute();

	$sql = "DELETE FROM items WHERE Item_no=".$item_no;
  	$query = $db->prepare($sql); //prepares the query
	$query->execute();

	print "All instances of given item number deleted from the items table";
}
?>