	
<? session_start(); ?>
<html>
<head>
	<title> 
		MS Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
</head>

<body link="#002089" alink="#002089" vlink="#002089">
	<?
	require "functions.php";
	if (isset($_SESSION['id']))
	{
		if(isset($_GET['cmd']))
		{
			$cmd=$_GET['cmd'];
			$id = $_SESSION['id'];
			switch($cmd)
			{
				case "ss": $idvolo=$_GET['id'];
						   $datavolo=$_GET['d'];
						   $host="localhost"; 
						   $user="root"; 
						   $pwd= "root";
						   //$user="msartore"; 
						   //$pwd= "ND0yj5lV"; 
						   $dbname="MS-Airlines";
						   //$dbname="msartore-ES";
						   $conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
						   mysql_select_db($dbname);
						   $query="SELECT  vo.cittaP as Da, vo.cittaA as A, vo.OraP as Partenza, vo.OraA as Arrivo, TIMEDIFF(vo.OraA, vo.OraP) as Durata, pe.nome, pe.cognome, vi.prezzo, vi.postiliberi, a.marca, a.modello
									FROM Voli vo, Viaggi vi, Aerei a, Personale pe
									WHERE vi.voloId='AZ112' AND vi.dat='2012-08-12' AND vo.numero=vi.voloID AND pe.matricola=vi.comandante AND a.matricola=vi.aereo";
						   $result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
						   $num_righe=mysql_num_rows($result);
						   $row = mysql_fetch_array($result);
						   echo "<div align=\"center\"><h2>Volo $ar </h2>
									<form name=form1 method=\"POST\" action=\"default.php?cmd=search\">
										<table cellpadding=\"8\"style=\"background-color:white; border-right:1px solid #99FFFF; border-bottom:2px solid #99FFFF; padding:7px\">
											<tr>
												<td>
													Da
												</td>
												<td>
													$row[0]
												<td>
											</tr>
											<tr>
												<td>
													a
												</td>
												<td>
													$row[1]
												</td>
											</tr>
											<tr>
												<td>
													Partenza
												</td>
												<td>
													$row[2]
												</td>
											</tr>
											<tr>
												<td>
													Arrivo
												</td>
												<td>
													$row[3]
												</td>
											</tr>
											<tr>
												<td>
													Durata
												</td>
												<td>
													$row[4]
												</td>
											</tr>
											<tr>
												<td>
												</td>
											</tr>
										</table>
									<form>
								  </div>";
				break;
				case "search":	$da=$_POST['da'];
								$a=$_POST['a'];
								$part=invert_data($_POST['partenza']);
								$rit=invert_data($_POST['ritorno']);
								$host="localhost"; 
								$user="root"; 
								$pwd= "root";
								//$user="msartore"; 
								//$pwd= "ND0yj5lV"; 
								$dbname="MS-Airlines";
								//$dbname="msartore-ES";
								$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
								mysql_select_db($dbname);
								$query="SELECT  vo.cittaP as Da, vo.cittaA as A, vo.OraP as Partenza, vo.OraA as Arrivo, TIMEDIFF(vo.OraA, vo.OraP) as Durata, pe.nome, pe.cognome, vi.prezzo, vi.postiliberi, a.marca, a.modello
										 FROM Voli vo, Viaggi vi, Aerei a, Personale pe
										 WHERE vi.voloId='AZ112' AND vi.dat='2012-08-12' AND vo.numero=vi.voloID AND pe.matricola=vi.comandante AND a.matricola=vi.aereo";
								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
								$num_righe=mysql_num_rows($result);
								$row = mysql_fetch_array($result);
					
				break;
			}
		}
		else
		{
			if(get_type($_SESSION['id'], 'id') == "admin")
				header("Location: http://localhost:8888/MAMP/reg.php?cmd=log");
			$arr = get_record($_SESSION['id'], "id");
			echo $arr['nome']. " <a href=\"login.php?cmd=out\">Logout</a> <br/>";
			
			$host="localhost"; 
			$user="root"; 
			$pwd= "root";
			//$user="msartore"; 
			//$pwd= "ND0yj5lV"; 
			$dbname="MS-Airlines";
			//$dbname="msartore-ES";
			$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
			mysql_select_db($dbname);
			$query="SELECT v.dat, vo.cittaP, vo.cittaA, vo.oraP, vo.oraA, v.prezzo, v.voloId FROM Viaggi v, Voli vo WHERE vo.numero=v.voloId AND v.stato='previsto' ORDER BY v.dat, v.prezzo";
			$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
			$num_righe=mysql_num_rows($result);
			
			$query="SELECT DISTINCT cittaP From Voli vo, Viaggi v WHERE vo.numero=v.voloId AND v.stato='previsto' ORDER BY cittaP";
			$partenze = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
			$num_partenze=mysql_num_rows($partenze);
			
			$query="SELECT DISTINCT cittaA From Voli vo, Viaggi v WHERE vo.numero=v.voloId AND v.stato='previsto' ORDER BY cittaA";
			$arrivi = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
			$num_arrivi=mysql_num_rows($arrivi);
			
			//FORM DI RICERCA
			echo"<table align=\"center\" style=\"margin-top:50px\">
				<td>
					<form name=form1 method=\"POST\" action=\"default.php?cmd=search\">
						<table cellpadding=\"8\"style=\"background-color:white; border-right:1px solid #99FFFF; border-bottom:2px solid #99FFFF; padding:7px\">
							<tr>
								<td align=\"right\" class=\"sm\">Da:</td>
								<td>
								<select name=\"da\">";
									while ($row2 = mysql_fetch_array($partenze))
    									populate_select($row2);	
			echo"				</select>
								</td>
							</tr>
							<tr>
								<td align=\"right\" class=\"sm\">A:</td>
								<td>
								<select name=\"a\">";
									while ($row3 = mysql_fetch_array($arrivi))
    									populate_select($row3);	
			echo"				</select>
								</td>
							</tr>
							<tr>
								<td align=\"right\" class=\"mm\">Partenza:</td>
								<td align=\"left\"><input name=\"partenza\" type=\"TEXT\" value=\"(dd/mm/aaaa)\" onblur=\"if(this.value=='') this.value='(dd/mm/aaaa)';\" 
								onfocus=\"if(this.value=='(dd/mm/aaaa)') this.value='';\" /></td>
							</tr>
							<tr>
								<td align=\"right\" class=\"mm\">Ritorno:</td>
								<td align=\"left\"><input name=\"ritorno\" type=\"TEXT\" value=\"(dd/mm/aaaa)\" onblur=\"if(this.value=='') this.value='(dd/mm/aaaa)';\" 
								onfocus=\"if(this.value=='(dd/mm/aaaa)') this.value='';\" /></td>
							</tr>
							<tr align=\"right\">
								<td></td>
								<td height=\"40px\">
									<input type=\"image\" src =\"images/search.png\" height=\"50\" width=\"50\" alt=\"Submit\" />
								</td>
							</tr>
						</table>
					</form>
				</td><td width=\"50px\"></td>";
			echo "<td align=\"center\" style=\"background-color:white\">";
			echo "<table style=\"margin-top:10px\" border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" class=\"table\" cellpadding=\"10\">
			<tr><h2 class=\"tt\">LAST MINUTE</h2></tr>
			<tr class=\"header\" bgcolor=\"#A5A5A5\">
  			<th>DATA</th>
  			<th>DA</th>
  			<th>A</th>
  			<th>PREZZO</th>
  			<th>VEDI</th>
			</tr>";
			//while ($row = mysql_fetch_row($result))
    			//echo_row($row);
    		while ($row = mysql_fetch_array($result))
    			echo_row($row);

			echo "</table></td></table>";
		}
	}
	else
		//header("Location: http://basidati/basidati/~mabarich/log.php?cmd=nauth");
		echo "Non sei autorizzato a stare qui. </br> Effettua il <a href=\"login.php\"> login </a>";
	?>
</body>
</html>
