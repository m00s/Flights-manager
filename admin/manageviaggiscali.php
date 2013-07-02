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
			if(isset($_REQUEST['option']) && $_REQUEST['option']="insert"){
				if(isset($_REQUEST['scali'])){
					$n = $_REQUEST['scali']+1;
					echo"
						<div class=\"content\">
						<form method=\"POST\" action=\"managecheck.php?area=viaggiScali\" class=\"form\">
							<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px; margin-left:20%\">
								<tr>
									<td align=\"center\"><h2 class=\"tt\">Inserisci Viaggio con scali - Scegli i viaggi</h2></td>
								</tr>
								<td>
								<input type=\"hidden\" name=\"scali\" value=\"$n\">
								<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >";
									for($k=1; $k<=$n; $k++){
									echo"
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Viaggio $k</label></td>
										<td align=\"center\">
										<select name=\"idViaggio$k\">";
											$query = "SELECT v.idViaggioDiretto, v.idVolo, vi.giorno, vt.Partenza, vt.Arrivo FROM ViaggiDiretti v JOIN Viaggi vi ON 														(vi.idViaggio=v.idViaggioDiretto) JOIN viewTratte vt ON (vi.idTratta=vt.Tratta) WHERE vi.stato='previsto' ORDER BY vi.giorno";
											$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
											while ($row = mysql_fetch_array($result))
												echo "<option value=\"$row[0]\"> $row[1] - $row[2] da: $row[3] a: $row[4]</option>";
										echo"
	    								</select>
	    								</td>
									</tr>";}
									echo"
									<tr>
										<td align=\"center\"><input type=\"submit\" value=\"Step 3\" class=\"button\"/></td>";
										if(isset($_REQUEST['error']) && $_REQUEST['error']=="aero")
											echo"<td><span class=\"error\">&nbsp &nbsp (!) Gli aeroporti di arrivo e partenza non corrispondono</span></td>";
										if(isset($_REQUEST['error']) && $_REQUEST['error']=="date")
											echo"<td><span class=\"error\">&nbsp &nbsp (!) Le date di arrivo e partenza non corrispondono</span></td>";
									echo"
									</tr>
								</table>
								</td>
							</table>
						</form>
						</div>";
				}
				else{
				$path = $_SERVER['PHP_SELF'];
				echo"
					<div class=\"content\">
							<form method=\"POST\" action=\"$path\" class=\"form\">
								<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px; margin-left:25%\">
									<tr>
										<td align=\"center\"><h2 class=\"tt\">Inserisci Viaggio con scali</h2></td>
									</tr>
									<td>
									<input type=\"hidden\" name=\"option\" value=\"insert\">
									<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
										<tr width=\"96\" align=\"right\" class=\"sm\">
											<td style=\"padding-right:10px\"><label>Scali</label></td>
											<td align=\"center\">
											<select name=\"scali\">";
												for ($n=1; $n<4; ++$n)
													echo"<option value=\"$n\">$n</option>";
		    								echo"
		    								</select>
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
								<meta http-equiv=\"refresh\" content=\"3;url=http://localhost:8888/admin/manageviaggiscali.php?option=insert\">
								<h2>Viaggio inserito con successo</h2>
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