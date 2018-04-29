<?php
include("top.html");
include("moocher_shared.php");
?>
	<br>
	<p>Thanks for your submission!</p>
	<h2> Here are the items that we found matching your search parameters</h2>
	<?php  
		print_query();
	?>
  </body>
  <br><br><br>
  <h2> Would you like rent an item?</h2>
  <form action="reserve_item.php">
        <select name="moocher" size="5">
      	  <?php //from here
          $sql = "SELECT Name FROM Moochers ORDER BY Name ASC";
          $query = $db->prepare($sql); //prepares the query
          $query->execute();
          //runs the query
          $rows = $query->fetchAll();
          foreach($rows as $row){
            $choice = $row[0];
            ?>
            <option value="<?= $choice ?>"><?= $choice ?></option><?php
          }
          ?>
        </select>
	Item Number:    <input type="text" name="item_id" value=""><br><br>
  <input type="submit" value="Reserve Item">
</form>
    <form action="moocher_home.php" method="get">	  
      <div>
        <input type="submit" value="Home" /> 
      </div>
    </form>
</html>

<?php
function print_query() {
	include("moocher_shared.php");
	$items = $_GET["item_type"];
	$in_stock = $_GET["stock"];
	$sql = '';

	if ("in" == $in_stock)
		$sql = "SELECT Item_no,Name,Quality,Amazon_URL FROM In_stock WHERE true ";
	if ($in_stock == "either")
		$sql = "SELECT Item_no,Name,Quality,Amazon_URL FROM items WHERE CanMooch=true ";
	if ($in_stock == "else")
		$sql = "SELECT Item_no,Name,Quality,Amazon_URL FROM items WHERE true ";

	$flag = 0;
	if (in_array("Any", $items))
		$sql .= "AND true";
	else { 
		if (in_array("sports_equipment", $items)){
			$sql .= "AND Item_no IN (SELECT Item_no FROM sports_equipment) ";
			$flag = 1;
		}
		if (in_array("musical_instruments", $items))
			if ($flag)
				$sql .= "OR Item_no IN (SELECT Item_no FROM musical_instruments) ";
			else
				$sql .= "AND Item_no IN (SELECT Item_no FROM musical_instruments) ";
		$i = 0;
		$iflag = 0;
		if (in_array("TV Show", $items)) {
			$imploder[$i] = "TV Show";
			$i = $i + 1;
			$iflag = 1;
		}
		if (in_array("Movie", $items)) {
			$imploder[$i] = "Movie";
			$i = $i + 1;
			$iflag = 1;
		}
		if (in_array("Videogame", $items)) {
			$imploder[$i] = "Videogame";
			$i = $i + 1;
			$iflag = 1;
		}

		if ($iflag)
			if ($flag)
				$sql .= " OR Item_no IN (SELECT Item_no FROM media WHERE Movie_TV_Game IN ('" . implode("','",$imploder) . "'))";
			else
				$sql .= " AND Item_no IN (SELECT Item_no FROM media WHERE Movie_TV_Game IN ('" . implode("','",$imploder) . "'))";
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