<?php session_start(); ?>
<?php
	if(isset($_SESSION['Privileges']) && $_SESSION['Privileges']=="Admin"){
		if(isset($_GET['area'])){
			require_once "../component/db_connection.php";
			switch($_GET['area']){
			case "voli":
				$idvolo=$_POST['numero'];
				$oraP=$_POST['oraP'];
				$oraA=$_POST['oraA'];
				$aeroportoP=$_POST['da'];
				$aeroportoA=$_POST['a'];
				$idcomp=$_POST['compagnia'];

				$query="SELECT Tratta FROM viewTratte WHERE Partenza='$aeroportoP' AND Arrivo='$aeroportoA'";
				$result = mysql_query($query,$conn) or die ("Query fallita select" . mysql_error($conn));
				if($row = mysql_fetch_array($result)){
					$idTratta=$row[0];
					$query="INSERT INTO Voli VALUES ('$idvolo', '$oraP', '$oraA', '$idTratta', '$idcomp')";
					$result = mysql_query($query,$conn) or die("Query fallita insert volo" . mysql_error($conn));
					header("Location: /basidati/~msartore/admin/managevoli.php?cmd=inserted");
				}
				else{
					$query="SELECT a1.idAeroporto, a2.idAeroporto FROM Aeroporti a1, Aeroporti a2 WHERE a1.nome='$aeroportoP' AND a2.nome='$aeroportoA'";
					$result = mysql_query($query,$conn) or die("Query fallita insert tratta 1" . mysql_error($conn));
					$row = mysql_fetch_array($result);
					$query="INSERT INTO Tratte (da,a) VALUES ('$row[0]','$row[1]')";
					$result = mysql_query($query,$conn) or die("Query fallita insert tratta" . mysql_error($conn));
					$query="SELECT Tratta FROM viewTratte WHERE Partenza='$aeroportoP' AND Arrivo='$aeroportoA'";
					$result = mysql_query($query,$conn) or die ("Query fallita select" . mysql_error($conn));
					$row = mysql_fetch_array($result);
					$idTratta=$row[0];
					$query="INSERT INTO Voli VALUES ('$idvolo', '$oraP','$oraA','$idTratta','$idcomp')";
					$result = mysql_query($query,$conn) or die("Query fallita insert volo insert tratta" . mysql_error($conn));
					header("Location: /basidati/~msartore/admin/managevoli.php?cmd=inserted");
				}
			break;

			case "viaggi":
				$query="SELECT idCompagnia FROM Compagnie WHERE nome='$_SESSION[Compagnia]'";
				$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
				$row = mysql_fetch_array($result);
				$idComp=$row[0];
				$query="SELECT idTratta FROM Voli WHERE idVolo='$_SESSION[Volo]'";
				$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
				$row = mysql_fetch_array($result);
				$idTratta=$row[0];
				$query="call InserisciViaggio ('$_SESSION[Volo]','$_SESSION[Giorno]', '$_POST[pPrima]', '$_POST[pSeconda]', '$idTratta', '$_SESSION[id]', '$idComp',
						'$_SESSION[Aereo]', '$_SESSION[Comandante]', '$_SESSION[Vice]', '$_POST[ridotto]');";
				$result = mysql_query($query,$conn) or die("Query fallita procedura" . mysql_error($conn));
				header("Location: /basidati/~msartore/admin/manageviaggi.php?cmd=inserted");
			break;

			case "assistenze":
				$query="INSERT INTO Assistenze VALUES ('$_POST[viaggio]','$_POST[assistente]')";
				$result = mysql_query($query,$conn) or die("Query fallita insert tratta 1" . mysql_error($conn));
				header("Location: /basidati/~msartore/admin/manageassistenze.php?viaggio='$_POST[viaggio]'");
			break;
			
			case "viaggiScali":
				$n=$_REQUEST['scali'];
				// CONTROLLO CHE I VIAGGI SIANO SCELTI IN MODO CHE GLI AEROPORTI CORRISPONDANO
				$errorA=false; // ERRORE AEROPORTI
				$errorD=false; // ERRORE DATA
				$errorO=false; // ERRORE ORA
				for($k=1; $k<$n & !$errorA & !$errorD; $k++){
					$var="idViaggio".$k;
					$w=$k+1;
					$var1="idViaggio".$w;
					$query="SELECT v1.a, v1.Giorno, v2.da, v2.Giorno, v1.oraA, v2.oraP FROM viewViaggiDiretti v1, viewViaggiDiretti v2 WHERE
							v1.idViaggio=$_REQUEST[$var] AND v2.idViaggio=$_REQUEST[$var1]";
					$result = mysql_query($query,$conn) or die("Query fallita - SELECT Aeroporti Arrivo - Partenza" . mysql_error($conn));
					$row = mysql_fetch_array($result);
					if($row[0]!=$row[2])
						$errorA=true;
					if($row[1]!=$row[3])
						$errorD=true;
					if($row[4]<$row[5])
						$errorO=true;
				}
				
				if($errorA || $errorD){
					$n=$n-1;
					if($errorA)
						header("Location: /basidati/~msartore/admin/manageviaggiscali.php?option=insert&scali=$n&error=aero");
					else{
						if($errorD)
							header("Location: /basidati/~msartore/admin/manageviaggiscali.php?option=insert&scali=$n&error=date");
						else
							header("Location: /basidati/~msartore/admin/manageviaggiscali.php?option=insert&scali=$n&error=ora");
					}		
				}
				else{
					$prezzoPtotale=0;
					$prezzoStotale=0;
					$postiPtotale=1000;
					$postiStotale=1000;
					$aeroportoP=0;
					$aeroportoA=0;
					$giorno=0;
					
					// CON IL CICLO CALCOLO I PREZZI/POSTI TOTALI
					for($k=1; $k<=$n; $k++){
						$var="idViaggio".$k;
						
						// SE $k É IL PRIMO O L'ULTIMO VIAGGIO PRENDO L'AEROPORTO PER CREARE LA TRATTA SUCCESSIVAMENTE
						if($k==1){
							$query="SELECT vt.Partenza, v.giorno FROM viewTratte vt JOIN Viaggi v ON (v.idTratta=vt.Tratta)
								WHERE idViaggio=$_REQUEST[$var]";
							$result = mysql_query($query,$conn) or die("Query fallita - SELECT Aeroporto Partenza" . mysql_error($conn));
							$row = mysql_fetch_array($result);
							$aeroportoP=$row[0];
							$giorno=$row[1];
						}
						if($k==$n){
							$query="SELECT vt.Arrivo FROM viewTratte vt JOIN Viaggi v ON (v.idTratta=vt.Tratta)
								WHERE idViaggio=$_REQUEST[$var]";
							$result = mysql_query($query,$conn) or die("Query fallita - SELECT Aeroporto Arrivo" . mysql_error($conn));
							$row = mysql_fetch_array($result);
							$aeroportoA=$row[0];
						}
							
						$query="SELECT v.prezzoPrima, v.prezzoSeconda FROM Viaggi v	WHERE v.idViaggio=$_REQUEST[$var]";
						$result = mysql_query($query,$conn) or die("Query fallita - SELECT Prezzi" . mysql_error($conn));
						$row = mysql_fetch_array($result);
						$prezzoPtotale+=$row[0];
						$prezzoStotale+=$row[1];
										
						$query="SELECT aereo FROM ViaggiDiretti WHERE idViaggioDiretto=$_REQUEST[$var]";
						$result = mysql_query($query,$conn) or die("Query fallita - SELECT aereo" . mysql_error($conn));
						$row = mysql_fetch_array($result);
						$aereo=$row[0];
						$query="SELECT getPosti('0','$aereo'), getPosti('1','$aereo')";
						$result = mysql_query($query,$conn) or die("Query fallita - SELECT Posti aereo" . mysql_error($conn));
						$row = mysql_fetch_array($result);
						if($postiPtotale > $row[0])
							$postiPtotale = $row[0];
						if($postiStotale > $row[1])
							$postiStotale = $row[1];
	
					}
					
					// CERCO ID_TRATTA E SE NON É PRESENTE LA CREO
					$query="SELECT Tratta FROM viewTratte WHERE Partenza='$aeroportoP' AND Arrivo='$aeroportoA'";
					$result = mysql_query($query,$conn) or die ("Query fallita select" . mysql_error($conn));
					if($row = mysql_fetch_array($result)){
						$idTratta=$row[0];
					}
					else{
						$query="SELECT a1.idAeroporto, a2.idAeroporto FROM Aeroporti a1, Aeroporti a2 WHERE a1.nome='$aeroportoP' AND a2.nome='$aeroportoA'";
						$result = mysql_query($query,$conn) or die("Query fallita - Select tratta 1" . mysql_error($conn));
						$row = mysql_fetch_array($result);
						$query="INSERT INTO Tratte (da,a) VALUES ('$row[0]','$row[1]')";
						$result = mysql_query($query,$conn) or die("Query fallita - Insert Tratta" . mysql_error($conn));
						$query="SELECT Tratta FROM viewTratte WHERE Partenza='$aeroportoP' AND Arrivo='$aeroportoA'";
						$result = mysql_query($query,$conn) or die ("Query fallita - Select Tratta" . mysql_error($conn));
						$row = mysql_fetch_array($result);
						$idTratta=$row[0];
					}
					// INSERISCO DENTRO LE TABELLE DEI VIAGGI
					$query="call inserisciViaggioConScali ('$giorno','$prezzoPtotale','$prezzoStotale','$postiPtotale','$postiStotale','$idTratta','$_SESSION[id]', @x);";
					$result = mysql_query($query,$conn) or die ("Query fallita Procedura Inserimento" . mysql_error($conn));
						
					// RECUPERO L'ID DALLA PROCEDURA E LO USO PER INSERIRE GLI SCALI
					$query="SELECT @x";
					$result = mysql_query($query,$conn) or die ("Query fallita - OUT Procedura" . mysql_error($conn));
					if($row = mysql_fetch_array($result)){
						$idViaggio=$row[0];
						for($k=1; $k<=$n; $k++){
							$var="idViaggio".$k;
							$query="INSERT INTO Scali Values ('$idViaggio','$_REQUEST[$var]','$k')";
							$result = mysql_query($query,$conn) or die("Query fallita - INSERT Scalo: ".$k . mysql_error($conn));
						}	
					}
					header("Location: /basidati/~msartore/admin/manageviaggiscali.php?cmd=inserted");
				}
			break;			
			
			}
		}
	}
?>


