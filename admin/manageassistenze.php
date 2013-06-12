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
			
			if(isset($_GET['viaggio'])){	
				echo "<div class=\"content\">
						<table border=\"2\">
						<tr>
							<td colspan=\"7\" align=\"center\"><h2>Assegna assistenti</h2></td>
						</tr>
						<th>Matricola</th>
						<th>Nome</th>
						<th>Cognome</th>
						<th>Compagnia</th>
						<th colspan=\"2\">permessi</th>";
						$idViaggio=$_GET['viaggio'];
						$query="SELECT va.matricola, va.nome, va.cognome, va.Compagnia FROM Viaggi v, viewAssistenti va, Compagnie c 
								WHERE v.idViaggio=$idViaggio AND v.idCompagniaEsec=c.idCompagnia AND va.Compagnia=c.nome
								AND va.matricola NOT IN (SELECT DISTINCT matricola FROM Assistenze)";
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
					<div class=\"content\" style=\"padding-left:35%\">
					<form method=\"GET\" action=\"manageassistenze.php?viaggio=$row[0]\" class=\"form\">
						<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
							<tr>
								<td align=\"center\"><h2 class=\"tt\">Assegna assistenze</h2></td>
							</tr>
							<td>
							<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Viaggio</label></td>
									<td align=\"center\">
									<select name=\"viaggio\">";
    								$query = "SELECT idViaggio, idCompagniaEsec FROM Viaggi WHERE stato='previsto' ORDER BY idViaggio";
    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
    									while ($row = mysql_fetch_array($result))
    										echo "<option value=\"$row[0]\">$row[0] - $row[1]</option>";	
    								echo"</select>
    								</td>
								</tr>
								<tr>
									<td align=\"center\"><input type=\"submit\" value=\"Step 2\" class=\"button\"/></td>
								</tr>
							</table>
							</td>
						</table>
					</form>
					</div>";
			}
			
		}
		else
			include "error.php";
		?>
		</table>
	</body>
</html>