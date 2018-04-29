<?php
//connect to the database
require 
__DIR__ 
. '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
if(getenv('APP_ENV') == 'development'){
	$dotenv->load();
}
$dotenv->required(['DATABASE_URL']);
// $dbopts = parse_url(getenv('DATABASE_URL'));
// $dbname = ltrim($dbopts["path"], '/');
// $db = new PDO("$dbopts[scheme]:host=$dbopts[host];dbname=$dbname;port=$dbopts[port]", $dbopts["user"], $dbopts["pass"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

$dbopts = parse_url(getenv('DATABASE_URL'));
$dbopts["path"] = ltrim($dbopts["path"], "/");

$db = new PDO("pgsql:" . sprintf("host=%s;port=%s;user=%s;password=%s;dbname=%s",
									$dbopts["host"],
									$dbopts["port"],
									$dbopts["user"],
									$dbopts["pass"],
									ltrim($dbopts["path"], "/")
									));

//this line create a new PHP Data Object
//its usage is as PDO("DB_Protocol:host=your_hostname;dbname=your_db;","Username","password");
//where DB_Protocol is the type of db you are trying to connect to
//for example sqlite or mysql
//for sqlite it is even simpler
//it would be something like PDO("sqlite:world.sqlite");

?>