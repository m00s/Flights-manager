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
			if(isset($_GET['option'])){
				if($_GET['option']="insert"){
				echo"
					<div class=\"content\">
					<form method=\"POST\" action=\"managecheck.php?area=voli\" class=\"form\">
						<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px; margin-left:25%\">
							<tr>
								<td align=\"center\"><h2 class=\"tt\">Inserisci Volo</h2></td>
							</tr>
							<td>
							<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>numero</label></td>
									<td><input type=\"TEXT\" name=\"numero\"/></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Ora di partenza</label></td>
									<td><input name=\"oraP\" type=\"TEXT\" value=\"(hh:mm)\" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
									onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Ora di arrivo</label></td>
									<td><input name=\"oraA\" type=\"TEXT\" value=\"(hh:mm)\" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
									onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Aereoporto di partenza</label></td>
									<td align=\"center\">
									<select name=\"da\">";
									$query = "SELECT nome FROM Aeroporti ORDER BY nome";
    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
    									while ($row = mysql_fetch_array($result))
    										echo "<option value=\"$row[0]\">$row[0]</option>";
    								echo"</select>
    								</td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Aereoporto di arrivo</label></td>
									<td align=\"center\">
									<select name=\"a\">";
    								$query = "SELECT nome FROM Aeroporti ORDER BY nome";
    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
    									while ($row = mysql_fetch_array($result))
    										echo "<option value=\"$row[0]\">$row[0]</option>";	
    								echo"</select>
    								</td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Compagnia</label></td>
									<td align=\"center\">
									<select name=\"compagnia\">";
    								$query = "SELECT idCompagnia, nome FROM Compagnie ORDER BY nome";
    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
    									while ($row = mysql_fetch_array($result))
    										echo "<option value=\"$row[0]\">$row[1]</option>";	
    								echo"</select>
    								</td>
								</tr>
								<tr>
									<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
								</tr>
							</table>
							</td>
						</table>
					</form>	
					</div>";
				}
			}
			else
			{
				if(isset($_GET['cmd']))
					if(($_GET['cmd'])=="inserted")
					{
						echo"
						<div class=\"content\">
							<div style=\"padding-left:15%\">
								<meta http-equiv=\"refresh\" content=\"3;url=http://localhost:8888/admin/managevoli.php?option=insert\">
								<h2>Volo inserito con successo</h2>
								<h4>a breve sarai reindirizzato..</h4>
							</div>
						</div>";
					}
			}
		}
		else
			include "error.php";
		?>
		</table>
	</body>
</html>
