<?
	require "functions.php";
	$insert=$_POST['password'];
	$login=$_POST['mail'];
	if($insert!="" && $login!="")
	{
		$insert=$_POST['password'];
		$login=$_POST['mail'];
		$arr = get_record($login, "mail");
		$pwd = $arr['password'];
		if ($pwd == $insert)
		{
			header("Location: http://localhost:8888/default.php");
			//header("Location: http://basidati/basidati/~mabarich/1.php");
			$rec = get_record($login, "mail");
			echo $rec['id'];
			session_start();
			$_SESSION['id'] = $rec['id'];
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