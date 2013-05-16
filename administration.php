<? session_start(); ?>
<html>
<head>
	<title> 
		Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
</head>

<body link="#002089" alink="#002089" vlink="#002089">
	<table align=\"center\" style=\"margin-top:50px\">
	<?
	if(isset($_SESSION['Admin']))
	{
		$id = $_SESSION['Admin'];
		echo $id."&nbsp&nbsp<a href=\"login.php?cmd=out\">Logout</a><br/>";
		if(isset($_GET['manage']))
		{
			$cmd=$_GET['manage'];
			switch($cmd)
			{
				case "voli":
					
					break;
				case "viaggi":
					break;
				case "aerei":
					break;
				case "aeroporti":
					break;
			}
		}
		else
		{
			echo "<div style=\"margin-top:30px\">
				<h2>Scegli la categoria</h2>
				<ul>
					<li><a href=\"administration.php?manage=voli\">Voli</a></li>
					<li><a href=\"administration.php?manage=viaggi\">Viaggi</a></li>
					<li><a href=\"administration.php?manage=aerei\">Aerei</a></li>
					<li><a href=\"administration.php?manage=aeroporti\">Aeroporti</a></li>
				</ul>
			</div>";
		}
	}
	else
		echo "Non sei autorizzato a stare qui. </br> Effettua il <a href=\"login.php\"> login come admin </a>";
			
	?>
	</table>
</body>
</html>
