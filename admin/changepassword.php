<? ob_start(); ?>
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
</html>

	<?php
	if(isset($_GET[cmd])){
		changePassword($_POST[mail], $_POST[oldP], $_POST[newP]);
		echo "<h3></br>Password cambiata con successo</h3></br>
			  <a href=\"default.php\">Torna alla home</a>";
	}
	else
	
	{
		include "banneradmin.php";
		include "sidebar.php";
		echo "<div align=\"center\" style=\"padding-top: 50px;\">
			<form method=\"POST\" action=\"changepassword.php?cmd=submit\" class=\"form\">
			<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000; padding:7px\">
			<tr>
				<td align=\"center\"><h2 class=\"tt\">Recupero password</h2></td>
			</tr>
			<td>
			<table border=\"1\" bordercolor=\"#A6ABB5\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
				
				<tr width=\"96\" align=\"right\" class=\"sm\">
					<td><label>Email di registrazione</label></td>
					<td><input type=\"TEXT\" name=\"mail\" size=\"25\"/></td>
				</tr>
				<tr width=\"96\" align=\"right\" class=\"sm\">
					<td><label>Vecchia password</label></td>
					<td><input type=\"TEXT\" name=\"oldP\" size=\"25\" maxlength=\"8\" /></td>
				</tr>
				<tr align=\"center\">
					<td align=\"right\" class=\"sm\">Password:</td>
					<td align=\"left\"><input name=\"password\" type=\"password\" id=\"password\" size=\"25\" maxlength=\"8\" /></td>
				</tr>
				<tr>
					<td align=\"right\"><input type=\"submit\" value=\"Procedi\" class=\"button\"/></td>
				</tr>
				</table>
			</form>
			</div>";
	}
	?>
<? ob_flush(); ?>