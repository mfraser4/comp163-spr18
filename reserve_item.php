<?php
include("top.html");
include("moocher_shared.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Renting</title>
</head>
<body>
<?php reserve_item() ?>
    <form action="moocher_home.php" method="get">	  
      <div>
        <input type="submit" value="Home" /> 
      </div>
    </form>
</body>
</html>

<?php
function reserve_item(){
	include('moocher_shared.php');

	$item_no = $_GET["item_id"];
	$name = $_GET["moocher"];
	$check_rent = "SELECT Item_no FROM rent_log WHERE Item_no=".$item_no." AND CURRENT_DATE<Return_date";
	$query = $db->prepare($check_rent); //prepares the query
	$query->execute();
	$row = $query->fetchAll();
	$member = "SELECT DISTINCT Member_id FROM moochers WHERE Name='".$name."'";
	$query = $db->prepare($member); //prepares the query
	$query->execute();
	$row = $query->fetch();
	$member = $row[0];

	if (count($row) == 0) {
		// insert into rent log and rent the item
		$house = "SELECT DISTINCT Home FROM items WHERE Item_no=".$item_no;
		$query = $db->prepare($house); //prepares the query
		$query->execute();
		$row = $query->fetch();
		$house = $row[0];

		$sql = "INSERT INTO rent_log VALUES ('".$house."',".$item_no.",".$member.",CURRENT_DATE,CURRENT_DATE + INTERVAL '7 days')";
		print $sql;
		$query = $db->prepare($sql); //prepares the query
		$query->execute();
		print "Your item has been rented!  You have a week from today to return the item without any late fees. Redirecting to the homepage...";
	} else {
		// put the item on hold for that person
		$hold_num = "SELECT COUNT(Item_no)+1 FROM holds WHERE Item_no=".$item_no;	
		$query = $db->prepare($hold_num); //prepares the query
		$query->execute();
		$row = $query->fetch();
		$hold_num = $row[0];
		$sql = "INSERT INTO holds
				VALUES
				(".$item_no.",".$member.",".$hold_num.");";
		$query = $db->prepare($sql); //prepares the query
		$query->execute();

		$sql = "SELECT * FROM holds";
		$query = $db->prepare($sql); //prepares the query
		$query->execute();
		?>
		<h2>Here is the holds table after submitting your request to put an item on hold:</h2>
		<?php
		print_table($query);
	}
}

function print_table($query){
	print "<table border=1>\n";
	$total = $query->columnCount();
	for($counter = 0; $counter<$total; $counter++){
		$meta = $query->getColumnMeta($counter);
		print "<th>{$meta['name']}</th>\n";
		$coln[$counter] = $meta['name'];
	}
	$rows = $query->fetchAll();
	foreach($rows as $row){
		print "<tr>\n";
		for($counter = 0; $counter<$total; $counter++){
			print "<td>{$row[$coln[$counter]]}</td>\n";
		}
		print "</tr>\n";
	}
	print "</table>\n";	
}
?>