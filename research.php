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
<body link="#002089" alink="#002089" vlink="#002089">


<div id="personale" align="center" style="background-color:#FF4030;">
<?php
	if(isset($_REQUEST["cmd"]))
		if($_REQUEST["cmd"]=="logout")
			{
				$_SESSION=array();
				session_destroy();
				header("Location: /basidati/~msartore/default.php");
			}
	if(isset($_SESSION["Privileges"])){
		echo "Benvenuto ".$_SESSION["email"] .", <a href=\"research.php?cmd=logout\" >Logout</a>";
		echo "<p>Torna Alla <a href=\"default.php\" >Pagina Iniziale</a></p>";
		echo "<p>Vai alla tua <a href=\"personale.php\" >pagina personale</a></p>";
		echo "<p>Vedi le <a href=\"research.php?cmd=offerte\" >Offerte</a></p>";
	}
	else{
		echo "<p>Torna Alla <a href=\"default.php\" >Pagina Iniziale</a></p>";
		echo "<p>Vedi le <a href=\"research.php?cmd=offerte\" >Offerte</a></p>";
		echo "Devi essere loggato o registrato per effettuare una prenotazione <a href=\"login.php\" class=\"postlink\" target=\"_new\">Login o Registrati</a>";
	}
?>
</div>

<?php

	require "\component\db_connection.php";
	
	if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"]=="offerte")
	{
		$queryoffertedirette="SELECT vvd.*,o.disponibili FROM viewViaggiDiretti vvd JOIN Offerte o ON (vvd.idViaggio=o.idViaggio) 
							WHERE vvd.postiSeconda>1 AND o.disponibili>1";		
		$resultoffertedirette=mysql_query($queryoffertedirette,$conn);
		
		$queryoffertescali="SELECT vvs.*,o.disponibili FROM viewViaggiConScali vvs JOIN Offerte o ON (vvs.idViaggio=o.idViaggio) 
							WHERE vvs.postiSeconda>1 AND o.disponibili>1";		
		$resultoffertescali=mysql_query($queryoffertescali,$conn);
		
		if(isset($_SESSION["Privileges"]))
		{
				echo "
				<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99\">
				<h4>Offerte Disponibili Dirette</h4>
					<h4>Viaggi Diretti Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Giorno</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Disponibili</th>
								<th>Acquista</th>
						</tr>";
					while($row=mysql_fetch_array($resultoffertedirette))
					{
						echo "
						<form method=\"GET\" action=\"details.php\" class=\"form\">
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[1]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
								<td>$row[16]</td>
								<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
								<input type=\"hidden\" name=\"voloa\" value=\"diretto\">
								<input type=\"hidden\" name=\"offerte\" value=\"on\">
								<td><input type=\"image\" src=\"images\go.png\" value=\"Dettagli\" height=\"30\" width=\"30\" alt=\"Acquista\"></td>
							</tr></form>";	
					
					}
					echo"</table> 
					<h4>Offerte disponibili viaggi con scali</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Giorno</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Disponibili</th>
								<th>Acquista</th>
							</tr>";
					while($row=mysql_fetch_array($resultoffertescali))
					{
						echo "
						<form method=\"GET\" action=\"details.php\" class=\"form\">
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[1]</td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
								<td>$row[11]</td>
								<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
								<input type=\"hidden\" name=\"voloa\" value=\"scali\">
								<input type=\"hidden\" name=\"offerte\" value=\"on\">
								<td><input type=\"image\" src=\"images\go.png\" value=\"Dettagli\ height=\"30\" width=\"30\" alt=\"Acquista\"></td>
							</tr></form>
						";	
					
					}				
				echo"</table> </div>";
		}
		else
		{
				echo "
				<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99\">
				<h4>Offerte Disponibili Dirette</h4>
					<h4>Viaggi Diretti Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Giorno</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Disponibili</th>
						</tr>";
					while($row=mysql_fetch_array($resultoffertedirette))
					{
						echo "
						<form method=\"GET\" action=\"details.php\" class=\"form\">
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[1]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
								<td>$row[16]</td>
							</tr></form>";	
					
					}
					echo"</table> 
					<h4>Offerte disponibili viaggi con scali</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Giorno</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Disponibili</th>
							</tr>";
					while($row=mysql_fetch_array($resultoffertescali))
					{
						echo "
						<form method=\"GET\" action=\"details.php\" class=\"form\">
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[1]</td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
								<td>$row[11]</td>
							</tr></form>
						";	
					
					}				
				echo"</table> </div>";
			
		}

		
	}
	
	if(isset($_REQUEST['tipo']))
	{
		if($_REQUEST['tipo']=='andata'){
			if(isset($_REQUEST['giornoa']))
			{
				$data=explode('/',$_REQUEST['giornoa']);
				$checkData=checkdate($data[1],$data[2],$data[0]);
				if(!$checkData) header ("Location: /basidati/~msartore/default.php?err=dateerr");
			}
		}	
		else
		{
			if(isset($_REQUEST['giornoa']))
				{
					$data=explode('/',$_REQUEST['giornoa']);
					$checkData=checkdate($data[1],$data[2],$data[0]);
					if(!$checkData) header ("Location: /basidati/~msartore/default.php?err=dateerr");
				}
				
			if(isset($_REQUEST['giornor']))
			{
				$data=explode('/',$_REQUEST['giornor']);
				$checkData=checkdate($data[1],$data[2],$data[0]);
				if(!$checkData) header ("Location: /basidati/~msartore/default.php?err=dateerr");
			}
		}
	}
	
	
	if(isset($_REQUEST['tipo']) && !isset($_REQUEST['checkscali']))
	{
		$query="SELECT * FROM viewViaggiDiretti WHERE giorno='$_REQUEST[giornoa]' AND luogoP='$_REQUEST[da]' AND luogoA='$_REQUEST[a]'  AND postiSeconda>1";		
		$result=mysql_query($query,$conn);
		
		if(isset($_SESSION["Privileges"]))
		{
			
			if($_REQUEST['tipo']=='andata')
			{/*solo andata con privilegi*/
				echo "
					<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99\">
					<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
						<tr>
							<th>Partenza</th>
							<th>Arrivo</th>
							<th>Durata</th>
							<th>Prezzo</th>
							<th>Acquista</th>
							
						</tr>";
				while($row=mysql_fetch_array($result))
				{
				echo "<form method=\"GET\" action=\"details.php\" class=\"form\">
					<tr>
						<td>$row[4] $row[2] $row[6]</td>
						<td>$row[5] $row[3] $row[7]</td>
						<td>$row[8]</td>
						<td>$row[11],00€</td>
						<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
						<input type=\"hidden\" name=\"voloa\" value=\"diretto\">
						<td><input type=\"image\" src=\"images\go.png\" value=\"Dettagli\ height=\"30\" width=\"30\" alt=\"Acquista\"></td>
					</tr>
				</form>";	
				
				}
				echo"</table> </div>";
			}
			else 
			{/*andata ritorno senza scali con privilegi*/
				$query="SELECT * FROM viewViaggiDiretti WHERE giorno='$_REQUEST[giornor]' AND luogoP='$_REQUEST[a]' AND luogoA='$_REQUEST[da]' AND postiSeconda>1";
						
				$result1=mysql_query($query,$conn);
				
				echo "
				<form method=\"GET\" action=\"details.php\" class=\"form\">
				<div id=\"seleziona\" align=\"center\" style=\"background-color:#123456;\">
					<h2 style=\"color:blue;\">Selezionare i viaggi Desiderati e poi confermare per procedere all'acquisto</h2>
					<input type=\"submit\" Value=\"Acquista\">
				</div>
					<div id=\"voliAndata\" align=\"center\" style=\"width:50%; float:left; background-color:#059899\">
					<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
						<tr>
							<th>Partenza</th>
							<th>Arrivo</th>
							<th>Durata</th>
							<th>Prezzo</th>
							<th>Seleziona</th>
							
						</tr>";
				while($row=mysql_fetch_array($result))
				{
				echo "
					<tr>
						<td>$row[4] $row[2] $row[6]</td>
						<td>$row[5] $row[3] $row[7]</td>
						<td>$row[8]</td>
						<td>$row[11],00€</td>					
						<input type=\"hidden\" name=\"voloa\" value=\"diretto\">
						<td><input type=\"radio\" name=\"idva\" value=\"$row[0]\"></td>
					</tr>";	
				
				}
				echo"</table> </div>";		
				echo "
					<div id=\"voliRitorno\" align=\"center\" style=\"width:50%; float:right; background-color:#65AF99\">
					<h4>Voli da: $_REQUEST[a] <br> a:$_REQUEST[da] <br> il giorno $_REQUEST[giornor] </h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
						<tr>
							<th>Partenza</th>
							<th>Arrivo</th>
							<th>Durata</th>
							<th>Prezzo</th>
							<th>Seleziona</th>
							
						</tr>";
				while($row=mysql_fetch_array($result1))
				{
				echo "
					<tr>
						<td>$row[4] $row[2] $row[6]</td>
						<td>$row[5] $row[3] $row[7]</td>
						<td>$row[8]</td>
						<td>$row[11],00€</td>
						<input type=\"hidden\" name=\"volor\" value=\"diretto\">
						<td><input type=\"radio\" name=\"idvr\" value=\"$row[0]\"></td>
					</tr>
				";	
				
				}
				echo"</table> </div></form>";
			}	
			
		}
		else{
		if($_REQUEST['tipo']=='andata')
		{/*entrata sezione senza privilegi*/
			echo "
				<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99\">
				<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
					<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo</th>						
					</tr>";
			while($row=mysql_fetch_array($result))
			{
			echo "<form method=\"GET\" class=\"form\">
				<tr>
					<td>$row[4] $row[2] $row[6]</td>
					<td>$row[5] $row[3] $row[7]</td>
					<td>$row[8]</td>
					<td>$row[11],00€</td>
				</tr>
			</form>";	
			
			}
			echo"</table> </div>";
		}
		else 
		{/*andata ritorno diretti senza scali senza privilegi*/
			$query="SELECT * FROM viewViaggi WHERE giorno='$_REQUEST[giornor]' AND luogoP='$_REQUEST[da]' AND luogoA='$_REQUEST[a]' AND postiSeconda>1";
					
			$result1=mysql_query($query,$conn);
			
			echo "
			<form method=\"GET\" class=\"form\">
				<div id=\"voliAndata\" align=\"center\" style=\"width:50%; float:left; background-color:#059899\">
				<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
					<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo</th>						
					</tr>";
			while($row=mysql_fetch_array($result))
			{
			echo "
				<tr>
					<td>$row[4] $row[2] $row[6]</td>
					<td>$row[5] $row[3] $row[7]</td>
					<td>$row[8]</td>
					<td>$row[11],00€</td>
				</tr>";	
			
			}
			echo"</table> </div>";		
			echo "
				<div id=\"voliRitorno\" align=\"center\" style=\"width:50%; float:right; background-color:#65AF99\">
				<h4>Voli da: $_REQUEST[a] <br> a:$_REQUEST[da] <br> il giorno $_REQUEST[giornor] </h4>
					<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
					<tr>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Durata</th>
						<th>Prezzo</th>	
					</tr>";
			while($row=mysql_fetch_array($result1))
			{
			echo "
				<tr>
					<td>$row[4] $row[2] $row[6]</td>
					<td>$row[5] $row[3] $row[7]</td>
					<td>$row[8]</td>
					<td>$row[11],00€</td>
				</tr>
			";	
			
			}
			echo"</table> </div> 
			</form>";
		}
		}	
		
	}
	else
	if(isset($_REQUEST['tipo']) && isset($_REQUEST['checkscali']))
	{
		$queryd="SELECT * FROM viewViaggiDiretti WHERE giorno='$_REQUEST[giornoa]' AND luogoP='$_REQUEST[da]' AND luogoA='$_REQUEST[a]' AND postiSeconda>1";
		$querys="SELECT * FROM viewViaggiConScali WHERE giorno='$_REQUEST[giornoa]' AND luogoP='$_REQUEST[da]' AND luogoA='$_REQUEST[a]' AND postiSeconda>1";		
		$resultd=mysql_query($queryd,$conn);
		$results=mysql_query($querys,$conn);
		if(isset($_SESSION["Privileges"]))
		{
				
			if($_REQUEST['tipo']=='andata')
			{
				echo "
				<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99\">
				<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
					<h4>Viaggi Diretti Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Acquista</th>
						</tr>";
					while($row=mysql_fetch_array($resultd))
					{
						echo "
						<form method=\"GET\" action=\"details.php\" class=\"form\">
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
								<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
								<input type=\"hidden\" name=\"voloa\" value=\"diretto\">
								<td><input type=\"image\" src=\"images\go.png\" value=\"Dettagli\" height=\"30\" width=\"30\" alt=\"Acquista\"></td>
							</tr></form>";	
					
					}
					echo"</table> 
					<h4>Viaggi Con Scali Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Acquista</th>
							</tr>";
					while($row=mysql_fetch_array($results))
					{
						echo "
						<form method=\"GET\" action=\"details.php\" class=\"form\">
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
								<input type=\"hidden\" name=\"idv\" value=\"$row[0]\">
								<input type=\"hidden\" name=\"voloa\" value=\"scali\">
								<td><input type=\"image\" src=\"images\go.png\" value=\"Dettagli\ height=\"30\" width=\"30\" alt=\"Acquista\"></td>
							</tr></form>
						";	
					
					}				
				echo"</table> </div>";
			}
			else 
			{/*Tipo andata ritorno*/
				$querydr="SELECT * FROM viewViaggiDiretti WHERE giorno='$_REQUEST[giornor]' AND luogoP='$_REQUEST[a]' AND luogoA='$_REQUEST[da]' AND postiSeconda>1";
				$querysr="SELECT * FROM viewViaggiConScali WHERE giorno='$_REQUEST[giornor]' AND luogoP='$_REQUEST[a]' AND luogoA='$_REQUEST[da]' AND postiSeconda>1";		
				$resultdr=mysql_query($querydr,$conn);
				$resultsr=mysql_query($querysr,$conn);
						
				echo "
				<div id=\"seleziona\" align=\"center\" style=\"background-color:#123456;\">
				<form method=\"GET\" action=\"details.php\" class=\"form\">
				<h2 style=\"color:blue;\">Selezionare i viaggi Desiderati e poi confermare per procedere all'acquisto</h2>
					<input type=\"submit\" Value=\"Acquista\">
				</div>
					<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99;width:50%;float:left;\">
				
					<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
						<h4>Viaggi Diretti Andata,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Seleziona</th>
						</tr>";
					while($row=mysql_fetch_array($resultd))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
								<td><input type=\"radio\" name=\"idva\" value=\"$row[0]\"></td>
							</tr>
						";	
					
					}
					echo"</table> 
					<h4>Viaggi Con Scali Andata,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Seleziona</th>
							</tr>";
					while($row=mysql_fetch_array($results))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
								<td><input type=\"radio\" name=\"idva\" value=\"$row[0]\"></td>
							</tr>";						
					}				
				echo"</table></div>";	
				
				/*Viaggi di Ritorno*/
				echo "
				<div id=\"voliRitorno\" align=\"center\" style=\"background-color:#65AF99;width:50%;float:right\">
					<h4>Voli da: $_REQUEST[a] <br> a:$_REQUEST[da] <br> il giorno $_REQUEST[giornor] </h4>
					<h4>Viaggi Diretti Ritorno,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Seleziona</th>
						</tr>";
					while($row=mysql_fetch_array($resultdr))
					{
						echo "
						<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
								<td><input type=\"radio\" name=\"idvr\" value=\"$row[0]\"></td>
							</tr>";	
					
					}
					echo"</table> 
					<h4>Viaggi Con Scali Ritorno,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
								<th>Seleziona</th>
							</tr>";
					while($row=mysql_fetch_array($resultsr))
					{
						echo "
						<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
								<td><input type=\"radio\" name=\"idvr\" value=\"$row[0]\"></td>
							</tr>
						";	
					
					}				
				echo"</table> </form> </div>";
			
		}
		}
		else{
		/*Parte senza privilegi*/	
			if($_REQUEST['tipo']=='andata')
			{
				echo "
				<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99\">
				<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
					<h4>Viaggi Diretti Per informazioni dettagliati selezionarlo per i dettagli</h4>
					
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
						</tr>";
					while($row=mysql_fetch_array($resultd))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
							</tr>";	
					
					}
					echo"</table> 
					<h4>Viaggi Con Scali Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
							</tr>";
					while($row=mysql_fetch_array($results))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
							</tr>
						";	
					
					}				
				echo"</table>";
			}
			else 
			{/*Tipo andata ritorno*/
				$querydr="SELECT * FROM viewViaggiDiretti WHERE giorno='$_REQUEST[giornor]' AND luogoP='$_REQUEST[a]' AND luogoA='$_REQUEST[da]' AND postiSeconda>1";
				$querysr="SELECT * FROM viewViaggiConScali WHERE giorno='$_REQUEST[giornor]' AND luogoP='$_REQUEST[a]' AND luogoA='$_REQUEST[da]' AND postiSeconda>1";		
				$resultdr=mysql_query($querydr,$conn);
				$resultsr=mysql_query($querysr,$conn);
						
				echo "
					<div id=\"voliAndata\" align=\"center\" style=\"background-color:#65AF99;width:50%;float:left;\">
					<h4>Voli da: $_REQUEST[da] <br> a:$_REQUEST[a] <br> il giorno $_REQUEST[giornoa] </h4>
						<h4>Viaggi Diretti Andata,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
						</tr>";
					while($row=mysql_fetch_array($resultd))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
							</tr>
						";	
					
					}
					echo"</table> 
					<h4>Viaggi Con Scali Andata,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
							</tr>";
					while($row=mysql_fetch_array($results))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
							</tr>";						
					}				
				echo"</table></div>";	
				
				/*Viaggi di Ritorno*/
				echo "
				<div id=\"voliRitorno\" align=\"center\" style=\"background-color:#65AF99;width:50%;float:right\">
					<h4>Voli da: $_REQUEST[a] <br> a:$_REQUEST[da] <br> il giorno $_REQUEST[giornor] </h4>
					<h4>Viaggi Diretti Ritorno,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Durata</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
						</tr>";
					while($row=mysql_fetch_array($resultdr))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] $row[6]</td>
								<td>$row[5] $row[3] $row[7]</td>
								<td>$row[8]</td>
								<td>$row[11],00€</td>
								<td>Diretto</td>
							</tr>";	
					
					}
					echo"</table> 
					<h4>Viaggi Con Scali Ritorno,Per informazioni dettagliati selezionarlo per i dettagli</h4>
						<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
							<tr>
								<th>Partenza</th>
								<th>Arrivo</th>
								<th>Prezzo</th>
								<th>Tipo Volo</th>
							</tr>";
					while($row=mysql_fetch_array($resultsr))
					{
						echo "
							<tr>
								<td>$row[4] $row[2] </td>
								<td>$row[5] $row[3] </td>
								<td>$row[8],00€</td>
								<td>Con Scali</td>
							</tr>
						";	
					}				
				echo"</table></div>";
			
			}	
		}
	}
	else
	{
		header("Location: /basidati/~msartore/default.php");
	}
?>
</body>
</html>