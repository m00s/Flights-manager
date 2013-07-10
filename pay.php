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
				header ("Location:default.php");
			}
	if(isset($_SESSION["Privileges"])){
		echo "Benvenuto ".$_SESSION["email"] .", <a href=\"details.php?cmd=logout\" >Logout</a>";
	}
	else{
		header ("Location:default.php");	
	}
	
?>
</div>

<div id="pagamento" align="center" style="background-color:#954030;">
	<?php
		if(isset($_REQUEST["idv"]))
		{/*viaggio diretto solo andata*/
			
			require "component/db_connection.php";
			$queryca="SELECT idViaggioDiretto FROM viaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idv]";
			$resultca=mysql_fetch_array(mysql_query($queryca,$conn));
			$voloa=$resultca["0"];
			$totaledapagare=0;
			$resultriduzioneofferta;
			if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 )
			{				
				for($i=1;$i<$_REQUEST["bigliettiPrima"]+1;$i++)
				{
					/*controllo se il passeggero esiste o meno*/
					$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['ppaemail'.$i]."'";
					$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));
					
					if(!$idpass)
					{/*passeggero non esiste*/
						$queryinspas="INSERT INTO Anagrafiche (nome,cognome,nascita,sesso,email,tipo)
													VALUES('".$_REQUEST['ppanome'.$i]."','".$_REQUEST['ppacognome'.$i]."','".$_REQUEST['ppanascita'.$i].
													"',\"".$_REQUEST['ppasesso'.$i]."\",'".$_REQUEST['ppaemail'.$i]."','".$_REQUEST['ppatipo'.$i]."')";
						$resultinspas=mysql_query($queryinspas,$conn);
						$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['ppaemail'.$i]."'";
						$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));						
					}
					
					
					/*calcolare il prezzo della prenotazione che è data dai bagagli e dal prezzo del viaggio di prima classe*/
					$prezzototale=0;
					if($voloa)
					{	/*prima diretto*/
						$bagaglio=explode(',',$_REQUEST['ppapesobagagli'.$i]);
						$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=$bagaglio[0]";
						$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
						$queryprezzoprima="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$_REQUEST[idv]";
						$resultprezzoprima=mysql_fetch_array(mysql_query($queryprezzoprima,$conn));
						$nbagagli=$_REQUEST["ppabagagli".$i];
						$prezzototale=$nbagagli*$bagaglio["1"]+$resultprezzoprima["0"];
						if($_REQUEST['ppatipo'.$i]=='bambino')
							$prezzototale=$prezzototale-($prezzototale*($resultprezzoprima["1"]/100));
						
						if(isset($_REQUEST["offerte"]))
							{
								$queryriduzioneofferta="SELECT scontoperc FROM Offerte WHERE idViaggio=$_REQUEST[idv]";
								$resultriduzioneofferta=mysql_fetch_array(mysql_query($queryriduzioneofferta,$conn));
								$prezzototale=$prezzototale-($prezzototale*($resultriduzioneofferta["0"]/100));
								$queryaggiornaofferte="UPDATE Offerte SET disponibili=disponibili-1 WHERE idViaggio=$_REQUEST[idv]";
								mysql_query($queryaggiornaofferte,$conn);
							}
						/*trovare un posto libero di prima classe per assegnarglielo*/
						$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
											WHERE vd.idViaggioDiretto=$_REQUEST[idv] AND pps.numero NOT IN 
												(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$_REQUEST[idv] AND p.type='prima')LIMIT 0,1";
						$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
						$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
						$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
						$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																				type,prezzoPrenotazione,posto) 
														VALUES ($_REQUEST[idv],NULL,$resultidacquirente[0],$idpass[0],$nbagagli+1,$resultidbagaglio[0],'prima',
														$prezzototale,'$resultpostoprima[0]')";
						mysql_query($queryinsertprenotazione,$conn);
						$totaledapagare=$totaledapagare+$prezzototale;
					}
					else
					{
						/*prima con scalo*/
						$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
						$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
						$nbagagli=$_REQUEST["ppabagagli".$i];
						$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
													(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idv]";
						$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
						
						if(isset($_REQUEST["offerte"]))
							{
								$queryriduzioneofferta="SELECT scontoperc FROM Offerte WHERE idViaggio=$_REQUEST[idv]";
								$resultriduzioneofferta=mysql_fetch_array(mysql_query($queryriduzioneofferta,$conn));
								$queryaggiornaofferte="UPDATE Offerte SET disponibili=disponibili-1 WHERE idViaggio=$_REQUEST[idv]";
								mysql_query($queryaggiornaofferte,$conn);
							}
						
						while($rowcvs=mysql_fetch_array($resultcvs))
						{	
							$queryprezzobagaglio="SELECT prezzo FROM TariffeBagagli WHERE idBagaglio=$resultidbagaglio[0] AND idCompagnia=$rowcvs[0]";
							$prezzoperBagaglio=mysql_fetch_array(mysql_query($queryprezzobagaglio,$conn));
							$queryprezzoprima="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$rowcvs[1]";
							$resultprezzoprima=mysql_fetch_array(mysql_query($queryprezzoprima,$conn));
							$prezzototale+=$nbagagli*$prezzoperBagaglio["0"]+$resultprezzoprima["0"];
							if($_REQUEST['ppatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-($prezzototale*($resultprezzoprima["1"]/100));
							
							if(isset($_REQUEST["offerte"]))
								$prezzototale=$prezzototale-($prezzototale*($resultriduzioneofferta["0"]/100));
								
							/*trovare un posto libero di prima classe per assegnarglielo*/
							$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
											WHERE vd.idViaggioDiretto=$_REQUEST[idv] AND pps.numero NOT IN 
												(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$_REQUEST[idv] AND p.type='prima')LIMIT 0,1";
							$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
							$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
							$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
							$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																				type,prezzoPrenotazione,posto) 
														VALUES ($rowcvs[1],FALSE,$_REQUEST[idv],$resultidacquirente[0],$idpass[0],$nbagagli+1,$resultidbagaglio[0],'prima',
														$prezzototale,'$resultpostoprima[0]')";
							mysql_query($queryinsertprenotazione,$conn);
							$totaledapagare=$totaledapagare+$prezzototale;
						}
					}
					
				}
				
			}
			
			if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
			{
				for($i=1;$i<$_REQUEST["bigliettiSeconda"]+1;$i++)
				{
					/*controllo se il passeggero esiste o meno*/
					$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['psaemail'.$i]."'";
					$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));
					
					if(!$idpass)
					{/*passeggero non esiste*/
						$queryinspas="INSERT INTO Anagrafiche (nome,cognome,nascita,sesso,email,tipo)
													VALUES('".$_REQUEST['psanome'.$i]."','".$_REQUEST['psacognome'.$i]."','".$_REQUEST['psanascita'.$i].
													"',\"".$_REQUEST['psasesso'.$i]."\",'".$_REQUEST['psaemail'.$i]."','".$_REQUEST['psatipo'.$i]."')";
						$resultinspas=mysql_query($queryinspas,$conn);
						$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['psaemail'.$i]."'";
						$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));						
					}
									
					
					/*calcolare il prezzo della prenotazione che è data dai bagagli e dal prezzo del viaggio di prima classe*/
					$prezzototale=0;
					if($voloa)
					{
						/*seconda diretto*/
						$bagaglio=explode(',',$_REQUEST['psapesobagagli'.$i]);
						$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=$bagaglio[0]";
						$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
						$queryprezzoSeconda="SELECT v.prezzoSeconda,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$_REQUEST[idv]";
						$resultprezzoSeconda=mysql_fetch_array(mysql_query($queryprezzoSeconda,$conn));
						$nbagagli=$_REQUEST["psabagagli".$i];
						$prezzototale=$nbagagli*$bagaglio["1"]+$resultprezzoSeconda["0"];
						if($_REQUEST['psatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-$prezzototale*($resultprezzoseconda["1"]/100);
													
						if(isset($_REQUEST["offerte"]))
							{
								$queryriduzioneofferta="SELECT scontoperc FROM Offerte WHERE idViaggio=$_REQUEST[idv]";
								$resultriduzioneofferta=mysql_fetch_array(mysql_query($queryriduzioneofferta,$conn));
								$prezzototale=$prezzototale-$prezzototale*($resultriduzioneofferta["0"]/100);
								$queryaggiornaofferte="UPDATE Offerte SET disponibili=disponibili-1 WHERE idViaggio=$_REQUEST[idv]";
								mysql_query($queryaggiornaofferte,$conn);
							}
						
						$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
						$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
						$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																			type,prezzoPrenotazione) 
													VALUES ($_REQUEST[idv],NULL,$resultidacquirente[0],$idpass[0],$nbagagli,$resultidbagaglio[0],'seconda',
													$prezzototale)";
						echo $queryinsertprenotazione;
						mysql_query($queryinsertprenotazione,$conn);
						$totaledapagare=$totaledapagare+$prezzototale;
					}
					else
					{	
						/*seconda Con scalo*/
						$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
						$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
						$nbagagli=$_REQUEST["psabagagli".$i];
						$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
													(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idv]";
						$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
						
						if(isset($_REQUEST["offerte"]))
							{
								$queryriduzioneofferta="SELECT scontoperc FROM Offerte WHERE idViaggio=$_REQUEST[idv]";
								$resultriduzioneofferta=mysql_fetch_array(mysql_query($queryriduzioneofferta,$conn));
								$queryaggiornaofferte="UPDATE Offerte SET disponibili=disponibili-1 WHERE idViaggio=$_REQUEST[idv]";
								mysql_query($queryaggiornaofferte,$conn);
							}
						
						while($rowcvs=mysql_fetch_array($resultcvs))
						{	
							$queryprezzobagaglio="SELECT prezzo FROM TariffeBagagli WHERE idBagaglio=$resultidbagaglio[0] AND idCompagnia=$rowcvs[0]";
							$prezzoperBagaglio=mysql_fetch_array(mysql_query($queryprezzobagaglio,$conn));
							$queryprezzoSeconda="SELECT v.prezzoSeconda,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$rowcvs[1]";
							$resultprezzoSeconda=mysql_fetch_array(mysql_query($queryprezzoSeconda,$conn));
							$prezzototalebagagli=$nbagagli*$prezzoperBagaglio["0"]+$resultprezzoSeconda["0"];
							$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
							$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
							$prezzototale=$nbagagli*$prezzoperBagaglio["0"]+$resultprezzoSeconda["0"];
							if($_REQUEST['psatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-($prezzototale*($resultprezzoseconda["1"]/100));
							if(isset($_REQUEST["offerte"]))
							$prezzototale=$prezzototale-($prezzototale*($resultriduzioneofferta["0"]/100));
							
							$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																				type,prezzoPrenotazione) 
														VALUES ($rowcvs[1],FALSE,$_REQUEST[idv],$resultidacquirente[0],$idpass[0],$nbagagli,$resultidbagaglio[0],'seconda',
														$prezzototale)";
							mysql_query($queryinsertprenotazione,$conn);
							$totaledapagare=$totaledapagare+$prezzototale;
						}
					}
				}
			}
			
			echo"
			<h2>Riepologo Totale E Pagamento</h2>
			<h4>Totale Prezzo da Pagare:$totaledapagare</h4>
			<form method=\"GET\" action=\"personale.php\" class=\"form\>
				<label for=\"CC\">Numero Carta Di Credito<input type=\"text\" name=\"cc\"></label>
				<input type=\"hidden\" name=\"pagamento\" value=\"ok\">
				<input type=\"submit\" value=\"Paga\">			
			</form>
			";
			
			
		}
		
		if(isset($_REQUEST["idva"]) && isset($_REQUEST["idvr"]))
		{
			require "component/db_connection.php";
			$queryca="SELECT idViaggioDiretto FROM viaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idva]";
			$resultca=mysql_fetch_array(mysql_query($queryca,$conn));
			$voloa=$resultca["0"];
			$querycr="SELECT idViaggioDiretto FROM viaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idvr]";
			$resultcr=mysql_fetch_array(mysql_query($queryca,$conn));
			$volor=$resultcr["0"];
			$totaledapagare=0;
			
			if($_REQUEST["idva"]!=0)
			{
				if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 )
				{	
					for($i=1;$i<$_REQUEST["bigliettiPrima"]+1;$i++)
					{
						/*controllo se il passeggero esiste o meno*/
						$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['ppaemail'.$i]."'";
						$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));
						
						if(!$idpass)
						{/*passeggero non esiste*/
							$queryinspas="INSERT INTO Anagrafiche (nome,cognome,nascita,sesso,email,tipo)
														VALUES('".$_REQUEST['ppanome'.$i]."','".$_REQUEST['ppacognome'.$i]."','".$_REQUEST['ppanascita'.$i].
														"',\"".$_REQUEST['ppasesso'.$i]."\",'".$_REQUEST['ppaemail'.$i]."','".$_REQUEST['ppatipo'.$i]."')";
							$resultinspas=mysql_query($queryinspas,$conn);
							$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['ppaemail'.$i]."'";
							$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));						
						}
						
						
						/*calcolare il prezzo della prenotazione che è data dai bagagli e dal prezzo del viaggio di prima classe*/
						$prezzototale=0;
						if($voloa)
						{	/*prima diretto*/
							$bagaglio=explode(',',$_REQUEST['ppapesobagagli'.$i]);
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=$bagaglio[0]";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$queryprezzoprima="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$_REQUEST[idva]";
							$resultprezzoprima=mysql_fetch_array(mysql_query($queryprezzoprima,$conn));
							$nbagagli=$_REQUEST["ppabagagli".$i];
							$prezzototale=$nbagagli*$bagaglio["1"]+$resultprezzoprima["0"];
							if($_REQUEST['ppatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-$prezzototale*($resultprezzoprima["1"]/100);
							/*trovare un posto libero di prima classe per assegnarglielo*/
							$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
											WHERE vd.idViaggioDiretto=$_REQUEST[idv] AND pps.numero NOT IN 
												(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$_REQUEST[idv] AND p.type='prima')LIMIT 0,1";
							$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
							$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
							$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
							$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																					type,prezzoPrenotazione,posto) 
															VALUES ($_REQUEST[idv],NULL,$resultidacquirente[0],$idpass[0],$nbagagli+1,$resultidbagaglio[0],'prima',
															$prezzototale,'$resultpostoprima[0]')";
							mysql_query($queryinsertprenotazione,$conn);
							$totaledapagare=$totaledapagare+$prezzototale;
						}
						else
						{
							/*prima con scalo*/
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$nbagagli=$_REQUEST["ppabagagli".$i];
							$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
														(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idva]";
							$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
							while($rowcvs=mysql_fetch_array($resultcvs))
							{	
								$queryprezzobagaglio="SELECT prezzo FROM TariffeBagagli WHERE idBagaglio=$resultidbagaglio[0] AND idCompagnia=$rowcvs[0]";
								$prezzoperBagaglio=mysql_fetch_array(mysql_query($queryprezzobagaglio,$conn));
								$queryprezzoprima="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$rowcvs[1]";
								$resultprezzoprima=mysql_fetch_array(mysql_query($queryprezzoprima,$conn));
								$prezzototale=$nbagagli*$queryprezzobagaglio["0"]+$resultprezzoprima["0"];
								if($_REQUEST['ppatipo'.$i]=='bambino')
									$prezzototale=$prezzototale-$prezzototale*($resultprezzoprima["1"]/100);
								/*trovare un posto libero di prima classe per assegnarglielo*/
								$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
											WHERE vd.idViaggioDiretto=$_REQUEST[idv] AND pps.numero NOT IN 
												(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$_REQUEST[idv] AND p.type='prima')LIMIT 0,1";
								$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
								$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
								$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
								$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																					type,prezzoPrenotazione,posto) 
															VALUES ($rowcvs[1],FALSE,$_REQUEST[idva],$resultidacquirente[0],$idpass[0],$nbagagli+1,$resultidbagaglio[0],'prima',
															$prezzototale,'$resultpostoprima[0]')";
								mysql_query($queryinsertprenotazione,$conn);
								$totaledapagare=$totaledapagare+$prezzototale;								
							}
						}
					}
					
				}
				
				if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
				{
					for($i=1;$i<$_REQUEST["bigliettiSeconda"]+1;$i++)
					{
						/*controllo se il passeggero esiste o meno*/
						$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['psaemail'.$i]."'";
						$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));
						
						if(!$idpass)
						{/*passeggero non esiste*/
							$queryinspas="INSERT INTO Anagrafiche (nome,cognome,nascita,sesso,email,tipo)
														VALUES('".$_REQUEST['psanome'.$i]."','".$_REQUEST['psacognome'.$i]."','".$_REQUEST['psanascita'.$i].
														"',\"".$_REQUEST['psasesso'.$i]."\",'".$_REQUEST['psaemail'.$i]."','".$_REQUEST['psatipo'.$i]."')";
							$resultinspas=mysql_query($queryinspas,$conn);
							$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['psaemail'.$i]."'";
							$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));	
						}
										
						
						/*calcolare il prezzo della prenotazione che è data dai bagagli e dal prezzo del viaggio di prima classe*/
						$prezzototale=0;
						if($voloa)
						{
							/*seconda diretto*/
							$bagaglio=explode(',',$_REQUEST['psapesobagagli'.$i]);
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=$bagaglio[0]";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$queryprezzoSeconda="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$_REQUEST[idva]";
							$resultprezzoSeconda=mysql_fetch_array(mysql_query($queryprezzoSeconda,$conn));
							$nbagagli=$_REQUEST["psabagagli".$i];
							$prezzototale=$nbagagli*$bagaglio["1"]+$resultprezzoSeconda["0"];
							if($_REQUEST['psatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-$prezzototale*($resultprezzoSeconda["1"]/100);
							$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
							$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
							$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																				type,prezzoPrenotazione) 
														VALUES ($_REQUEST[idva],NULL,$resultidacquirente[0],$idpass[0],$nbagagli,$resultidbagaglio[0],'seconda',
														$prezzototale)";
							mysql_query($queryinsertprenotazione,$conn);
							$totaledapagare=$totaledapagare+$prezzototale;
						}
						else
						{	
							/*seconda Con scalo*/
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$nbagagli=$_REQUEST["psabagagli".$i];
							$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
														(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idva]";
							$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
							while($rowcvs=mysql_fetch_array($resultcvs))
							{	
								$queryprezzobagaglio="SELECT prezzo FROM TariffeBagagli WHERE idBagaglio=$resultidbagaglio[0] AND idCompagnia=$rowcvs[0]";
								$prezzoperBagaglio=mysql_fetch_array(mysql_query($queryprezzobagaglio,$conn));
								$queryprezzoSeconda="SELECT v.prezzoSeconda,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$rowcvs[1]";
								$resultprezzoSeconda=mysql_fetch_array(mysql_query($queryprezzoSeconda,$conn));
								$prezzototale=$nbagagli*$queryprezzobagaglio["0"]+$resultprezzoSeconda["0"];
								if($_REQUEST['psatipo'.$i]=='bambino')
									$prezzototale=$prezzototale-$prezzototale*($resultprezzoSeconda["1"]/100);
								$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
								$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
								$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																					type,prezzoPrenotazione) 
															VALUES ($rowcvs[1],FALSE,$_REQUEST[idva],$resultidacquirente[0],$idpass[0],$nbagagli,$resultidbagaglio[0],'seconda',
															$prezzototale)";
								mysql_query($queryinsertprenotazione,$conn);
								$totaledapagare=$totaledapagare+$prezzototale;
							}
						}
					}
				}
				
			}
			
			if($_REQUEST["idvr"]!=0)
			{
				if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 )
				{	
					for($i=1;$i<$_REQUEST["bigliettiPrima"]+1;$i++)
					{
						/*controllo se il passeggero esiste o meno*/
						$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['ppaemail'.$i]."'";
						$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));
						
						if(!$idpass)
						{/*passeggero non esiste*/
							$queryinspas="INSERT INTO Anagrafiche (nome,cognome,nascita,sesso,email,tipo)
														VALUES('".$_REQUEST['ppanome'.$i]."','".$_REQUEST['ppacognome'.$i]."','".$_REQUEST['ppanascita'.$i].
														"',\"".$_REQUEST['ppasesso'.$i]."\",'".$_REQUEST['ppaemail'.$i]."','".$_REQUEST['ppatipo'.$i]."')";
							$resultinspas=mysql_query($queryinspas,$conn);
							$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['ppaemail'.$i]."'";
							$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));						
						}
						
						
						/*calcolare il prezzo della prenotazione che è data dai bagagli e dal prezzo del viaggio di prima classe*/
						$prezzototale=0;
						if($voloa)
						{	/*prima diretto*/
							$bagaglio=explode(',',$_REQUEST['ppapesobagagli'.$i]);
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=$bagaglio[0]";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$queryprezzoprima="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$_REQUEST[idvr]";
							$resultprezzoprima=mysql_fetch_array(mysql_query($queryprezzoprima,$conn));
							$nbagagli=$_REQUEST["ppabagagli".$i];
							$prezzototale=$nbagagli*$bagaglio["1"]+$resultprezzoprima["0"];
							if($_REQUEST['ppatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-$prezzototale*($resultprezzoprima["1"]/100);
							/*trovare un posto libero di prima classe per assegnarglielo*/
							$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
											WHERE vd.idViaggioDiretto=$_REQUEST[idv] AND pps.numero NOT IN 
												(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$_REQUEST[idv] AND p.type='prima')LIMIT 0,1";
							$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
							$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
							$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
							$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																					type,prezzoPrenotazione,posto) 
															VALUES ($_REQUEST[idvr],NULL,$resultidacquirente[0],$idpass[0],$nbagagli+1,$resultidbagaglio[0],'prima',
															$prezzototale,'$resultpostoprima[0]')";
							mysql_query($queryinsertprenotazione,$conn);
							$totaledapagare=$totaledapagare+$prezzototale;
						}
						else
						{
							/*prima con scalo*/
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$nbagagli=$_REQUEST["ppabagagli".$i];
							$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
														(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idvr]";
							$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
							while($rowcvs=mysql_fetch_array($resultcvs))
							{	
								$queryprezzobagaglio="SELECT prezzo FROM TariffeBagagli WHERE idBagaglio=$resultidbagaglio[0] AND idCompagnia=$rowcvs[0]";
								$prezzoperBagaglio=mysql_fetch_array(mysql_query($queryprezzobagaglio,$conn));
								$queryprezzoprima="SELECT v.prezzoPrima,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$rowcvs[1]";
								$resultprezzoprima=mysql_fetch_array(mysql_query($queryprezzoprima,$conn));
								$prezzototale=$nbagagli*$queryprezzobagaglio["0"]+$resultprezzoprima["0"];
								if($_REQUEST['ppatipo'.$i]=='bambino')
									$prezzototale=$prezzototale-$prezzototale*($resultprezzoprima["1"]/100);
								/*trovare un posto libero di prima classe per assegnarglielo*/
								$querypostoprima="SELECT pps.numero,pps.aereo FROM postiPrimaClasse pps JOIN ViaggiDiretti vd ON (pps.aereo=vd.aereo)
											WHERE vd.idViaggioDiretto=$_REQUEST[idv] AND pps.numero NOT IN 
												(SELECT p.posto FROM Prenotazioni p WHERE p.idViaggio=$_REQUEST[idv] AND p.type='prima')LIMIT 0,1";
								$resultpostoprima=mysql_fetch_array(mysql_query($querypostoprima,$conn));
								$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
								$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
								$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																					type,prezzoPrenotazione,posto) 
															VALUES ($rowcvs[1],FALSE,$_REQUEST[idvr],$resultidacquirente[0],$idpass[0],$nbagagli+1,$resultidbagaglio[0],'prima',
															$prezzototale,'$resultpostoprima[0]')";
								mysql_query($queryinsertprenotazione,$conn);
								$totaledapagare=$totaledapagare+$prezzototale;
								
							}
						}
					}
					
				}
				
				if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
				{
					for($i=1;$i<$_REQUEST["bigliettiSeconda"]+1;$i++)
					{
						/*controllo se il passeggero esiste o meno*/
						$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['psaemail'.$i]."'";
						$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));
						
						if(!$idpass)
						{/*passeggero non esiste*/
							$queryinspas="INSERT INTO Anagrafiche (nome,cognome,nascita,sesso,email,tipo)
														VALUES('".$_REQUEST['psanome'.$i]."','".$_REQUEST['psacognome'.$i]."','".$_REQUEST['psanascita'.$i].
														"',\"".$_REQUEST['psasesso'.$i]."\",'".$_REQUEST['psaemail'.$i]."','".$_REQUEST['psatipo'.$i]."')";
							$resultinspas=mysql_query($queryinspas,$conn);
							$queryidpass="SELECT idAnag FROM Anagrafiche WHERE email='".$_REQUEST['psaemail'.$i]."'";
							$idpass=mysql_fetch_array(mysql_query($queryidpass,$conn));						
						}
										
						
						/*calcolare il prezzo della prenotazione che è data dai bagagli e dal prezzo del viaggio di prima classe*/
						$prezzototale=0;
						if($voloa)
						{
							/*seconda diretto*/
							$bagaglio=explode(',',$_REQUEST['psapesobagagli'.$i]);
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=$bagaglio[0]";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$queryprezzoSeconda="SELECT v.prezzoSeconda,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$_REQUEST[idvr]";
							$resultprezzoSeconda=mysql_fetch_array(mysql_query($queryprezzoSeconda,$conn));
							$nbagagli=$_REQUEST["psabagagli".$i];
							$prezzototale=$nbagagli*$bagaglio["1"]+$resultprezzoSeconda["0"];
							if($_REQUEST['psatipo'.$i]=='bambino')
								$prezzototale=$prezzototale-$prezzototale*($resultprezzoSeconda["1"]/100);
							$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
							$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
							$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																				type,prezzoPrenotazione) 
														VALUES ($_REQUEST[idvr],NULL,$resultidacquirente[0],$idpass[0],$nbagagli,$resultidbagaglio[0],'seconda',
														$prezzototale)";
							mysql_query($queryinsertprenotazione,$conn);
							$totaledapagare=$totaledapagare+$prezzototale;
						}
						else
						{	
							/*seconda Con scalo*/
							$queryidbagaglio="SELECT idBagaglio FROM Bagagli WHERE peso=20";
							$resultidbagaglio=mysql_fetch_array(mysql_query($queryidbagaglio,$conn));
							$nbagagli=$_REQUEST["psabagagli".$i];
							$querycompagnieviaggioscali="SELECT vd.idCompagniaEsec,vd.idViaggioDiretto FROM Scali s JOIN ViaggiDiretti vd ON 
														(s.idViaggioDiretto=vd.idViaggioDiretto) WHERE s.idViaggioConScali=$_REQUEST[idvr]";
							$resultcvs=mysql_query($querycompagnieviaggioscali,$conn);
							while($rowcvs=mysql_fetch_array($resultcvs))
							{	
								$queryprezzobagaglio="SELECT prezzo FROM TariffeBagagli WHERE idBagaglio=$resultidbagaglio[0] AND idCompagnia=$rowcvs[0]";
								$prezzoperBagaglio=mysql_fetch_array(mysql_query($queryprezzobagaglio,$conn));
								$queryprezzoSeconda="SELECT v.prezzoSeconda,vd.ridottoPerc FROM ViaggiDiretti vd JOIN Viaggi v ON(vd.idViaggioDiretto=v.idViaggio)
														WHERE vd.idViaggioDiretto=$rowcvs[1]";
								$resultprezzoSeconda=mysql_fetch_array(mysql_query($queryprezzoSeconda,$conn));
								$prezzototale=$nbagagli*$queryprezzobagaglio["0"]+$resultprezzoSeconda["0"];
								if($_REQUEST['psatipo'.$i]=='bambino')
									$prezzototale=$prezzototale-$prezzototale*($resultprezzoSeconda["1"]/100);
								$queryidacquirente="SELECT idAnag FROM Anagrafiche WHERE email='$_SESSION[email]'";
								$resultidacquirente=mysql_fetch_array(mysql_query($queryidacquirente,$conn));
								$queryinsertprenotazione="INSERT INTO Prenotazioni (idViaggio,diretto,idViaggioConScali,acquirente,passeggero,numeroBagagli,idBagaglio,
																					type,prezzoPrenotazione) 
															VALUES ($rowcvs[1],FALSE,$_REQUEST[idvr],$resultidacquirente[0],$idpass[0],$nbagagli,$resultidbagaglio[0],'seconda',
															$prezzototale)";
								mysql_query($queryinsertprenotazione,$conn);
								$totaledapagare=$totaledapagare+$prezzototale;
							}
						}
					}
				}
				
			}
			
		}
		
	?>
</div>

</body>
</html>