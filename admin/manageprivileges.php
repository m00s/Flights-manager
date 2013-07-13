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
				require_once "../component/db_connection.php";
				include "banneradmin.php";
				include "sidebar.php";
				if(isset($_GET['userid']) && $_REQUEST['buttonForm']=="Aggiorna"){
					$id=$_GET['userid'];		
					$query="UPDATE Utenti SET type='$_POST[type]' WHERE idAnag=$id";
					$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));		
				}
				elseif(isset($_GET['userid']) && $_REQUEST['buttonForm']=="Elimina"){
					$id=$_GET['userid'];		
					$query="call eliminaUtente('$id');";
					$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
				}
						 
				echo "<div class=\"content\">
						<div style=\"padding-left:7%\">
						<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
						<tr>
							<td colspan=\"8\" align=\"center\"><h2>Manage privileges</h2></td>
						</tr>
						<th>id</th>
						<th>nome</th>
						<th>cognome</th>
						<th>email</th>
						<th colspan=\"2\">permessi</th>
						<th colspan=\"2\">azioni</th>";
						
							$query="SELECT idAnag, nome, cognome, email, type FROM Anagrafiche a NATURAL JOIN Utenti";
							$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
								while ($row = mysql_fetch_row($result))
									{
									echo "<form method=\"POST\" action=\"manageprivileges.php?userid=$row[0]\" class=\"form\">
												<tr>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
														<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>";
													if($row[4]=="Guest"){
														echo"<td><input type=\"radio\" name=\"type\" value=\"Guest\" checked=\"checked\"/>&nbspGuest&nbsp&nbsp&nbsp</td>
														<td><input type=\"radio\" name=\"type\" value=\"Admin\"/>&nbspAdmin&nbsp</td>";	
													}
													else{
														echo"<td><input type=\"radio\" name=\"type\" value=\"Guest\"/>&nbspGuest&nbsp</td>
														<td><input type=\"radio\" name=\"type\" value=\"Admin\" checked=\"checked\"/>&nbspAdmin&nbsp&nbsp&nbsp</td>";
													}
													echo"
													<td align=\"center\">
														<button type=\"submit\" name=\"buttonForm\" value=\"Aggiorna\"><img src=\"..\images\update_user.png\" alt=\"Aggiorna utente\"></button>
													</td>
													<td align=\"center\">
														<button type=\"submit\" name=\"buttonForm\" value=\"Elimina\"><img src=\"..\images\delete_user.png\" alt=\"Elimina utente\"></button>
													</td>
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