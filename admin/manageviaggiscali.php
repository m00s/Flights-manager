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
			
			
			
		}
		else
			include "error.php";
		?>
		</table>
	</body>
</html>


<?

		if(isset($_GET['option'])){
			if($_GET['option']="insert")
				if(isset($_GET['type'])){
					if(($_GET['type'])=='d') // Itinerario diretto
					{
						
					}
					else // Itinerario con scali
					{
						// $_GET[s] numero di scali
						$query="SELECT v,idViaggio, v.idVolo, v.giorno, c.nome FROM Viaggi v JOIN Compagnie c ON 
								(v.idCompagniaEsec = c.idCompagnia)";
						$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
						echo "<form method=\"POST\" action=\"managecheck.php?area=itinerari\" class=\"form\">";
						for ($i = 0; $i < $_GET[s]; ++$i) {
							echo "<tr>
									<td align=\"center\" style=\"padding-right:10px\"><label> $row[0] </label></td>
									<td align=\"center\" style=\"padding-right:10px\"><label> $row[1] </label></td>
									<td align=\"center\" style=\"padding-right:10px\"><label> $row[2] </label></td>
									<td align=\"center\" style=\"padding-right:10px\"><label> $row[3] </label></td>
									<td align=\"center\"><input type=\"submit\" value=\"Aggiorna\" class=\"button\"/></td>
								  </tr>";
						}
						echo "</form>";  
					}
				
				}
				else{
					
				}
			}
			else{
				
			}
		}
		else{
		
		}
		else
			include "error.php";
		
						
?>