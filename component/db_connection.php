<?php
	/*
	$host="localhost"; 
	$user="root"; 
	$pwd= "root";
	*/
	$host="basidati"; 
	$user="msartore"; 
	$pwd= "Ou3EoAvD";
	$dbname="msartore-ES";
	$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
	mysql_select_db($dbname);
	
?>