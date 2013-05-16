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
	require "functions.php";
	$voli = getVoli();
	?>
	</table>
</body>
</html>
