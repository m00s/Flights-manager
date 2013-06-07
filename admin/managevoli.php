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
			if(isset($_GET['option'])){
				if($_GET['option']="insert"){
				echo"
					<div class=\"content\" style=\"padding-left:35%\">
					<form method=\"POST\" action=\"managecheck.php?area=voli\" class=\"form\">
						<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
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
									<td style=\"padding-right:10px\"><label>oraP</label></td>
									<td><input name=\"oraP\" type=\"TEXT\" value=\"(hh:mm)\" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
									onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>oraA</label></td>
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
    										echo "<option value=$row[0]>$row[0]</option>";
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
    										echo "<option value=$row[0]>$row[0]</option>";	
    								echo"</select>
    								</td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>Compagnia</label></td>
									<td align=\"center\">
									<select name=\"compagnia\">";
    								$query = "SELECT nome FROM Compagnie ORDER BY nome";
    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
    									while ($row = mysql_fetch_array($result))
    										echo "<option value=$row[0]>$row[0]</option>";	
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
		}
		else
			echo "Non sei autorizzato a stare qui. </br> Effettua il <a href=\"../login.php\"> login come admin </a>";
		?>
		</table>
	</body>
</html>


<?
/*
				if($_GET['option']="insert"){
				echo"
					<div class=\"content\">
					<form method=\"POST\" action=\"managecheck.php?area=voli\" class=\"form\">
						<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
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
									<td style=\"padding-right:10px\"><label>oraP</label></td>
									<td><input name=\"oraP\" type=\"TEXT\" value=\"(hh:mm)\" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
									onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td style=\"padding-right:10px\"><label>oraA</label></td>
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
    										echo "<option>$row[0]</option>";
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
    										echo "<option>$row[0]</option>";	
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
				else
					if($_GET['option']="update"){
						if(isset($_GET['option'])){
							$query="SELECT * FROM Voli WHERE numero='$_GET[volo]'";
							$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
							echo"
							<form method=\"POST\" action=\"managecheck.php?area=voli\" class=\"form\">
								<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
									<tr>
										<td align=\"center\"><h2 class=\"tt\">Modifica Volo</h2></td>
									</tr>
									<td>
									<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>numero</label></td>
											<td><input type=\"TEXT\" name=\"numero\"/ value=";$result['numero']echo"></td>
										</tr>
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>oraP</label></td>
											<td><input name=\"oraP\" type=\"TEXT\" value=";$result['oraP']echo" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
											onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
										</tr>
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>oraA</label></td>
											<td><input name=\"oraA\" type=\"TEXT\" value=";$result['oraA']echo" onblur=\"if(this.value=='') this.value='(hh:mm)';\" 
											onfocus=\"if(this.value=='(hh:mm)') this.value='';\" /></td>
										</tr>
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>Aereoporto di partenza</label></td>
											<td align=\"center\">
											<select name=\"da\">";
											$query = "SELECT nome FROM Aeroporti ORDER BY nome";
		    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		    									while ($row = mysql_fetch_array($result))
		    										echo "<option>$row[0]</option>";
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
		    										echo "<option>$row[0]</option>";	
		    								echo"</select>
		    								</td>
										</tr>
										<tr>
											<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
										</tr>
									</table>
									</td>
								</table>
							</form>";
						}
						else{
						echo"
							<form method=\"GET\" action=\"managevoli.php?option=edit\" class=\"form\">
								<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
									<tr>
										<td align=\"center\"><h2 class=\"tt\">Seleziona il Volo da modificare</h2></td>
									</tr>
									<td>
									<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>Aereoporto di arrivo</label></td>
											<td align=\"center\">
											<select name=\"idvolo\">";
		    								$query = "SELECT numero FROM Voli ORDER BY numero";
		    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		    								while ($row = mysql_fetch_array($result))
		    									echo "<option>$row[0]</option>";	
		    								echo"</select>
		    								</td>
										</tr>
										<tr>
											<td align=\"center\"><input type=\"submit\" value=\"Vai\" class=\"button\"/></td>
										</tr>
									</table>
									</td>
								</table>
							</form>	";
						}
							
					}
*/
?>