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


<div id="personale" align="center" style="background-color:#FF4030;">
<?php
	if(isset($_REQUEST["cmd"]))
		if($_REQUEST["cmd"]=="logout")
			{
				$_SESSION=array();
				session_destroy();
				header ("Location:/basidati/~msartore/default.php");
			}
	if(isset($_SESSION["Privileges"])){
		echo "Benvenuto ".$_SESSION["email"] .", <a href=\"details.php?cmd=logout\" >Logout</a>";
		echo "<p>Torna Alla <a href=\"default.php\" >Pagina Iniziale</a></p>";
		echo "<p>Vedi le <a href=\"research.php?cmd=offerte\" >Offerte</a></p>";
	}
	else{
		header ("Location:/basidati/~msartore/default.php");	
	}
?>
</div>

<div id="informazioni" align="center" style="background-color:#FF4030;">

<div id="dettaglipersona" align="center" style="background-color:#FF4030;">
	<?php 
	
		require "component/db_connection.php";
		$querydettagli="SELECT * FROM Anagrafiche WHERE email='$_SESSION[email]'";
		$resultdettagli=mysql_fetch_array(mysql_query($querydettagli,$conn));
		echo"
		<h2>Riepilogo dei tuoi dati</h2>
			<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Nascita</th>
					<th>Sesso</th>
					<th>Email</th>	
				</tr>
				<tr>
					<td>$resultdettagli[1]</td>
					<td>$resultdettagli[2]</td>
					<td>$resultdettagli[3]</td>
					<td>$resultdettagli[4]</td>
					<td>$resultdettagli[5]</td>
					
				</tr>	
			</table>
		";
		
		
	?>
</div>

<div id="prenotazioni valide" align="center" style="background-color:#FF4030;">
	<?php
	echo"<br><br><br>";
		$queryprenotazionivalidedirette="SELECT p.idPrenotazione,p.numeroBagagli,p.type,p.prezzoPrenotazione,p.posto
													,vvd.giorno,vvd.da,vvd.a,vvd.luogoP,vvd.luogoA,vvd.oraP,vvd.oraA,vvd.durata,
													a.*
													FROM Prenotazioni p JOIN viewViaggiDiretti vvd ON (p.idViaggio=vvd.idViaggio) 
													JOIN Anagrafiche a ON (p.passeggero=a.idAnag)
											WHERE p.acquirente=(SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]') AND p.stato='valido' 
													AND p.idViaggioConScali IS NULL
											ORDER BY p.idPrenotazione ASC";
		$resultprenotazionivalidedirette=mysql_query($queryprenotazionivalidedirette,$conn);
		
		echo"
		<h2>Viaggi Diretti Acquistati Da Effettuare</h2>
			<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>	
					<th>Numero Prenotazione</th>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Nascita</th>
					<th>Sesso</th>
					<th>Email</th>
					<th>Tipo</th>
					<th>Numero Bagagli</th>
					<th>Classe</th>
					<th>Prezzo</th>
					<th>Posto</th>
					<th>Giorno</th>
					<th>Luogo Partenza</th>
					<th>Aeroporto Partenza</th>
					<th>Ora Partenza</th>
					<th>Luogo Arrivo</th>
					<th>Aeroporto Arrivo</th>
					<th>Ora Arrivo</th>
					<th>Durata</th>
					
				</tr>
		";
		
		while($row=mysql_fetch_array($resultprenotazionivalidedirette))
		{
			echo"
			<tr>	
					<td>$row[0]</td>
					<td>$row[14]</td>
					<td>$row[15]</td>
					<td>$row[16]</td>
					<td>$row[17]</td>
					<td>$row[18]</td>
					<td>$row[19]</td>
					<td>$row[1]</td>
					<td>$row[2]</td>
					<td>$row[3]</td>";
					if($row["4"])
						echo"<td>$row[4]</td>";
					else
						echo"<td>No</td>";
						
					echo"
					<td>$row[5]</td>
					<td>$row[8]</td>
					<td>$row[6]</td>
					<td>$row[10]</td>
					<td>$row[9]</td>
					<td>$row[7]</td>
					<td>$row[11]</td>
					<td>$row[12]</td>
				</tr>
			";
		}
		echo"</table>";
		
		$queryprenotazionivalidescali="SELECT p.idPrenotazione,p.numeroBagagli,p.type,p.prezzoPrenotazione,p.posto
													,vvs.giorno,vvs.da,vvs.a,vvs.luogoP,vvs.luogoA,
													a.*,p.idViaggioConScali
													FROM Prenotazioni p JOIN viewViaggiConScali vvs ON (p.idViaggioConScali=vvs.idViaggio) 
													JOIN Anagrafiche a ON (p.passeggero=a.idAnag)
											WHERE p.acquirente=(SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]') AND p.stato='valido'
											GROUP BY p.passeggero
                                            ORDER BY p.idPrenotazione ASC";
		$resultprenotazionivalidescali=mysql_query($queryprenotazionivalidescali,$conn);
		echo"<br><br><br>";
		echo"
		<h2>Viaggi Con Scali Acquistati Da Effettuare, Per Dettagli Sull'orario Clicca Sul Link</h2>
			<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Nascita</th>
					<th>Sesso</th>
					<th>Email</th>
					<th>Tipo</th>
					<th>Numero Bagagli</th>
					<th>Classe</th>
					<th>Prezzo</th>
					<th>Posto</th>
					<th>Giorno</th>
					<th>Luogo Partenza</th>
					<th>Aeroporto Partenza</th>
					<th>Luogo Arrivo</th>
					<th>Aeroporto Arrivo</th>
					<th>Dettagli Orari</th>
				</tr>
		";
		
		while($row=mysql_fetch_array($resultprenotazionivalidescali))
		{
			echo"
			<tr>	
					<td>$row[11]</td>
					<td>$row[12]</td>
					<td>$row[13]</td>
					<td>$row[14]</td>
					<td>$row[15]</td>
					<td>$row[16]</td>
					<td>$row[1]</td>
					<td>$row[2]</td>
					<td>$row[3]</td>";
					if($row["4"])
						echo"<td>SI</td>";
					else
						echo"<td>No</td>";
						
					echo"
					<td>$row[5]</td>
					<td>$row[8]</td>
					<td>$row[6]</td>
					<td>$row[9]</td>
					<td>$row[7]</td>
					<td><a href=\"appo.php?idv=$row[17]&passeggero=$row[10]\" target=\"_new\">Clicca Qui</a></td>
				</tr>
			";
		}
		echo"</table>";
		
	?>
</div>

<div id="prenotazionidarimborsare" align="center" style="background-color:#FF4030;">
	<?php		
		echo"<br><br><br>";	
		$queryprenotazionirimbrosdirette="SELECT p.idPrenotazione,p.numeroBagagli,p.type,p.prezzoPrenotazione,p.posto
													,vvd.giorno,vvd.da,vvd.a,vvd.luogoP,vvd.luogoA,vvd.oraP,vvd.oraA,vvd.durata,
													a.*,p.idViaggio,p.acquirente
													FROM Prenotazioni p JOIN viewViaggiDiretti vvd ON (p.idViaggio=vvd.idViaggio) 
													JOIN Anagrafiche a ON (p.passeggero=a.idAnag)
											WHERE p.acquirente=(SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]') AND p.stato='annullato' 
													AND p.idViaggioConScali IS NULL
											ORDER BY p.idPrenotazione ASC";
		$resultprenotazionirimborsodirette=mysql_query($queryprenotazionirimbrosdirette,$conn);
		
		echo"
		<h2>Viaggi Diretti Acquistati Da Rimborsare</h2>
			<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>	
					<th>Numero Prenotazione</th>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Nascita</th>
					<th>Sesso</th>
					<th>Email</th>
					<th>Tipo</th>
					<th>Numero Bagagli</th>
					<th>Classe</th>
					<th>Prezzo</th>
					<th>Posto</th>
					<th>Giorno</th>
					<th>Luogo Partenza</th>
					<th>Aeroporto Partenza</th>
					<th>Ora Partenza</th>
					<th>Luogo Arrivo</th>
					<th>Aeroporto Arrivo</th>
					<th>Ora Arrivo</th>
					<th>Durata</th>
					<th>Rimborso</th>
					
				</tr>
		";
		
		while($row=mysql_fetch_array($resultprenotazionirimborsodirette))
		{
			echo"
			<tr>	
					<td>$row[0]</td>
					<td>$row[14]</td>
					<td>$row[15]</td>
					<td>$row[16]</td>
					<td>$row[17]</td>
					<td>$row[18]</td>
					<td>$row[19]</td>
					<td>$row[1]</td>
					<td>$row[2]</td>
					<td>$row[3]</td>";
					if($row["4"])
						echo"<td>$row[4]</td>";
					else
						echo"<td>No</td>";
						
					echo"
					<td>$row[5]</td>
					<td>$row[8]</td>
					<td>$row[6]</td>
					<td>$row[10]</td>
					<td>$row[9]</td>
					<td>$row[7]</td>
					<td>$row[11]</td>
					<td>$row[12]</td>
					<td><a href=\"appo.php?idp=$row[0]&idacquirente=$row[21]&rimborso=on&idpass=$row[13]&luogopartenza=$row[8]&luogoarrivo=$row[9]\">Clicca Qui</a></td>
				</tr>
			";
		}
		echo"</table>";
		
		$queryprenotazionirimbrososcali="SELECT p.idPrenotazione,p.numeroBagagli,p.type,p.prezzoPrenotazione,p.posto
													,vvs.giorno,vvs.da,vvs.a,vvs.luogoP,vvs.luogoA,
													a.*,p.idViaggioConScali,p.idViaggio,p.acquirente
													FROM Prenotazioni p JOIN viewViaggiConScali vvs ON (p.idViaggioConScali=vvs.idViaggio) 
													JOIN Anagrafiche a ON (p.passeggero=a.idAnag)
											WHERE p.acquirente=(SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]') AND p.stato='annullato'
											GROUP BY p.passeggero
                                            ORDER BY p.idPrenotazione ASC";
		$resultprenotazionirimborsoscali=mysql_query($queryprenotazionirimbrososcali,$conn);
		echo"<br><br><br>";
		echo"
		<h2>Viaggi Con Scali Acquistati Da Rimborsare</h2>
			<table align=\"center\" border=\"2px\" bordercolor=\"#99AF99\" style=\"margin:0px\">
				<tr>	
					<th>Nome</th>
					<th>Cognome</th>
					<th>Nascita</th>
					<th>Sesso</th>
					<th>Email</th>
					<th>Tipo</th>
					<th>Numero Bagagli</th>
					<th>Classe</th>
					<th>Prezzo</th>
					<th>Posto</th>
					<th>Giorno</th>
					<th>Luogo Partenza</th>
					<th>Aeroporto Partenza</th>
					<th>Luogo Arrivo</th>
					<th>Aeroporto Arrivo</th>
					<th>Ottieni Rimborso</th>
				</tr>
		";
		
		while($row=mysql_fetch_array($resultprenotazionirimborsoscali))
		{
			echo"
			<tr>
					<td>$row[11]</td>
					<td>$row[12]</td>
					<td>$row[13]</td>
					<td>$row[14]</td>
					<td>$row[15]</td>
					<td>$row[16]</td>
					<td>$row[1]</td>
					<td>$row[2]</td>
					<td>$row[3]</td>";
					if($row["4"])
						echo"<td>SI</td>";
					else
						echo"<td>No</td>";
						
					echo"
					<td>$row[5]</td>
					<td>$row[8]</td>
					<td>$row[6]</td>
					<td>$row[9]</td>
					<td>$row[7]</td>
					<td><a href=\"appo.php?prezzo=$row[3]&idvs=$row[17]&giorno=$row[5]&rimborso=on&idacquirente=$row[19]&idpass=$row[10]&luogopartenza=$row[8]&luogoarrivo=$row[9]\">Clicca Qui</a></td>
				</tr>
			";
		}
		echo"</table>";
	
	?>
</div>
</div>

</body>
</html>