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
	<?
	require "functions.php";
	$q = "SELECT vi.giorno, v.oraP, v.oraA, a1.citta AS da, a2.citta AS a, timediff(v.oraA,v.oraP) FROM (Viaggi vi JOIN Voli v ON vi.voloId=v.numero), Aeroporti a1, Aeroporti a2 WHERE a1.id=v.da AND a2.id=v.a ORDER BY vi.giorno LIMIT 0,5";
	$result = getByQuery($q);
	$durata = getDurata(row[1], row[2]);
	echo row[1];
	echo $q;
	?>
</body>
</html>
