<?php
include("top.html");
include("moocher_shared.php");
?>
	<br>
	<p>Thanks for your submission!</p>
	<h2> Here are the results for your query...</h2>
	<?php
		print_query();
	?>
  </body>
</html>

<?php
function print_query() {
	include("moocher_shared.php");
	$items = $_GET["item_type"];
	$in_stock = $_GET["stock"];
	$sql = '';

	if ("In Stock" == $in_stock)
		$sql = "SELECT Item_no,Name,Quality,Amazon_URL FROM In_stock WHERE ";
	else
		$sql = "SELECT Item_no,Name,Quality,Amazon_URL FROM Items WHERE CanMooch=1 ";

	if (in_array("Any", $items))
		$sql .= "AND 1;";
	else { 
		if (in_array("sports_equipment", $items))
			$sql .= "AND Item_no IN (SELECT Item_no FROM sports_equipment) ";
		if (in_array("musical_instruments", $items))
			$sql .= "AND Item_no IN (SELECT Item_no FROM musical_instruments) ";
		$i = 0;
		$flag = 0;
		if (in_array("TV Show", $items)) {
			$imploder[i] = "TV Show";
			$i = $i + 1;
			$flag = 1;
		}
		if (in_array("Movie", $items)) {
			$imploder[i] = "Movie";
			$i = $i + 1;
			$flag = 1;
		}
		if (in_array("Videogame", $items)) {
			$imploder[i] = "Videogame";
			$i = $i + 1;
			$flag = 1;
		}

		if ($flag)
			$sql .= " AND Item_no IN (SELECT Item_no FROM media WHERE Movie_TV_Game IN ('" . implode("','",$items) . "')";

		$sql .= ";";
	}

  	$query = $db->prepare($sql); //prepares the query
	$query->execute();
	print_table($query);
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