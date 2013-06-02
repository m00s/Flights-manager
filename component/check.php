<?php
	session_start();
	$insert=$_POST['password'];
	$login=$_POST['mail'];
	if($insert!="" && $login!="")
	{
		require "db_connection.php";
		$query="SELECT * FROM Utenti WHERE email=\"$login\"";
		$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		$arr = mysql_fetch_assoc($result);
		$pwd = $arr['password'];
		if ($pwd == sha1($insert))
		{
			if($arr['type'] == "Guest"){
				header("Location: http://localhost:8888/default.php");
				$_SESSION['Privileges'] = $arr['type'];
				$_SESSION['email'] = $arr['email'];
			}
			else{
				header("Location: http://localhost:8888/admin/managevoli.php");
				$_SESSION['Privileges'] = $arr['type'];
				$_SESSION['email'] = $arr['email'];
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