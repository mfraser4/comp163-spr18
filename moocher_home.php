<?php
include("top.html");
include("moocher_shared.php");
?>
<html>
  <body>
    <p>Welcome, fellow Moocher, to the site where you can freeload without losing your friends!  Please enter in your search parameters and we will show you what your friends have listed as moochable.</p>

    <form action="moocher_results.php" method="get">
      <div>
        <h2>These are the items our Moochers love!</h2>
          <?php print_favorites() ?>
        <h2>Which type of item do you wish to search through?</h2>

        <select name="item_type[]" size="5" multiple="multiple">
          <option value="Any" selected='selected'>Any</option>
          <option value="Movie">Movies</option>
          <option value="Videogame">Videogames</option>
          <option value="TV Show">TV Shows</option>
          <option value="sports_equipment">Sports Equipment</option>
          <option value="musical_instruments">Musical Instruments</option>
        </select>
	   </div>
      <div>
		<h2>Do you wish to see only currently available items, items also on hold, or all items, even those hidden by the house owners?</h2>
		<input type="radio" name="stock" value="in">In Stock
		<input type="radio" name="stock" value="either">Hold and In Stock
    <input type="radio" name="stock" value="else" checked="true">All Items

      </div>	  
      <div>
        <input type="submit" value="Submit" /> 
      </div>
    </form>
  </body>
  <body>
  <h2> If you would like to see some reviews of our houses, please select the desired rating and click the submit button below.</h2>

    <form action="moocher_reviews.php" method="get">
      <div>
    <input type="radio" name="stars" value="1">1
    <input type="radio" name="stars" value="2">2
    <input type="radio" name="stars" value="3">3
    <input type="radio" name="stars" value="4">4
    <input type="radio" name="stars" value="5">5
    <input type="radio" name="stars" value="all" checked="true">All<br>    
      <div>
      <div>   
      <h2>Which houses(s) do you prefer to see?</h2>
      <select name="houses[]" size="5" multiple="multiple">
        <option value="Any" selected='selected'>Any House</option>
        <?php //from here
        $sql = "SELECT DISTINCT Name FROM houses ORDER BY Name ASC";
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
    </div>
        <input type="submit" value="Submit" /> 
      </div>
    </form>
     <form action="moocher_rent.php" method="get">  
      <h2>To view the rent log, click the submit button below</h2> 
      <div>
        <input type="submit" value="Submit" /> 
      </div>
    </form>
     <form action="moocher_mooch.php" method="get">  
      <h2>To view the Moochers table, click the submit button below</h2> 
      <div>
        <input type="submit" value="Submit" /> 
      </div>
    </form>
     <form action="moocher_inserts.php" method="get">  
      <h2>To insert, update, and delete from tables, click the submit button below</h2> 
      <div>
        <input type="submit" value="Submit" /> 
      </div>
    </form> 
     <form action="archive_rent.php" method="get">  
      <h2>To view the rent logs of transactions where one of the factors no longer exists, click below to view the archive</h2> 
      <div>
        <input type="submit" value="Archived rent logs" /> 
      </div>
    </form>    
    <form action="logout.php" method="get">  
      <h2>To logout, click the logout button below...</h2> 
      <div>
        <input type="submit" value="Logout" /> 
      </div>
    </form>

  </body>
</html>

<?php
function print_favorites() {
  include("moocher_shared.php");
  $sql = "SELECT * FROM item_popularity LIMIT 5;";
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


