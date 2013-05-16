<?
	require "functions.php";
	$insert=$_POST['password'];
	$login=$_POST['mail'];
	if($insert!="" && $login!="")
	{
		$query="SELECT password, type FROM Utenti WHERE mail=\"$login\"";
		$arr = executeQ($query);		
		$pwd = $arr['password'];
		if ($pwd == sha1($insert))
		{
			//header("Location: http://basidati/basidati/~msartore/1.php");
			if($arr['type'] == "Guest")
				header("Location: http://localhost:8888/default.php");
			else
				header("Location: http://localhost:8888/admin.php");
			session_start();
			$_SESSION['id'] = $rec['mail'];
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