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

<body>


<div id="personale" align="center" >
<?php
	if(isset($_REQUEST["cmd"]))
		if($_REQUEST["cmd"]=="logout")
			{
				$_SESSION=array();
				session_destroy();
				header ("Location:default.php");
			}
	if(isset($_SESSION["Privileges"])){
		echo "Benvenuto ".$_SESSION["email"] .", <a href=\"details.php?cmd=logout\" >Logout</a>";
		echo "<p>Vai alla tua <a href=\"personale.php\" >pagina personale</a></p>";
		echo "<p>Torna Alla <a href=\"default.php\" >Pagina Iniziale</a></p>";
		echo "<p>Vedi le <a href=\"research.php?cmd=offerte\" >Offerte</a></p>";
	}
	else{
		header ("Location:default.php");	
	}
	
?>
</div>


<?php 
	require_once "component/db_connection.php";
	if(isset($_REQUEST["idv"]) && isset($_REQUEST['voloa']))
	{
		if($_REQUEST['voloa']=='diretto')
		{
			$query="SELECT * FROM viewViaggiDiretti WHERE idViaggio=$_REQUEST[idv]";
			$result=mysql_query($query,$conn);
			$row=mysql_fetch_array($result);
			
			echo "
			<div id=\"volodirettosingolo\"  >
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Viaggio Selezionato</h4>
			<form method=\"GET\" action=\"buy.php\" class=\"form\">
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
					</tr>";
			
			
			echo "
				<tr>
						<td>$row[4] $row[2] $row[6]</td>
						<td>$row[5] $row[3] $row[7]</td>
						<td>$row[8]</td>
						<td>$row[10],00€</td>
						<td>$row[11],00€</td>
						<td>$row[12]</td>
						<td>$row[13]</td>
						<td>$row[14]</td>
				</tr>
			";
			
			echo "
			</table>
			<table align=\"top-left\" style=\"margin:0px\">";
				
				if($row["12"])
				{	
					
					$k=11;
					if($row["12"]<10)
						$k=$row["12"]+1;
						
					echo "
					<tr>
					<td>Numero biglietti prima classe</td>
					<td><select name=\"bigliettiPrima\">";
						$j=0;
						while ($j<$k)
						{
							echo "<option>$j</option> ";
							$j+=1;
						}
						echo"
						</select>
					</td>
					</tr>";	
					$k=11;
					if($row["13"]<10)
						$k=$row["13"]+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>";
					
				}
				else
				{
					
					$k=11;
					if($row["13"]<10)
						$k=$row["13"]+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">;";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>
					<input type=\"hidden\" name=\"bigliettiPrima\" value=\"0\">";
					
				}	
				if(isset($_REQUEST["offerte"]))
					echo"<input type=\"hidden\" name=\"offerte\" value=\"on\">";
				
				echo"
				<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
				<tr><td><input type=\"submit\" class=\"button\" value=\"Acquista\" style=\"float:left;\"></td></tr>
				</table></form></div>";
			}
		else
		{/*Volo singolo con scali*/
			$query="SELECT vvd.* ,s.ordine
					FROM viewViaggiconscali vvs join scali s ON (vvs.idViaggio=s.idViaggioConScali) 
					JOIN viewViaggiDiretti vvd ON(s.idViaggioDiretto=vvd.idViaggio)
					WHERE vvs.idViaggio=$_REQUEST[idv]
					ORDER BY s.ordine ASC";
			$result=mysql_query($query,$conn);
			
			
			echo "
			<div id=\"voloconscalisingolo\" >
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Dei Viaggi Del Viaggio Con Scali Selezionato</h4>
				<form method=\"GET\" action=\"buy.php\" class=\"form\">
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
						<th>Ordine Scalo</th>
					</tr>";
			$numPostiPrima=10000;
			$numPostiSeconda=10000;
			while ($row=mysql_fetch_array($result))
			{
				/*Controlli se ci sono posti di prima e di seconda*/
				if($numPostiPrima>$row["12"])
					$numPostiPrima=$row["12"];
				if($numPostiSeconda>$row["13"])
					$numPostiSeconda=$row["13"];
				echo "
					<tr>
							<td>$row[4] $row[2] $row[6]</td>
							<td>$row[5] $row[3] $row[7]</td>
							<td>$row[8]</td>
							<td>$row[10],00€</td>
							<td>$row[11],00€</td>
							<td>$row[12]</td>
							<td>$row[13]</td>
							<td>$row[14]</td>
							<td>$row[16]</td>
					</tr>";
			}
			echo "
			</table>
			<table align=\"top-left\" style=\"margin:0px\">";
				
				if($numPostiPrima)
				{	
					
					$k=11;
					if($numPostiPrima<10)
						$k=$numPostiPrima+1;
						
					echo "
					<tr>
					<td>Numero biglietti prima classe</td>
					<td><select name=\"bigliettiPrima\">";
						$j=0;
						while ($j<$k)
						{
							echo "<option>$j</option> ";
							$j+=1;
						}
						echo"
						</select>
					</td>
					</tr>";	
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>";
					
				}
				else
				{
					
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">;";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>
					<input type=\"hidden\" name=\"bigliettiPrima\" value=\"0\">";
					
				}

				if(isset($_REQUEST["offerte"]))
					echo"<input type=\"hidden\" name=\"offerte\" value=\"on\">";
				echo"
				<input type=\"hidden\" name=\"idv\" value=\"$_REQUEST[idv]\"
				<tr><td><input type=\"submit\" class=\"button\" value=\"Acquista\" style=\"float:left;\"></td></tr>
				</table></form></div>";
			}
	}
	else/*ANDATA E RITORNO*/
	if(isset($_REQUEST["idva"]) && isset($_REQUEST["idvr"]))
	{	
		$queryca="SELECT idViaggioDiretto FROM ViaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idva]";
		$querycr="SELECT idViaggioDiretto FROM ViaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idvr]";
		$resultca=mysql_query($queryca,$conn);
		$resultcr=mysql_query($querycr,$conn);
		$rowca=mysql_fetch_array($resultca);
		$rowcr=mysql_fetch_array($resultcr);
		$voloa;
		$volor;
		
		if($rowca)
			$voloa="diretto";
		else
			$voloa="scali";
			
		if($rowcr)
			$volor="diretto";
		else
			$volor="scali";

		if($voloa=='diretto' & $volor=='diretto')
		{
			$querya="SELECT * FROM viewViaggiDiretti WHERE idViaggio=$_REQUEST[idva]";
			$resulta=mysql_query($querya,$conn);
			$rowa=mysql_fetch_array($resulta);
			
			echo "<h4 align=\"center\" style=\"color:blue;\">Riepilogo Viaggi Selezionati</h4>
			<h2 align=\"left\" style=\"color:blue;\">Andata</h2>
			<form method=\"GET\" action=\"buy.php\" class=\"form\">
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>	
						<th>Compagnia</th>
					</tr>";
			
			
			echo "
				<tr>
						<td>$rowa[4] $rowa[2] $rowa[6]</td>
						<td>$rowa[5] $rowa[3] $rowa[7]</td>
						<td>$rowa[8]</td>
						<td>$rowa[10],00€</td>
						<td>$rowa[11],00€</td>
						<td>$rowa[12]</td>
						<td>$rowa[13]</td>
						<td>$rowa[14]</td>
				</tr>
			";
			
			echo "
			</table>";				
				
				
			$queryr="SELECT * FROM viewViaggiDiretti WHERE idViaggio=$_REQUEST[idvr]";
			$resultr=mysql_query($queryr,$conn);
			$rowr=mysql_fetch_array($resultr);			
				
			echo" 
			<h2 align=\"left\" style=\"color:blue;\">Ritorno</h2>
			<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
					</tr>";
			
			
			echo "
				<tr>
						<td>$rowr[4] $rowr[2] $rowr[6]</td>
						<td>$rowr[5] $rowr[3] $rowr[7]</td>
						<td>$rowr[8]</td>
						<td>$rowr[10],00€</td>
						<td>$rowr[11],00€</td>
						<td>$rowr[12]</td>
						<td>$rowr[13]</td>
						<td>$rowr[14]</td>
				</tr>
			";
			
			echo "
			</table>
			<table align=\"top-left\" style=\"margin:0px\">";
			$numPostiPrima=0;
			if($rowa["12"]>$rowr["12"])
				$numPostiPrima=$rowr["12"];
			else
				$numPostiPrima=$rowa["12"];
				
			$numPostiSeconda=0;
			if($rowa["13"]>$rowr["13"])
				$numPostiSeconda=$rowr["13"];
			else
				$numPostiSeconda=$rowa["13"];
			
				
				if($numPostiPrima)
				{	
					
					$k=11;
					if($numPostiPrima<10)
						$k=$numPostiPrima+1;
						
					echo "
					<tr>
					<td>Numero biglietti prima classe</td>
					<td><select name=\"bigliettiPrima\">";
						$j=0;
						while ($j<$k)
						{
							echo "<option>$j</option> ";
							$j+=1;
						}
						echo"
						</select>
					</td>
					</tr>";	
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>";
					
				}
				else
				{
					
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>
					<input type=\"hidden\" name=\"bigliettiPrima\" value=\"0\">";
				}
				
				echo"
				<input type=\"hidden\" name=\"idva\" value=\"$_REQUEST[idva]\">
				<input type=\"hidden\" name=\"idvr\" value=\"$_REQUEST[idvr]\">
				<tr><td><input type=\"submit\" class=\"button\" value=\"Acquista\" style=\"float:left;\"></td></tr>
				</table>
				</form>";
		}
		else
			if($voloa=='diretto' & $volor=='scali' )
			{
			
			$querya="SELECT * FROM viewViaggiDiretti WHERE idViaggio=$_REQUEST[idva]";
			$resulta=mysql_query($querya,$conn);
			$rowa=mysql_fetch_array($resulta);
			
			echo "<h4 align=\"center\" style=\"color:blue;\">Riepilogo Viaggi Selezionati</h4>
			<h2 align=\"left\" style=\"color:blue;\">Andata</h2>
			<div id=\"voloandataritornodirettoscali\" >
			<form method=\"GET\" action=\"buy.php\" class=\"form\">
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>	
						<th>Compagnia</th>
					</tr>";
			
			
			echo "
				<tr>
						<td>$rowa[4] $rowa[2] $rowa[6]</td>
						<td>$rowa[5] $rowa[3] $rowa[7]</td>
						<td>$rowa[8]</td>
						<td>$rowa[10],00€</td>
						<td>$rowa[11],00€</td>
						<td>$rowa[12]</td>
						<td>$rowa[13]</td>
						<td>$rowa[14]</td>
				</tr>
			";
			
			echo "
				</table>";
				
				$query="SELECT vvd.* ,s.ordine
					FROM viewViaggiconscali vvs join scali s ON (vvs.idViaggio=s.idViaggioConScali) 
					JOIN viewViaggiDiretti vvd ON(s.idViaggioDiretto=vvd.idViaggio)
					WHERE vvs.idViaggio=$_REQUEST[idvr]
					ORDER BY s.ordine ASC";
				$result=mysql_query($query,$conn);
			
			echo "
			
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Dei Viaggi Del Viaggio Con Scalo Selezionato Per il Ritorno</h4>
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
						<th>Ordine Scalo</th>
					</tr>";
			$numPostiPrimaScali=10000;
			$numPostiSecondaScali=10000;
			while ($row=mysql_fetch_array($result))
			{
				/*Controlli se ci sono posti di prima e di seconda*/
				if($numPostiPrimaScali>$row["12"])
					$numPostiPrimaScali=$row["12"];
				if($numPostiSecondaScali>$row["13"])
					$numPostiSecondaScali=$row["13"];
				echo "
					<tr>
							<td>$row[4] $row[2] $row[6]</td>
							<td>$row[5] $row[3] $row[7]</td>
							<td>$row[8]</td>
							<td>$row[10],00€</td>
							<td>$row[11],00€</td>
							<td>$row[12]</td>
							<td>$row[13]</td>
							<td>$row[14]</td>
							<td>$row[16]</td>							
					</tr>";
			}
			echo "
			</table>
			<table align=\"top-left\" style=\"margin:0px\">";
				
				$numPostiPrima=0;
				$numPostiSeconda=0;
				
				if($numPostiPrimaScali>$rowa["12"])
					$numPostiPrima=$rowa["12"];
				else
					$numPostiPrima=$numPostiPrimaScali;	
					
				if($numPostiSecondaScali>$rowa["13"])
					$numPostiSeconda=$rowa["13"];
				else
					$numPostiSeconda=$numPostiSecondaScali;		
					
				if($numPostiPrima)
				{	
					
					$k=11;
					if($numPostiPrima<10)
						$k=$numPostiPrima+1;
						
					echo "
					<tr>
					<td>Numero biglietti prima classe</td>
					<td><select name=\"bigliettiPrima\">";
						$j=0;
						while ($j<$k)
						{
							echo "<option>$j</option> ";
							$j+=1;
						}
						echo"
						</select>
					</td>
					</tr>";	
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>";
					
				}
				else
				{
					
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">;";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>
					<input type=\"hidden\" name=\"bigliettiPrima\" value=\"0\">";
					
				}			
				echo"
				<input type=\"hidden\" name=\"idva\" value=\"$_REQUEST[idva]\">
				<input type=\"hidden\" name=\"idvr\" value=\"$_REQUEST[idvr]\">
				</table>
				<input type=\"submit\" class=\"button\" value=\"Procedi con l'acquisto\">
				</form></div>";
			}
			else
			if($voloa=='scali' & $volor=='diretto' )
			{
			
			$query="SELECT vvd.* ,s.ordine
					FROM viewViaggiconscali vvs join scali s ON (vvs.idViaggio=s.idViaggioConScali) 
					JOIN viewViaggiDiretti vvd ON(s.idViaggioDiretto=vvd.idViaggio)
					WHERE vvs.idViaggio=$_REQUEST[idva]
					ORDER BY s.ordine ASC";
				$result=mysql_query($query,$conn);	
			
			
			echo "
			<div id=\"voloandataritornoscalidiretto\" >
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Dei Viaggi Del Viaggio Con Scalo Selezionato Per l'Andata</h4>
				<form method=\"GET\" action=\"buy.php\" class=\"form\">
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
						<th>Ordine Scalo</th>
					</tr>";
				$numPostiPrimaScali=10000;
				$numPostiSecondaScali=10000;
				while ($row=mysql_fetch_array($result))
				{
					/*Controlli se ci sono posti di prima e di seconda*/
					if($numPostiPrimaScali>$row["12"])
						$numPostiPrimaScali=$row["12"];
					if($numPostiSecondaScali>$row["13"])
						$numPostiSecondaScali=$row["13"];
					echo "
						<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[10],00€</td>
								<td>$row[11],00€</td>
								<td>$row[12]</td>
								<td>$row[13]</td>
								<td>$row[14]</td>
								<td>$row[16]</td>							
						</tr>";
				}
				echo "
				</table>";
				
			$queryr="SELECT * FROM viewViaggiDiretti WHERE idViaggio=$_REQUEST[idvr]";
			$resultr=mysql_query($queryr,$conn);
			$rowr=mysql_fetch_array($resultr);			
				
			echo" 
			<h2 align=\"left\" style=\"color:blue;\">Ritorno</h2>
			<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
					</tr>";
			
			
			echo "
				<tr>
						<td>$rowr[4] $rowr[2] $rowr[6]</td>
						<td>$rowr[5] $rowr[3] $rowr[7]</td>
						<td>$rowr[8]</td>
						<td>$rowr[10],00€</td>
						<td>$rowr[11],00€</td>
						<td>$rowr[12]</td>
						<td>$rowr[13]</td>
						<td>$rowr[14]</td>
				</tr>
			";
			
			echo "
			</table>
			<table align=\"top-left\" style=\"margin:0px\">";
				
				$numPostiPrima=0;
				$numPostiSeconda=0;
				
				if($numPostiPrimaScali>$rowr["12"])
					$numPostiPrima=$rowr["12"];
				else
					$numPostiPrima=$numPostiPrimaScali;	
					
				if($numPostiSecondaScali>$rowr["13"])
					$numPostiSeconda=$numPostiSecondaScali;
				else
					$numPostiSeconda=$rowr["12"];
				
				if($numPostiPrima)
				{	
					
					$k=11;
					if($numPostiPrima<10)
						$k=$numPostiPrima+1;
						
					echo "
					<tr>
					<td>Numero biglietti prima classe</td>
					<td><select name=\"bigliettiPrima\">";
						$j=0;
						while ($j<$k)
						{
							echo "<option>$j</option> ";
							$j+=1;
						}
						echo"
						</select>
					</td>
					</tr>";	
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>";
					
				}
				else
				{
					
					$k=11;
					if($numPostiSeconda<10)
						$k=$numPostiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">;";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>
					<input type=\"hidden\" name=\"bigliettiPrima\" value=\"0\">";
					
				}			
				echo"
				<input type=\"hidden\" name=\"idva\" value=\"$_REQUEST[idva]\">
				<input type=\"hidden\" name=\"idvr\" value=\"$_REQUEST[idvr]\">
				</table>
				<input type=\"submit\" class=\"button\" value=\"Procedi con l'acquisto\">
				</form></div>";
			
				
			}
			else
			if($voloa=='scali' & $volor=='scali' )
			{
				$query="SELECT vvd.* ,s.ordine
					FROM viewViaggiconscali vvs join scali s ON (vvs.idViaggio=s.idViaggioConScali) 
					JOIN viewViaggiDiretti vvd ON(s.idViaggioDiretto=vvd.idViaggio)
					WHERE vvs.idViaggio=$_REQUEST[idva]
					ORDER BY s.ordine ASC";
				$result=mysql_query($query,$conn);	
			
			
			echo "
			<div id=\"voloconscalisingolo\" >
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Dei Viaggi Del Viaggio Con Scalo Selezionato Per l'Andata</h4>
				<form method=\"GET\" action=\"buy.php\" class=\"form\">
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
						<th>Ordine Scalo</th>
					</tr>";
			$numPostiPrimaAndata=10000;
			$numPostiSecondaAndata=10000;
			while ($row=mysql_fetch_array($result))
			{
				/*Controlli se ci sono posti di prima e di seconda*/
				if($numPostiPrimaAndata>$row["12"])
					$numPostiPrimaAndata=$row["12"];
				if($numPostiSecondaAndata>$row["13"])
					$numPostiSecondaAndata=$row["13"];
				echo "
					<tr>
							<td>$row[4] $row[2] $row[6]</td>
							<td>$row[5] $row[3] $row[7]</td>
							<td>$row[8]</td>
							<td>$row[10],00€</td>
							<td>$row[11],00€</td>
							<td>$row[12]</td>
							<td>$row[13]</td>
							<td>$row[14]</td>
							<td>$row[16]</td>							
					</tr>";
			}
			echo "
			</table></div>";				
				
				$query="SELECT vvd.* ,s.ordine
					FROM viewViaggiconscali vvs join scali s ON (vvs.idViaggio=s.idViaggioConScali) 
					JOIN viewViaggiDiretti vvd ON(s.idViaggioDiretto=vvd.idViaggio)
					WHERE vvs.idViaggio=$_REQUEST[idvr]
					ORDER BY s.ordine ASC";
				$result=mysql_query($query,$conn);
			
			echo "
			<div id=\"voloconscalisingolo\" >
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Dei Viaggi Del Viaggio Con Scalo Selezionato Per il Ritorno</h4>
				<table align=\"top-left\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo Prima Classe</th>
						<th>Prezzo Seconda Classe</th>
						<th>Posti Prima Classe</th>
						<th>Posti Seconda Classe</th>
						<th>Compagnia</th>
						<th>Ordine Scalo</th>
					</tr>";
			$numPostiPrimaRitorno=10000;
			$numPostiSecondaRitorno=10000;
			while ($row=mysql_fetch_array($result))
			{
				/*Controlli se ci sono posti di prima e di seconda*/
				if($numPostiPrimaRitorno>$row["12"])
					$numPostiPrimaRitorno=$row["12"];
				if($numPostiSecondaRitorno>$row["13"])
					$numPostiSecondaRitorno=$row["13"];
				echo "
					<tr>
							<td>$row[4] $row[2] $row[6]</td>
							<td>$row[5] $row[3] $row[7]</td>
							<td>$row[8]</td>
							<td>$row[10],00€</td>
							<td>$row[11],00€</td>
							<td>$row[12]</td>
							<td>$row[13]</td>
							<td>$row[14]</td>
							<td>$row[16]</td>							
					</tr>";
			}
			echo "
			</table>
			<table align=\"top-left\" style=\"margin:0px\">";
				
				if($numPostiSecondaAndata>$numPostiSecondaRitorno)
						$postiSeconda=$numPostiSecondaAndata;
					else
						$postiSeconda=$numPostiSecondaRitorno;
						
				if($numPostiPrimaAndata>$numPostiPrimaRitorno)
						$postiPrima=$numPostiPrimaAndata;
					else
						$postiPrima=$numPostiPrimaRitorno;
						
				if($numPostiPrimaAndata && $numPostiPrimaRitorno)
				{	
					
					
					$k=11;
					if($postiPrima<10)
						$k=$postiPrima+1;
						
					echo "
					<tr>
					<td>Numero biglietti prima classe</td>
					<td><select name=\"bigliettiPrima\">";
						$j=0;
						while ($j<$k)
						{
							echo "<option>$j</option> ";
							$j+=1;
						}
						echo"
						</select>
					</td>
					</tr>";	
					$k=11;					
					if($postiSeconda<10)
						$k=$postiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>";
					
				}
				else
				{
					
					$k=11;					
					if($postiSeconda<10)
						$k=$postiSeconda+1;
						
					echo "
					<tr>
					<td>Numero biglietti seconda classe</td>
					<td><select name=\"bigliettiSeconda\">";
					$j=0;
					while ($j<$k)
				{
					echo "<option>$j</option> ";
					$j+=1;
				}
					echo"
					</td>
					</tr>
					</select>
					<input type=\"hidden\" name=\"bigliettiPrima\" value=\"0\">";
					
				}			
				echo"
				<input type=\"hidden\" name=\"idva\" value=\"$_REQUEST[idva]\">
				<input type=\"hidden\" name=\"idvr\" value=\"$_REQUEST[idvr]\">
				</table>
				<input type=\"submit\" class=\"button\" value=\"Procedi con l'acquisto\">
				</form></div>";
			}
	}
	else
	{
		echo"<h2 style=\"color:red;\">ATTENZIONE errore nella selezione dei viaggi</h2>
			<p>Riesegui la ricerca secondo i tuoi interessi cliccando <a href=\"default.php\">qui</a> 
				oppure torna indietro e scegli entrambi i viaggi per procedere correttamente</p>";
	}
?>

</body>
</html>
