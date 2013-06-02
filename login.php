<? ob_start(); ?>
<?php session_start(); ?>
<html>
<head>
	<title> 
		Airlines
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="component/style.css">
	</head>
</head>


<body link="red" alink="yellow" vlink="green">
	<?php
	//require "db_connection.php";
	if(isset($_GET['cmd']))
	{
		$cmd=$_GET['cmd'];
		switch($cmd)
		{
			case "out":		$_SESSION=array();
							session_destroy();
							header("Location: http://localhost:8888/login.php");
							break;
			case "nauth":	header("Location: http://localhost:8888/login.php?a=nauth");
							break;
		}
	}
	else
	{	
		if(isset($_SESSION['Admin']) | isset($_SESSION['Guest'])){
			echo "<h3>Prima effettua il <a href=\"login.php?cmd=out\">logout</a><br/></h3>";
		}
		else{
	?>
		<br />
		<br />
		<br />

		<div align="center" style="margin-top:120px">
			<form method="POST" action="component/check.php" class="form">
			<table cellpadding="1" style="border-right:1px solid #000000; border-bottom:2px solid #000000; padding:7px">
				<tr height="30px">
					<td align="center"><h2 class="tt">Autenticati
					<!-- BEGIN url --><a href="{URL}" class="postlink" target="_new">{DESCRIPTION}</a><!-- END url --></h2></td>
				</tr>
				<tr>
					<td>
						<table width="260" border="1" bordercolor="#6397D0" cellspacing="0" align="center" class="table">
						  <tr align="center">
							<td width="96" align="right" class="sm">
							<? if(isset($_GET['a'])){
								$alert=$_GET['cmd'];
								if($alert=='nauth')
									echo"(!)";}
								?>
							E-mail:</td>
							<td align="left"><input type="text" name="mail" id="mail" size="25" /></td>
						  </tr>
						  <tr align="center">
							<td align="right" class="sm">Password:</td>
							<td align="left"><input name="password" type="password" id="password" size="25" maxlength="8" /></td>
						  </tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" height="40px">
						<?	
						if(isset($_GET['e']))
						{
							if($_GET['e']=="ae")
								echo "<span class=\"error\">Errore autenticazione &nbsp &nbsp</span>";
						}
						?>
						<input type="submit" name="login" id="login" value="Accedi" class="button" />
					</td>
				</tr>
			</table>
			</form>
			<table cellpadding="1" style="border-right:1px solid #000000; border-bottom:2px solid #000000; padding:7px">
				<tr height="30px">
						<td align="center"><h2 class="tt">Registrazione</h2></td>
					</tr>
					<tr>
						<td>
							<p class="mm">Se non sei ancora registrato procedi ed in pochi passi avrai pieno accesso al sito</p>
						</td>
					</tr>
				<tr>
					<td align="center" height="40px">
						<a href="registration.php?cmd=log"><input type="submit" href name="reg" id="login" value="Registrati" class="button" /></a>
					</td>
				</tr>
			</table>
		</div>
		<?
		}}
		?>
</body>
</html>
<? ob_flush();?>