<!-- https://www.tutorialspoint.com/php/php_login_example.htm -->
<?php
   session_start();
   unset($_SESSION["username"]);
   unset($_SESSION["password"]);
   
   echo 'You have cleaned session';
   header('Refresh: 2; URL = index.php');
?>