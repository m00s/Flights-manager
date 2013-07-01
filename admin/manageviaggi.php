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
				if($_GET['option']="insert")
					if(isset($_GET['idVolo']) && isset($_GET['Compagnia'])){
						if(isset($_GET['tariffe']))
						{
							$_SESSION[Aereo]=$_GET[aereo];
							$_SESSION[Comandante]=$_GET[comandante];
							$_SESSION[Vice]=$_GET[vice];
							$_SESSION[Giorno]=$_GET[giorno];
							echo"
								<div class=\"content\">
								<form method=\"POST\" action=\"managecheck.php?area=viaggi\" class=\"form\">
									<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px; margin-left:25%\">
										<tr>
											<td align=\"center\"><h2 class=\"tt\">Inserisci Viaggio</h2></td>
										</tr>
										<td>	
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td><label>Prezzo Business</label></td>
											<td><input type=\"TEXT\" name=\"pPrima\"/></td>
										</tr>
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td><label>Prezzo Turistica</label></td>
											<td><input type=\"TEXT\" name=\"pSeconda\"/></td>
										</tr>
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td><label>ridotto</label></td>
											<td><input type=\"TEXT\" name=\"ridotto\"/></td>
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
						{
							$path = $_SERVER['PHP_SELF'];
							$_SESSION[Volo]=$_GET[idVolo];
							$_SESSION[Compagnia]=$_GET[Compagnia];
							echo"
								<div class=\"content\">
								<form method=\"GET\" action=\"$path\" class=\"form\">
									<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px; margin-left:25%\">
										<tr>
											<td align=\"center\"><h2 class=\"tt\">Inserisci Viaggio</h2></td>
										</tr>
										<td>
										<input type=\"hidden\" name=\"option\" value=\"insert\">
										<input type=\"hidden\" name=\"idVolo\" value=\"$_SESSION[Volo]\">
										<input type=\"hidden\" name=\"Compagnia\" value=\"$_SESSION[Compagnia]\">
										<input type=\"hidden\" name=\"tariffe\" value=\"on\">
										<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
											
											<tr width=\"96\" align=\"right\" class=\"sm\">
												<td><label>Data</label></td>
												<td><input name=\"giorno\" type=\"TEXT\" value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" 
												onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" /></td>
											</tr>
											<tr width=\"96\" align=\"right\" class=\"sm\">
												<td style=\"padding-right:10px\"><label>Aereo</label></td>
												<td align=\"center\">
												<select name=\"aereo\">";
			    								$query = "SELECT matricola FROM Aerei WHERE idCompagnia=
			    											(SELECT idCompagnia FROM Compagnie WHERE nome='$_GET[Compagnia]') ORDER BY matricola";
			    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
			    									while ($row = mysql_fetch_array($result))
			    										echo "<option value=\"$row[0]\">$row[0]</option>";	
			    								echo"</select>
			    								</td>
											</tr>
											<tr width=\"96\" align=\"right\" class=\"sm\">
												<td style=\"padding-right:10px\"><label>Comandante</label></td>
												<td align=\"center\">
												<select name=\"comandante\">";
			    								$query = "SELECT matricola FROM viewComandanti WHERE Compagnia='$_GET[Compagnia]' ORDER BY matricola";
			    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
			    									while ($row = mysql_fetch_array($result))
			    										echo "<option value=\"$row[0]\">$row[0]</option>";	
			    								echo"</select>
			    								</td>
											</tr>
											<tr width=\"96\" align=\"right\" class=\"sm\">
												<td style=\"padding-right:10px\"><label>Vice comandante</label></td>
												<td align=\"center\">
												<select name=\"vice\">";
			    								$query = "SELECT matricola FROM viewViceComandanti WHERE Compagnia='$_GET[Compagnia]' ORDER BY matricola";
			    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
			    									while ($row = mysql_fetch_array($result))
			    										echo "<option value=\"$row[0]\">$row[0]</option>";	
			    								echo"</select>
			    								</td>
											</tr>
											<tr>
												<td align=\"center\"><input type=\"submit\" value=\"Step 3\" class=\"button\"/></td>
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
						$path = $_SERVER['PHP_SELF'];
						echo"
							<div class=\"content\">
	
							<form method=\"GET\" action=\"$path\" class=\"form\">
								<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px; margin-left:25%\">
									<tr>
										<td align=\"center\"><h2 class=\"tt\">Inserisci Viaggio - Compagnia</h2></td>
									</tr>
									<td>
									<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
										<input type=\"hidden\" name=\"option\" value=\"insert\">
										
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>numero di volo</label></td>
											<td align=\"center\">
											<select name=\"idVolo\">";
											// MOSTRO SOLO I VOLI CHE NON APPARTENGONO AD ALCUN VIAGGIO
											$query = "SELECT vo.idVolo FROM Voli vo ";
		    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		    									while ($row = mysql_fetch_array($result))
		    										echo "<option value=\"$row[0]\">$row[0]</option>";
		    								echo"</select>
		    								</td>
										</tr>
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>Compagnia esecutrice</label></td>
											<td align=\"center\">
											<select name=\"Compagnia\">";
		    								$query = "SELECT nome FROM Compagnie ORDER BY nome";
		    								$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		    									while ($row = mysql_fetch_array($result))
		    										echo "<option value=\"$row[0]\">$row[0]</option>";	
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
			{
				if(isset($_GET['cmd']))
					if(($_GET['cmd'])=="inserted")
					{
						echo"
						<div class=\"content\">
							<div style=\"padding-left:15%\">
								<meta http-equiv=\"refresh\" content=\"3;url=http://localhost:8888/admin/manageviaggi.php?option=insert\">
								<h2>Viaggio inserito con successo</h2>
								<h4>a breve sarai reindirizzato alla pagina delle hostess zoccolone..</h4>
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









