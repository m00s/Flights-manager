<?php session_start();?>
<html>
<head>
	<title> 
		Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="\component\style.css">
	</head>
</head>

<body>
<div>
	<?php
	require "component/db_connection.php";
	if(isset($_REQUEST["idv"]) && isset($_REQUEST["passeggero"]))
	{
		$query="SELECT p.posto,vvd.*,p.idPrenotazione
					FROM Prenotazioni p JOIN viewviaggiDiretti vvd ON(p.idViaggio=vvd.idViaggio)
					WHERE p.idViaggioConScali=$_REQUEST[idv] AND p.passeggero=$_REQUEST[passeggero] AND p.stato='valido'";
		$result=mysql_query($query,$conn);
			
			echo "
			
			<h4 align=\"center\" style=\"color:blue;\">Riepilogo Dei Viaggi Del Viaggio Con Scalo Selezionato</h4>
				<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Numero Prenotazione</th>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Giorno</th>
						<th>Posto Prima Classe</th>
						<th>Compagnia</th>
					</tr>";
			while ($row=mysql_fetch_array($result))
			{				
				echo "
					<tr>
							<td>$row[17]</td>
							<td>$row[5] $row[3] $row[7]</td>
							<td>$row[6] $row[4] $row[8]</td>
							<td>$row[9]</td>
							<td>$row[2]</td>";
							if($row["0"])
								echo"<td>$row[0]</td>";
							else
								echo"<td>No</td>";
							echo"<td>$row[15]</td
					</tr>";
			}
			echo "
			</table>";
	}
	
	if(isset($_REQUEST["rimborso"]))
	{
		/*diretti*/
		
			$quueryprima="SELECT posto FROM Prenotazioni WHERE idPrenotazione=$_REQUEST[idp]";
			$resultprima=mysql_query($query,$conn);
			
			if($resultprima)
			{
				if(isset($_REQUEST["idp"]))
				{
				
					$query="SELECT * FROM viewViaggiDiretti WHERE luogoP='$_REQUEST[luogopartenza]' AND luogoA='$_REQUEST[luogoarrivo]' AND stato='previsto' AND postiPrima>1";
								
					$result=mysql_query($query,$conn);
						
						echo "
						<h2>Seleziona il volo che ti piace e sarai rimborsato</h2>
						<form method=\"GET\" action=\"appo.php\" class=\"form\">
							<div id=\"voliAndata\" align=\"center\" style=\"width:50%; float:left; background-color:#059899\">
							<h4>Voli da: $_REQUEST[luogopartenza] <br> a:$_REQUEST[luogoarrivo] <br> </h4>
								<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
								<tr>
									<th>Partenza</th>
									<th>Arrivo</th>
									<th>Durata</th>
									<th>Giorno</th>
									<th>Acquista</th>
								</tr>";
						while($row=mysql_fetch_array($result))
						{
						echo "
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[1]</td>
								<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
								<td><input type=\"submit\" value=\"Acquista\"></td>
							</tr>";	
						
						}
						echo"</table>
						<input type=\"hidden\" name=\"idpass\" value=\"$_REQUEST[idpass]\">
						<input type=\"hidden\" name=\"rimborsato\" value=\"on\">
						<input type=\"hidden\" name=\"idp\" value=\"$_REQUEST[idp]\">
						<input type=\"hidden\" name=\"classe\" value=\"Prima\">						
						<input type=\"hidden\" name=\"idacquirente\" value=\"$_REQUEST[idacquirente]\">
						</form></div>";
				}
				else
				{
					/*scali*/
					$query="SELECT * FROM viewViaggiConScali WHERE luogoP='$_REQUEST[luogopartenza]' AND luogoA='$_REQUEST[luogoarrivo]' AND stato='previsto' AND postiPrima>1";
									
						$result=mysql_query($query,$conn);
							
							echo "
							<h2>Seleziona il volo che ti piace e sarai rimborsato</h2>
							<form method=\"GET\" action=\"appo.php?rimborsato=on&\" class=\"form\">
								<div id=\"voliAndata\" align=\"center\" style=\"width:50%; float:left; background-color:#059899\">
								<h4>Voli da: $_REQUEST[luogopartenza] <br> a:$_REQUEST[luogoarrivo] <br> </h4>
									<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
									<tr>
										<th>Partenza</th>
										<th>Arrivo</th>
										<th>Giorno</th>
										<th>Acquista</th>
										
									</tr>";
							while($row=mysql_fetch_array($result))
							{
							echo "
								<tr>
									<td>$row[4] $row[2] $row[6]</td>
									<td>$row[5] $row[3] $row[7]</td>
									<td>$row[1]</td>
									<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
									<td><input type=\"submit\" value=\"Acquista\"></td>
								</tr>";	
							
							}
							echo"</table>
							<input type=\"hidden\" name=\"classe\" value=\"Prima\">	
							<input type=\"hidden\" name=\"idacquirente\" value=\"$_REQUEST[idacquirente]\">
							<input type=\"hidden\" name=\"idpass\" value=\"$_REQUEST[idpass]\">
							<input type=\"hidden\" name=\"idvs\" value=\"$_REQUEST[idvs]\">
							<input type=\"hidden\" name=\"prezzo\" value=\"$_REQUEST[prezzo]\">
							<input type=\"hidden\" name=\"rimborsato\" value=\"on\">
							</form></div>";
				}
			}
			else
				{
				if(isset($_REQUEST["idp"]))
				{
					$query="SELECT * FROM viewViaggiDiretti WHERE luogoP='$_REQUEST[luogopartenza]' AND luogoA='$_REQUEST[luogoarrivo]' AND stato='previsto' AND postiSeconda>1";
								
					$result=mysql_query($query,$conn);
						
						echo "
						<h2>Seleziona il volo che ti piace e sarai rimborsato</h2>
						<form method=\"GET\" action=\"appo.php\" class=\"form\">
							<div id=\"voliAndata\" align=\"center\" style=\"width:50%; float:left; background-color:#059899\">
							<h4>Voli da: $_REQUEST[luogopartenza] <br> a:$_REQUEST[luogoarrivo] <br> </h4>
								<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
								<tr>
									<th>Partenza</th>
									<th>Arrivo</th>
									<th>Durata</th>
									<th>Giorno</th>
									<th>Acquista</th>
								</tr>";
						while($row=mysql_fetch_array($result))
						{
						echo "
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[1]</td>
								<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
								<td><input type=\"submit\" value=\"Acquista\"></td>
							</tr>";	
						
						}
						echo"</table>
						<input type=\"hidden\" name=\"classe\" value=\"Seconda\">	
						<input type=\"hidden\" name=\"idpass\" value=\"$_REQUEST[idpass]\">
						<input type=\"hidden\" name=\"rimborsato\" value=\"on\">
						<input type=\"hidden\" name=\"idp\" value=\"$_REQUEST[idp]\">
						<input type=\"hidden\" name=\"idacquirente\" value=\"$_REQUEST[idacquirente]\">
						</form></div>";
				}
				else
				{
					/*scali*/
					$query="SELECT * FROM viewViaggiConScali WHERE luogoP='$_REQUEST[luogopartenza]' AND luogoA='$_REQUEST[luogoarrivo]' AND stato='previsto' AND postiSeconda>1";
									
						$result=mysql_query($query,$conn);
							
							echo "
							<h2>Seleziona il volo che ti piace e sarai rimborsato</h2>
							<form method=\"GET\" action=\"appo.php?rimborsato=on&\" class=\"form\">
								<div id=\"voliAndata\" align=\"center\" style=\"width:50%; float:left; background-color:#059899\">
								<h4>Voli da: $_REQUEST[luogopartenza] <br> a:$_REQUEST[luogoarrivo] <br> </h4>
									<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
									<tr>
										<th>Partenza</th>
										<th>Arrivo</th>
										<th>Giorno</th>
										<th>Acquista</th>
										
									</tr>";
							while($row=mysql_fetch_array($result))
							{
							echo "
								<tr>
									<td>$row[4] $row[2] $row[6]</td>
									<td>$row[5] $row[3] $row[7]</td>
									<td>$row[1]</td>
									<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
									<td><input type=\"submit\" value=\"Acquista\"></td>
								</tr>";	
							
							}
							echo"</table>
							<input type=\"hidden\" name=\"classe\" value=\"Seconda\">	
							<input type=\"hidden\" name=\"idacquirente\" value=\"$_REQUEST[idacquirente]\">
							<input type=\"hidden\" name=\"idpass\" value=\"$_REQUEST[idpass]\">
							<input type=\"hidden\" name=\"idvs\" value=\"$_REQUEST[idvs]\">
							<input type=\"hidden\" name=\"prezzo\" value=\"$_REQUEST[prezzo]\">
							<input type=\"hidden\" name=\"rimborsato\" value=\"on\">
							</form></div>";
				}
			}
		}
	
	if(isset($_REQUEST["rimborsato"]))
	{
		if(isset($_REQUEST["idp"]))
		{	
			if(isset($_REQUEST["classe"]) && $_REQUEST["classe"]=="Prima")
				{
					$queryviaggiovecchio="SELECT * FROM Prenotazioni WHERE idPrenotazione=$_REQUEST[idp]";
					$resultvv=mysql_fetch_array(mysql_query($queryviaggiovecchio,$conn));
					$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
													WHERE vd.idViaggioDiretto=$_REQUEST[idviaggio] AND pps.numero NOT IN 
														(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$rowcvs[1])LIMIT 0,1";
					$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
					$query="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
														type,prezzoPrenotazione,posto) 
							VALUES ($_REQUEST[idviaggio],NULL,$_REQUEST[idacquirente],$_REQUEST[idpass],$resultvv[5],$resultvv[6],'seconda',$resultvv[9],'resultpostoprima[0]')";
					$queryaggiorna="UPDATE Prenotazioni SET stato='rimborsato' WHERE idPrenotazione=$_REQUEST[idp]";
					echo $query;
					echo $queryaggiorna;
					$result=mysql_query($query,$conn);
					$result=mysql_query($queryaggiorna,$conn);
					
					$query="CALL ScalaPosti(1,1,$_REQUEST[idviaggio],0);";
					mysql_query($query,$conn);
				}
				else
				{
					if(isset($_REQUEST["classe"]) && $_REQUEST["classe"]=="Seconda")
					{
						$queryviaggiovecchio="SELECT * FROM Prenotazioni WHERE idPrenotazione=$_REQUEST[idp]";
					$resultvv=mysql_fetch_array(mysql_query($queryviaggiovecchio,$conn));
					
					$query="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
														type,prezzoPrenotazione) 
							VALUES ($_REQUEST[idviaggio],NULL,$_REQUEST[idacquirente],$_REQUEST[idpass],$resultvv[5],$resultvv[6],'seconda',$resultvv[9])";
					$queryaggiorna="UPDATE Prenotazioni SET stato='rimborsato' WHERE idPrenotazione=$_REQUEST[idp]";
					$result=mysql_query($query,$conn);
					$result=mysql_query($queryaggiorna,$conn);
					
					$query="CALL ScalaPosti(1,0,$_REQUEST[idviaggio],0);";
					mysql_query($query,$conn);
					}
					
				}
		}
		else
		{
			if(isset($_REQUEST["classe"]) && $_REQUEST["classe"]=="Prima")
			{
				$queryaggiorna="UPDATE Prenotazioni SET stato='rimborsato' WHERE idViaggioConScali=$_REQUEST[idvs] AND acquirente=$_REQUEST[idacquirente]
																				AND passeggero=$_REQUEST[idpass]";
					$result=mysql_query($queryaggiorna,$conn);
					
					$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
																(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idviaggio]";
					$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
					$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
					$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
					$queryNumBagagli="SELECT numeroBagagli FROM Prenotazioni WHERE idViaggioConScali=$_REQUEST[idviaggio] AND acquirente=$_REQUEST[idacquirente] AND passeggero=$_REQUEST[idpass] LIMIT 0,1";
					$resultnumerobagagli=mysql_fetch_array(mysql_query($queryNumBagagli,$conn));
					while($rowcvs=mysql_fetch_array($resultcvs))
					{
						$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
												WHERE vd.idViaggioDiretto=$rowcvs[1] AND pps.numero NOT IN 
													(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$rowcvs[1])LIMIT 0,1";
						$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
						
						$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																							type,prezzoPrenotazione,posto) 
																	VALUES ($rowcvs[1],FALSE,$_REQUEST[idviaggio],$_REQUEST[idacquirente],$_REQUEST[idpass],$resultnumerobagagli[0],
																	$resultidbagaglio[0],'seconda',$_REQUEST[prezzo],'$resultpostoprima[0]')";
						mysql_query($queryinsertprenotazione,$conn);
					}
					
					$query="CALL ScalaPosti(1,1,$_REQUEST[idviaggio],1);";
					mysql_query($query,$conn);
			}
			else
			{
				if(isset($_REQUEST["classe"]) && $_REQUEST["classe"]=="Seconda")
				{
					$queryaggiorna="UPDATE Prenotazioni SET stato='rimborsato' WHERE idViaggioConScali=$_REQUEST[idvs] AND acquirente=$_REQUEST[idacquirente]
																				AND passeggero=$_REQUEST[idpass]";
					$result=mysql_query($queryaggiorna,$conn);
					
					$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
																(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idviaggio]";
					$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
					$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
					$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
					$queryNumBagagli="SELECT numeroBagagli FROM Prenotazioni WHERE idViaggioConScali=$_REQUEST[idviaggio] AND acquirente=$_REQUEST[idacquirente] AND passeggero=$_REQUEST[idpass] LIMIT 0,1";
					$resultnumerobagagli=mysql_fetch_array(mysql_query($queryNumBagagli,$conn));
					while($rowcvs=mysql_fetch_array($resultcvs))
					{
						$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																							type,prezzoPrenotazione) 
																	VALUES ($rowcvs[1],FALSE,$_REQUEST[idviaggio],$_REQUEST[idacquirente],$_REQUEST[idpass],$resultnumerobagagli[0],
																	$resultidbagaglio[0],'seconda',$_REQUEST[prezzo])";
						mysql_query($queryinsertprenotazione,$conn);
					}
					
					$query="CALL ScalaPosti(1,0,$_REQUEST[idviaggio],1);";
					mysql_query($query,$conn);
				}
			}
		}
		
		header ("Location:personale.php");
	}
	
	?>
</div>
</body>
</html>