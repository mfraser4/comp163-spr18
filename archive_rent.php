<?php
include("top.html");
include("moocher_shared.php");
?>
	<br>
	<h2> Here is the Rent Archive table:</h2>
	<?php
		print_query();
	?>
  </body>
      <form action="moocher_home.php" method="get">	  
      <div>
        <input type="submit" value="Home" /> 
      </div>
    </form>
</html>

<?php
function print_query() {
	include("moocher_shared.php");

	$sql = "SELECT * FROM archive_rent_log";

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