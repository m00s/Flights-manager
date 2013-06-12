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
				if(isset($_GET['userid'])){
					$id=$_GET['userid'];		
					$query="UPDATE Utenti SET type='$_POST[type]' WHERE idAnag=$id";
					$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));		
				}
						 
				echo "<div class=\"content\">
						<table border=\"2\">
						<tr>
							<td colspan=\"7\" align=\"center\"><h2>Manage privileges</h2></td>
						</tr>
						<th>id</th>
						<th>nome</th>
						<th>cognome</th>
						<th>email</th>
						<th colspan=\"2\">permessi</th>";
							$query="SELECT idAnag, nome, cognome, email, type FROM Anagrafiche NATURAL JOIN Utenti";
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
														echo"<td><input type=\"radio\" name=\"type\" value=\"Guest\" checked=\"checked\"/>&nbspGuest&nbsp</td>
														<td><input type=\"radio\" name=\"type\" value=\"Admin\"/>&nbspAdmin&nbsp</td>";	
													}
													else{
														echo"<td><input type=\"radio\" name=\"type\" value=\"Guest\"/>&nbspGuest&nbsp</td>
														<td><input type=\"radio\" name=\"type\" value=\"Admin\" checked=\"checked\"/>&nbspAdmin&nbsp</td>";
													}
													echo"
													<td align=\"center\"><input type=\"submit\" value=\"Aggiorna\" class=\"button\"/></td>
												</tr>
										  </form>";
								}
								echo"				
						</table>
				</div>";
				}
			else
				include "error.php";
		?>
	</body>
</html>