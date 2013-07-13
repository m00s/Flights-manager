<?php session_start();?>
<html>
<head>
	<title> 
		Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="\component\style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
</head>

<body link="#002089" alink="#002089" vlink="#002089">

<div id="personale" align="center" >
<?php
	if(isset($_REQUEST["cmd"]))
		if($_REQUEST["cmd"]=="logout")
			{
				$_SESSION=array();
				session_destroy();
				header("Location:/basidati/~msartore/default.php");
			}
	if(isset($_SESSION["Privileges"])){
		echo "Benvenuto ".$_SESSION["email"] .", <a href=\"default.php?cmd=logout\" >Logout</a>";
		echo "<p>Vai alla tua <a href=\"personale.php\" >pagina personale</a></p>";
		echo "<p>Vedi le <a href=\"research.php?cmd=offerte\" >Offerte</a></p>";
	}
	else{
		echo "<p>Vedi le <a href=\"research.php?cmd=offerte\" >Offerte</a></p>";
		echo "Devi essere loggato o registrato per effettuare una prenotazione <a href=\"login.php\" class=\"postlink\" target=\"_new\">Login o Registrati</a>";
	}
?>
</div>

<div id="filtri" align="center" style="float:right; width:25%;" >
	<?php require_once "filter.php";
	
		if(isset($_REQUEST['err']))
		{
			echo "<p style=\"color:red;\">Errore data. Ripetere la ricerca</p>";
		}
		if(isset($_COOKIE["Destinazioni"]))
		{
			$destinazioni=explode(',',$_COOKIE["Destinazioni"]);
			echo "<h3 style=\"color:blue\"> Ultima ricerca effettuata</h3>
					<p>DA: $destinazioni[0]</p>
					<p>A: $destinazioni[1]</p>";
		}
			
	?>
</div>

<div id="voliDelGiorno" align="center" color="123456" style="width:75%; float:left;">
	<?php
		require_once "component/db_connection.php";
		$query="SELECT * FROM viewViaggiDiretti WHERE postiSeconda>1 AND stato='previsto' AND giorno>NOW() ORDER BY giorno ASC LIMIT 0,20";
		$result=mysql_query($query,$conn);
			
		if(isset($_SESSION["Privileges"]))
		{
		
		echo "<h4>Voli previsti a breve <br></h4>
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>
					<th>Partenza</th>
					<th>Arrivo</th>
					<th>Durata</th>
					<th>Giorno</th>
					<th>Prezzo</th>
					<th>Acquista</th>
					
				</tr>";
		while($row=mysql_fetch_array($result))
		{
		echo "
			<form method=\"GET\" action=\"details.php\" >
			<tr>
					<td>$row[4] $row[2] $row[6]</td>
					<td>$row[5] $row[3] $row[7]</td>
					<td>$row[8]</td>
					<td>$row[1]</td>
					<td>$row[11],00€</td>
					<td height=\"25\">
					<input type=\"hidden\" name=\"voloa\" value=\"diretto\">
					<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
					<input type=\"image\" src=\"images\go.png\" value=\"Dettagli\ height=\"30\" width=\"30\" alt=\"Acquista\"></td>
			</tr>
			</form>
			";			
		}
		echo "</table>";		
		}
		else{
			
		echo "<h4>Voli Previsti a breve <br> </h4>
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>
					<th>Partenza</th>
					<th>Arrivo</th>
					<th>Durata</th>
					<th>Giorno</th>
					<th>Prezzo</th>					
				</tr>";
		while($row=mysql_fetch_array($result))
		{
		echo "<form method=\"GET\" class=\"form\">
			<tr>
					<td>$row[4] $row[2] $row[6]</td>
					<td>$row[5] $row[3] $row[7]</td>
					<td>$row[8]</td>
					<td>$row[1]</td>
					<td>$row[11],00€</td>
			</tr>
		</form>";		
			
		}
		echo "</table>";
		}
		
	?>	
</div>

</body>
</html>
