<?php
include("top.html");
include("moocher_shared.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Item Insertion</title>
	<?php insert_item() ?>
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
function insert_item(){
	include('moocher_shared.php');
	$sql = "SELECT MAX(item_no)+1 FROM items";
  	$query = $db->prepare($sql); //prepares the query
	$query->execute();
	$row = $query->fetch();
	$item_no  = $row[0];
	$name = $_GET["Name"];
	$url = $_GET["URL"];
	$quality = $_GET["Quality"];
	$house = $_GET["houses"];
	$sql = "INSERT INTO items VALUES (" . $item_no . ",'".$name."','".$url."','".$quality."','".$house."',true)";
  	$query = $db->prepare($sql); //prepares the query
	$query->execute(); 

	$sql = "SELECT Item_no FROM items WHERE Item_no=".$item_no;
  	$query = $db->prepare($sql); //prepares the query
	$query->execute();
	$row = $query->fetchAll();

	if (count($row) == 1) {
		print "Item entered successfully";
	} else {
		print "Item unable to be entered. Check all parameters are filled in...";
	}
}
?>