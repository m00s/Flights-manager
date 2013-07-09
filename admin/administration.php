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
				require "banneradmin.php";
				include "sidebar.php";
				$id=$_SESSION['id'];
				$query="SELECT nome, cognome FROM Anagrafiche WHERE idAnag=$id";
				$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
				if($row = mysql_fetch_row($result))
					$string = $row[0]." ".$row[1];
				echo "<div class=\"content\">
						<div style=\"padding-left:12%\">
						<table border=\"2\">
						<tr>
							<td colspan=\"7\" align=\"center\"><h2>Riepilogo viaggi inseriti da $string</h2></td>
						</tr>
						<th>Giorno</th>
						<th>Da</th>
						<th>A</th>
						<th>Partenza</th>
						<th>Arrivo</th>
						<th>Compagnia</th>";
						
						$query="SELECT * FROM viewViaggiDiretti WHERE admin=$id";
						$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
							while ($row = mysql_fetch_row($result))
								{
									echo"
									<tr>
											<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
											<td align=\"center\" style=\"padding-right:10px\"><label> $row[4] </label></td>
											<td align=\"center\" style=\"padding-right:10px\"><label> $row[5] </label></td>
											<td align=\"center\" style=\"padding-right:10px\"><label> $row[6] </label></td>
											<td align=\"center\" style=\"padding-right:10px\"><label> $row[7] </label></td>
											<td align=\"center\" style=\"padding-right:10px\"><label> $row[14] </label></td>
									</tr>";
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