<?php
	require "db_connection.php";
	$insert=$_POST['password'];
	$login=$_POST['mail'];
	if($insert!="" && $login!="")
	{
		$query="SELECT password, type, mail FROM Utenti WHERE mail=\"$login\"";
		$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		$arr = mysql_fetch_assoc($result);
		$pwd = $arr['password'];
		if ($pwd == sha1($insert))
		{
			session_start();
			if($arr['type'] == "Guest"){
				header("Location: http://localhost:8888/default.php");
				$_SESSION['Guest'] = $rec['mail'];
			}
			else{
				header("Location: http://localhost:8888/administration.php");
				$_SESSION['Admin'] = $arr['mail'];
			}
		}
		else
		{
			header("Location: http://localhost:8888/login.php?e=ae");
		}
	}
	else
	{
		header("Location: http://localhost:8888/login.php?e=ae");
	}
?>