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
				if(isset($_GET['Compagnia'])){
				}
		else
			include "error.php";
		
						
?>