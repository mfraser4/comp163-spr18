  <?php
include("top.html");
include("moocher_shared.php");
?>
  <h2> Would you like to insert a new item into the database?</h2>
  <form action="insert_item.php">
  Name:    <input type="text" name="Name" value="">
  URL:     <input type="text" name="URL" value="">
  Quality: <input type="text" name="Quality" value=""><br><br>
        <select name="houses" size="5">
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
      </select><br><br>
  (NOTE: By default, it will automatically be Moochable)<br>
  <input type="submit" value="Insert new item">
</form>
 <h2> Would you like to delete an item from the database?</h2>
  <form action="delete_item.php">
  Item Number:    <input type="text" name="item_no" value="">
  <input type="submit" value="Delete Item"><br><br>
</form>
    <form action="moocher_home.php" method="get">	  
      <div>
        <input type="submit" value="Home" /> 
      </div>
    </form>

</form>
 <h2> Would you like to update the contact info of one of the Moochers?</h2>
  <form action="update_moocher.php">
  Moocher ID:    <input type="text" name="moocher_id" value="">
  Contact Info:  <input type="text" name="contact_info" value="">
  <p>Please specify which field you are updating before submitting.</p>
      <input type="radio" name="table" value="Email" checked="true">Email
    <input type="radio" name="table" value="Phone">Phone
    <input type="radio" name="table" value="Address">Address<br> 
  <input type="submit" value="Update Moocher"><br><br>
</form>
    <form action="moocher_home.php" method="get">	  
      <div>
        <input type="submit" value="Home" /> 
      </div>
    </form>