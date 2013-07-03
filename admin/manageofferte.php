<?php session_start(); ?>
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
		<?php
			if(isset($_SESSION['Privileges']) && $_SESSION['Privileges']=="Admin"){
				require "../component/db_connection.php";
				include "banneradmin.php";
				include "sidebar.php";
				if(isset($_REQUEST['idviaggio']) & isset($_REQUEST['disp'])){
					$idviaggio=$_GET['idviaggio'];	
					$discount=$_REQUEST['discount'];
					$disp=$_REQUEST['disp'];
					$query="INSERT INTO Offerte VALUES ('$idviaggio','$discount','$disp')";
					$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));		
				}
						 
				echo "<div class=\"content\">
						<div style=\"padding-left:5%\">
						<table border=\"2\">
						<tr>
							<td colspan=\"7\" align=\"center\"><h2>Modifica offerte</h2></td>
						</tr>
						<th>id</th>
						<th>giorno</th>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th colspan=\"3\">azioni</th>";
							$query = "SELECT vi.idViaggio, vi.giorno, vt.Partenza, vt.Arrivo
										FROM Viaggi vi JOIN viewTratte vt ON (vi.idTratta=vt.Tratta) WHERE vi.stato='previsto' ORDER BY vi.giorno
										AND vi.idViaggio NOT IN (SELECT idViaggio FROM Offerte)";
							$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
								while ($row = mysql_fetch_row($result))
									{
									echo "<form method=\"GET\" action=\"manageofferte.php\" class=\"form\">
												<tr>
													<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\">
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
													<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>
													<td><input type=\"TEXT\" name=\"discount\" value=\"10\"/></td>
													<td><input type=\"TEXT\" name=\"disp\" value=\"30\"/></td>
													<td align=\"center\"><input type=\"submit\" value=\"Vai\" class=\"button\"/></td>
												</tr>
										  </form>";
								}
								echo"				
						</table>
						</div>
				</div>";
				}
			else
				include "error.php";
		?>
	</body>
</html>