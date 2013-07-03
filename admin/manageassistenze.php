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
			
			if(isset($_REQUEST['idviaggio'])){	
				echo "<div class=\"content\">
						<table border=\"1px\" style=\"padding:7px; margin-left:10%\">
						<tr>
							<td colspan=\"7\" align=\"center\"><h2>Assegna assistenti</h2></td>
						</tr>
						<th>Matricola</th>
						<th>Nome</th>
						<th>Cognome</th>
						<th>Compagnia</th>
						<th colspan=\"2\">permessi</th>";
						$idViaggio=$_REQUEST['idviaggio'];
						$query="SELECT va.matricola, va.nome, va.cognome, va.Compagnia 
								FROM ViaggiDiretti v, viewAssistenti va, Compagnie c 
								WHERE v.idViaggioDiretto=$idViaggio AND v.idCompagniaEsec=c.idCompagnia AND va.Compagnia=c.nome
								AND va.matricola NOT IN (SELECT DISTINCT matricola FROM Assistenze WHERE idViaggio=$idViaggio)";
						$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
						while ($row = mysql_fetch_row($result))
						{
							echo "<form method=\"POST\" action=\"managecheck.php?area=assistenze\" class=\"form\">
									<tr>
										<input type=\"hidden\" name=\"viaggio\" value=\"$idViaggio\"/>
										<input type=\"hidden\" name=\"assistente\" value=\"$row[0]\"/>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>";
										echo"
										<td align=\"center\"><input type=\"submit\" value=\"Assegna\" class=\"button\"/></td>
									</tr>
							  </form>";
						}
						echo"				
						</table>
				</div>";
			}
			else{
				echo"
					<div class=\"content\">
						<table border=\"1px\" style=\"padding:7px; margin-left:10%\">
						<tr>
							<td colspan=\"7\" align=\"center\"><h2>Assegna assistenti</h2></td>
						</tr>
						<th>Giorno</th>
						<th>Da</th>
						<th>A</th>
						<th>Compagnia</th>";
						$query="SELECT v.idViaggio, v.giorno, v.da, v.a, v.compagnia FROM viewViaggiDiretti v";
						$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
						while ($row = mysql_fetch_row($result))
						{
							echo "<form method=\"GET\" action=\"manageassistenze.php?idviaggio=$row[0]\" class=\"form\">
									<tr>
										<input type=\"hidden\" name=\"idviaggio\" value=\"$row[0]\"/>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>
										<td align=\"center\" style=\"padding-right:10px\"><label> $row[4] </label></td>";
										echo"
										<td align=\"center\"><input type=\"submit\" value=\"Vai\" class=\"button\"/></td>
									</tr>
							  </form>";
						}
						echo"				
						</table>
					</div>";
			}
			
		}
		else
			include "error.php";
		?>
		</table>
	</body>
</html>