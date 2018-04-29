<?php
include("top.html");
include("moocher_shared.php");
if (!$_SESSION['valid'])
	header("Location: index.php");
?>
	<br>
	<p>Thanks for your submission!</p>
	<h2> Here are the results for your query...</h2>
	<p>...for the following houses:</p>
	<?php
		print_houses();
	?>
	<p>...where they have <?php print_rating(); ?> stars or better.</p>
	
    <h2>Reviews that fit your search parameters are:</h2>
	<?php
		print_reviews();
	?>
	<form action="screenshot2.html" method="get">
	<!-- <input type="submit" value="Adventure Screenshot" /> -->
	</form>
  </body>
</html>

<?php
function print_rating() {
	$stars = $_GET["stars"];
	print $stars;
}

function print_houses() {
	$houses = $_GET["houses"];

	?>
	<ul>
		<?php
			foreach($houses as $house){
		?>
			<li><?=$house?></li>
		<?php
	}
	?>
	</ul>
	<?php
}

function print_reviews() {
	include("moocher_shared.php");
	$stars = $_GET["stars"];
	$houses = $_GET["houses"];
	$where = '';
	$flag = 0;

	if ("all" != $stars) {
		$where .= "WHERE Stars=" . $stars . " "; // add condition for specific languages if applicable
		$flag = 1;
	}

	if (!in_array("Any", $houses)) {
		if ($flag == 1)
			$where .= "AND ";
		else
			$where = "WHERE ";
		$where .= "House_name IN ('" . implode("','",$houses) . "')";
	}
	$sql = "SELECT House_name,Stars,Name,Review FROM customer_reviews JOIN moochers ON Moocher_id=Member_id " . $where . ";";
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