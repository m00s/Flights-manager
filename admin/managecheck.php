<? session_start(); ?>
<?
	if(isset($_SESSION['Privileges']) && $_SESSION['Privileges']=="Admin"){
		if(isset($_GET['area'])){
			switch($_GET['area']){
			case "voli":
				$idvolo=$_POST['numero'];
				$oraP=$_POST['oraP'];
				$oraA=$_POST['oraA'];
				$aeroportoP=$_POST['da'];
				$aeroportoA=$_POST['a'];
				$comp=$_POST['compagnia'];
				
				$query="SELECT t.idTratta FROM Tratte t, Aeroporti a1, Aeroporti a2 WHERE a1.nome=$aeroportoP
						AND a2.nome=$aeroportoA AND t.idAeroportoP=a1.idAeroporto AND t.idAeroportoA=a2.idAeroporto";
				$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
				if($row = mysql_fetch_array($result)){
					$idTratta=$row[0];
					$query="INSERT INTO Voli VALUES ($idvolo, $oraP, $oraA, $idTratta, $comp";
					$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
				}
				else{
					/*	non esiste una tratta per gli aeroporti selezionati	*/
				}
			break;
			}
		}
	}
?>