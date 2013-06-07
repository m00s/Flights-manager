<?php
	$host="localhost"; 
	$user="root"; 
	$pwd= "root";
	$dbname="Airlines";
	$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
	mysql_select_db($dbname);
?>