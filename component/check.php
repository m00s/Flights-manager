<?php
	session_start();
	$insert=$_POST['password'];
	$login=$_POST['mail'];
	if($insert!="" && $login!="")
	{	
		require_once "db_connection.php";
		$query="SELECT * FROM Anagrafiche NATURAL JOIN Utenti WHERE email=\"$login\"";
		$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		$arr = mysql_fetch_assoc($result);
		$pwd = $arr['password'];
		if ($pwd == sha1($insert))
		{
			if($arr['type'] == "Guest"){
				header("Location: /basidati/~msartore/default.php");
				$_SESSION['Privileges'] = $arr['type'];
				$_SESSION['email'] = $arr['email'];
				$_SESSION['id'] = $arr['idAnag'];
			}
			else{
				if(isset($_SESSION['acquista']))
					$path=$_SESSION['acquista']."&prima=".$_SESSION['bigliettiPrima']."&seconda=".$_SESSION['bigliettiSeconda'];
				else{
					$path="/basidati/~msartore/admin/administration.php";
					}
					
				header("Location: $path");
				$_SESSION['Privileges'] = $arr['type'];
				$_SESSION['email'] = $arr['email'];
				$_SESSION['id'] = $arr['idAnag'];
			}
		}
		else
		{
			header("Location: /basidati/~msartore/login.php?a=nauth");
		}
	}
	else
	{
		header("Location: /basidati/~msartore/login.php?e=ae");
	}
?>