<? session_start() ?>
<html>
	<head>
		<title> 
			Airlines 
		</title>
		<head>
			<link rel="stylesheet" type="text/css" href="../component/style.css">
		</head>
	</head>
	
	<body link="#002089" alink="#002089" vlink="#002089">
		<table align=\"center\" style=\"margin-top:50px\">
		<?
		if(isset($_SESSION['Admin']))
		{
		}
		else
			echo "Non sei autorizzato a stare qui. </br> Effettua il <a href=\"login.php\"> login come admin </a>";	
		?>
		</table>
	</body>
</html>
