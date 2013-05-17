<? session_start(); ?>
<html>
<head>
	<title> 
		Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
</head>

<body link="#002089" alink="#002089" vlink="#002089">
	<table align=\"center\" style=\"margin-top:50px\">
	<?
	if(isset($_SESSION['Admin']))
	{
		require "functions.php";
		$id = $_SESSION['Admin'];
		echo $id."&nbsp&nbsp<a href=\"login.php?cmd=out\">Logout</a><br/>";		
		if(isset($_GET['submitted'])){
			$v = $_GET['submitted'];
			echo"<h2> $v inserito con successo!</h2>";
		}
		else{
			if(isset($_GET['manage']))
			{
				echo "<br> &nbsp&nbsp<a href=\"administration.php\">Torna al men&ugrave</a><br/>";		
				$cmd=$_GET['manage'];
				switch($cmd)
				{
					case "voli":
						if(isset($_GET['cmd'])){
							$query="INSERT INTO Voli (numero, oraP, oraA, da, a) VALUES ('$_POST[numero]', '$_POST[oraP]', '$_POST[oraA]', '$_POST[da]','$_POST[a]')";
						insert_Ut($query);
						header("Location: http://localhost:8888/administration.php?submitted=Volo");
						}
						else{
						echo "<div align=\"center\" style=\"padding-top: 20px;\">
			  				<form method=\"POST\" action=\"administration.php?manage=voli&cmd=submit\" class=\"form\">
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
										$queryV = "SELECT nome FROM Aeroporti ORDER BY nome";
	    								select($queryV);	
	    								echo"</select>
	    								</td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Aereoporto di arrivo</label></td>
										<td align=\"center\">
										<select name=\"a\">";
	    								select($queryV);	
	    								echo"</select>
	    								</td>
									</tr>
									<tr>
										<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
									</tr>
									</table>
								</form>
								</div>";
							}
						break;
					case "viaggi":
						if(isset($_GET['cmd'])){
							$query="INSERT INTO Viaggi (giorno, voloId, aereo, comandande, vice, prezzo, inseritoDa) VALUES ('$_POST[data]', '$_POST[idvolo]', '$_POST[aereo]', '$_POST[comandante]', '$_POST[vice]', '$_POST[prezzo]', '$_POST[admin]')";
						insert_Ut($query);
						header("Location: http://localhost:8888/administration.php?submitted=Viaggio");
						}
						else{
						echo "<div align=\"center\" style=\"padding-top: 20px;\">
			  				<form method=\"POST\" action=\"administration.php?manage=aerei&cmd=submit\" class=\"form\">
							<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
								<tr>
									<td align=\"center\"><h2 class=\"tt\">Inserisci Viaggio</h2></td>
								</tr>
								<td>
								<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
									
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Volo</label></td>
										<td align=\"center\">
										<select name=\"da\">";
										$queryV = "SELECT numero FROM Voli ORDER BY numero";
	    								select($queryV);	
	    								echo"</select>
	    								</td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td><label>Data</label></td>
										<td><input name=\"data\" type=\"TEXT\" value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" 
										onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" /></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Aereo</label></td>
										<td align=\"center\">
										<select name=\"vice\">";
										$queryA = "SELECT matricola FROM Aerei ORDER BY matricola";
	    								select($queryA);	
	    								echo"</select>
	    								</td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Comandante</label></td>
										<td align=\"center\">
										<select name=\"comandante\">";
										$queryC = "SELECT matricola FROM Dipendenti WHERE grado=\"comandante\" ORDER BY matricola";
	    								select($queryC);	
	    								echo"</select>
	    								</td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>vice-comandante</label></td>
										<td align=\"center\">
										<select name=\"vice\">";
										$queryVC = "SELECT matricola FROM Dipendenti WHERE grado=\"vice\" ORDER BY matricola";
	    								select($queryVC);	
	    								echo"</select>
	    								</td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Prezzo &#8364</label></td>
										<td><input type=\"TEXT\" name=\"prezzo\"/></td>
									</tr>
									<tr>
										<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
									</tr>
									</table>
								</form>
								</div>";
							}
						break;
					case "aerei":
						if(isset($_GET['cmd'])){
							$query="INSERT INTO Aerei (matricola, marca, modello, anno, posti) VALUES ('$_POST[matricola]', '$_POST[marca]', '$_POST[modello]', '$_POST[anno]', '$_POST[posti]')";
						insert_Ut($query);
						header("Location: http://localhost:8888/administration.php?submitted=Aereo");
						}
						else{
						echo "<div align=\"center\" style=\"padding-top: 20px;\">
			  				<form method=\"POST\" action=\"administration.php?manage=aerei&cmd=submit\" class=\"form\">
							<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
								<tr>
									<td align=\"center\"><h2 class=\"tt\">Inserisci Aereo</h2></td>
								</tr>
								<td>
								<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
									
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Matricola</label></td>
										<td><input type=\"TEXT\" name=\"matricola\"/></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Marca</label></td>
										<td><input type=\"TEXT\" name=\"marca\"/></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Modello</label></td>
										<td><input type=\"TEXT\" name=\"modello\"/></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Anno</label></td>
										<td><input type=\"TEXT\" name=\"anno\"/></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Posti</label></td>
										<td><input type=\"TEXT\" name=\"posti\"/></td>
									</tr>
									<tr>
										<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
									</tr>
									</table>
								</form>
								</div>";
							}
						break;
					case "aeroporti":
						if(isset($_GET['cmd'])){
							$query="INSERT INTO Aeroporti (citta, nome, nazione) VALUES ('$_POST[citta]', '$_POST[nome]', '$_POST[nazione]')";
						insert_Ut($query);
						header("Location: http://localhost:8888/administration.php?submitted=Aeroporto");
						}
						else{
						echo "<div align=\"center\" style=\"padding-top: 20px;\">
			  				<form method=\"POST\" action=\"administration.php?manage=aeroporti&cmd=submit\" class=\"form\">
							<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000;padding:7px\">
								<tr>
									<td align=\"center\"><h2 class=\"tt\">Inserisci Aeroporto</h2></td>
								</tr>
								<td>
								<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
									
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Citt&agrave</label></td>
										<td><input type=\"TEXT\" name=\"citta\"/></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Nome</label></td>
										<td><input type=\"TEXT\" name=\"nome\"/></td>
									</tr>
									<tr width=\"96\" align=\"right\" class=\"sm\">
										<td style=\"padding-right:10px\"><label>Nazione</label></td>
										<td><input type=\"TEXT\" name=\"nazione\"/></td>
									</tr>
									<tr>
										<td align=\"center\"><input type=\"submit\" value=\"Inserisci\" class=\"button\"/></td>
									</tr>
									</table>
								</form>
								</div>";
							}
						break;
				}
			}
			else
			{
				echo "<div style=\"margin-top:30px\">
					<h2>Scegli la categoria</h2>
					<ul>
						<li><a href=\"administration.php?manage=voli\">Voli</a></li>
						<li><a href=\"administration.php?manage=viaggi\">Viaggi</a></li>
						<li><a href=\"administration.php?manage=aerei\">Aerei</a></li>
						<li><a href=\"administration.php?manage=aeroporti\">Aeroporti</a></li>
					</ul>
				</div>";
			}
		}
	}
	else
		echo "Non sei autorizzato a stare qui. </br> Effettua il <a href=\"login.php\"> login come admin </a>";
			
	?>
	</table>
</body>
</html>
