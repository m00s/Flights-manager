<? session_start(); ?>
<html>
	<head>
		<title> 
			Airlines 
		</title>
		<head>
			<link rel="stylesheet" type="text/css" href="../component/style.css">
		</head>
	</head>
	
	<body link="#002089" alink="#002089" vlink="#002089">
		<?
		if(isset($_SESSION['Privileges']) && $_SESSION['Privileges']=="Admin"){
			require "../component/db_connection.php";
			include "banneradmin.php";
			include "sidebar.php";
			if(isset($_GET['userid'])){
				$id=$_GET['userid'];		
				$query="INSERT INTO Offerte VALUES ($_GET[viaggio], $_POST[sconto], $_POST[disp]) ";
				$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));		
			}
			echo"
				<div class=\"content\" style=\"padding-left:35%\">
					<tr>
						<td colspan=\"7\" align=\"center\"><h2>Inserisci offerta</h2></td>
					</tr>
					<th>Da</th>
					<th>A</th>
					<th>Data</th>
					<th>Partenza</th>
					<th>Arrivo</th>
					<th>prezzo Base</th>
					<th>Sconto</th>
					<th>Disponibili</th>
					
					<th colspan=\"2\">permessi</th>";
					<form method=\"POST\" action=\"managecheck.php?area=voli\" class=\"form\">
						<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
							<tr>
								<td align=\"center\"><h2 class=\"tt\">Inserisci Offerta</h2></td>
							</tr>
							<td>
							<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Aereoporto di partenza</label></td>
									<td align=\"center\">
									<select name=\"da\">";
									$query = "SELECT * FROM viewVoli WHERE inseritoDa=$_SESSION['id'] ORDER BY giorno";
									$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
									while ($row = mysql_fetch_array($result)){
										echo "<form method=\"POST\" action=\"manageofferte.php?viaggio=$row[0]\" class=\"form\">
												<tr>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[4] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[5] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[6] </label></td>
													
													<td><input type=\"TEXT\" name=\"sconto\"/></td>
													<td><input type=\"TEXT\" name=\"disp\"/></td>
													<td align=\"center\"><input type=\"submit\" value=\"Aggiorna\" class=\"button\"/></td>
												</tr>
										  </form>";
									}
									echo"</select>
									</td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>numero</label></td>
									<td><input type=\"TEXT\" name=\"numero\"/></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>oraP</label></td>
									<td><input name=\"oraP\" type=\"TEXT\" value=\"(hh:mm)\" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
									onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
								</tr>
							</table>
							</td>
						</table>
					</form>	
				</div>";
		}
		else
			echo "Non sei autorizzato a stare qui. </br> Effettua il <a href=\"../login.php\"> login come admin </a>";
		?>
		</table>
	</body>
</html>
