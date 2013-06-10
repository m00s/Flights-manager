<? session_start(); ?>
<?
	if(isset($_SESSION['Privileges']) && $_SESSION['Privileges']=="Admin"){
		if(isset($_GET['area'])){
			require "../component/db_connection.php";
			switch($_GET['area']){
			case "voli":
				$idvolo=$_POST['numero'];
				$oraP=$_POST['oraP'];
				$oraA=$_POST['oraA'];
				$aeroportoP=$_POST['da'];
				$aeroportoA=$_POST['a'];
				$idcomp=$_POST['compagnia'];
				echo $idcomp;
				
				$query="SELECT Tratta FROM viewTratte WHERE Partenza='$aeroportoP' AND Arrivo='$aeroportoA'";
				$result = mysql_query($query,$conn) or die ("Query fallita select" . mysql_error($conn));
				if($row = mysql_fetch_array($result)){
					$idTratta=$row[0];
					$query="INSERT INTO Voli VALUES ('$idvolo', '$oraP', '$oraA', '$idTratta', '$idcomp')";
					echo $query;
					$result = mysql_query($query,$conn) or die("Query fallita insert volo" . mysql_error($conn));
					header("Location: http://localhost:8888/managevoli.php");
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
					$result = mysql_query($query,$conn) or die("Query fallita insert volo" . mysql_error($conn));
					header("Location: http://localhost:8888/admin/managevoli.php?cmd=inserted");
				}
			break;
			
			case "viaggi":
				$query="SELECT idCompagnia FROM Compagnie WHERE nome='$_SESSION[Compagnia]'";
				$result = mysql_query($query,$conn) or die("Query fallita insert tratta 1" . mysql_error($conn));
				$row = mysql_fetch_array($result);
				$idComp=$row[0];
				$query="INSERT INTO Viaggi (giorno, comandante, vice, aereo, idVolo, prezzoPrima, prezzoSeconda, ridotto, idCompagniaEsec, inseritoDa)
						VALUES ('$_SESSION[Giorno]','$_SESSION[Comandante]','$_SESSION[Vice]','$_SESSION[Aereo]','$_SESSION[Volo]','$_POST[pPrima]','$_POST[pSeconda]',
						'$_POST[ridotto]','$idComp','$_SESSION[id]')";
				echo $query;
				$result = mysql_query($query,$conn) or die("Query fallita insert tratta 1" . mysql_error($conn));
				header("Location: http://localhost:8888/admin/manageviaggi.php?cmd=inserted");
			break;
			}
		}
	}
?>