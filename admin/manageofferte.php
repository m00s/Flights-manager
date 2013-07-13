<?php session_start(); ?>
<html>
	<head>
		<title> 
			Airlines 
		</title>
		<head>
			<link rel="stylesheet" type="text/css" href="../component/style.css">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		</head>
	</head>
	
	<body link="#002089" alink="#002089" vlink="#002089">
		<?php
			if(isset($_SESSION['Privileges']) && $_SESSION['Privileges']=="Admin"){
				require "../component/db_connection.php";
				include "banneradmin.php";
				include "sidebar.php";
				if(isset($_REQUEST['option']) && $_REQUEST['option']=="insert"){
					if(isset($_REQUEST['idviaggio']) & isset($_REQUEST['disp'])){
						$idviaggio=$_GET['idviaggio'];	
						$discount=$_REQUEST['discount'];
						$disp=$_REQUEST['disp'];
						$query="INSERT INTO Offerte VALUES ('$idviaggio','$discount','$disp')";
						$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));		
					}
							 
					echo "<div class=\"content\">
							<div style=\"padding-left:7%\">
							<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
							<tr>
								<td colspan=\"7\" align=\"center\"><h2>Inserisci offerta</h2></td>
							</tr>
							<th>id</th>
							<th>giorno</th>
							<th>Partenza</th>
							<th>Arrivo</th>
							<th>Disponibili</th>
							<th>Sconto %</th>
							<th>Azioni</th>";
								$query = "SELECT vi.idViaggio, vi.giorno, vt.Partenza, vt.Arrivo
											FROM Viaggi vi JOIN viewTratte vt ON (vi.idTratta=vt.Tratta) WHERE vi.stato='previsto'
											AND vi.idViaggio NOT IN (SELECT idViaggio FROM Offerte) ORDER BY vi.giorno";
								$result = mysql_query($query,$conn) or die("Query fallita - Select viaggi non in offerta" . mysql_error($conn));
									while ($row = mysql_fetch_row($result))
										{
										echo "<form method=\"GET\" action=\"manageofferte.php\" class=\"form\">
													<tr>
														<input type=\"hidden\" name=\"option\" value=\"insert\">
														<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>
														<td><input style=\"text-align:center\" type=\"TEXT\" name=\"discount\" value=\"10\"/></td>
														<td><input style=\"text-align:center\" type=\"TEXT\" name=\"disp\" value=\"30\"/></td>
														<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
													</tr>
											  </form>";
									}
									echo"				
							</table>
							</div>
					</div>";
				}
				elseif(isset($_REQUEST['option']) && $_REQUEST['option']=="edit"){
					if(isset($_REQUEST['idviaggio'])){
						$idviaggio=$_GET['idviaggio'];	
						$discount=$_REQUEST['discount'];
						$disp=$_REQUEST['disp'];
						$query="DELETE FROM Offerte WHERE idViaggio=$idviaggio";
						$result = mysql_query($query,$conn) or die("Query fallita - Delete offerta" . mysql_error($conn));		
					}
					echo "<div class=\"content\">
							<div style=\"padding-left:2%\">
							<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
							<tr>
								<td colspan=\"7\" align=\"center\"><h2>Modifica offerte</h2></td>
							</tr>
							<th>id</th>
							<th>giorno</th>
							<th>Partenza</th>
							<th>Arrivo</th>
							<th>Disponibili</th>
							<th>Sconto %</th>
							<th>Elimina offerta</th>";
								$query = "SELECT vi.idViaggio, vi.giorno, vt.Partenza, vt.Arrivo, o.disponibili, o.scontoperc
											FROM Viaggi vi JOIN viewTratte vt ON (vi.idTratta=vt.Tratta) JOIN Offerte o ON (vi.idViaggio=o.idViaggio) WHERE vi.stato='previsto' AND vi.idViaggio IN (SELECT idViaggio FROM Offerte) ORDER BY vi.giorno";
								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
									while ($row = mysql_fetch_row($result))
										{
										echo "<form method=\"GET\" action=\"manageofferte.php\" class=\"form\">
													<tr>
														<input type=\"hidden\" name=\"option\" value=\"edit\">
														<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[4] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[5]% </label></td>
														<td><button class=\"button\" type=\"submit\" value=\"Togli\">Togli</button></td>
													</tr>
											  </form>";
									}
									echo"				
							</table>
							</div>
					</div>";
				}
			}
			else
				include "error.php";
		?>
	</body>
</html>