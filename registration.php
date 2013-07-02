<?php ob_start(); ?>
<?php session_start();?>
<html>
	<title> 
		Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="component/style.css">
	</head>
	
	<script type="text/javascript">	
		function checkScript(){
			var nome = document.formreg.nome.value;
			var cog = document.formreg.cog.value;
			var nascita = document.formreg.nascita.value;
			var email = document.formreg.mail.value;
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			var pass = document.formreg.psw.value;
			var passconf = document.formreg.pswconf.value;
			var ok=true;
			if ((nome == "") || (nome == "undefined")){
				document.getElementById('nome').innerHTML = '<span class=\"error\">(!)</span> nome';
				ok=false;
			}
			
			else
				document.getElementById('nome').innerHTML = 'nome';
				
			if ((cog == "") || (cog == "undefined")){
				document.getElementById('cog').innerHTML = '<span class=\"error\">(!)</span> cognome';
				ok=false;
			}
			else
				document.getElementById('cog').innerHTML = 'cognome';
				
			if (nascita == "" || nascita =="(aaaa/mm/dd)" ||
				nascita.substring(4,5) != "/" ||
				nascita.substring(7,8) != "/" ||
				isNaN(nascita.substring(0,4)) ||
				isNaN(nascita.substring(5,7)) ||
				isNaN(nascita.substring(8,10))|| 
				nascita.substring(0,4) > 1995 ||
				nascita.substring(5,7) > 12 ||
				nascita.substring(8,10) > 31)  {
				document.getElementById('nascita').innerHTML = '<span class=\"error\">(!)</span> data di nascita';
				ok=false;
			}
			else
				document.getElementById('nascita').innerHTML = 'data di nascita';
				
			if (!filter.test(email)){
				document.getElementById('labmail').innerHTML = '<span class=\"error\">(!)</span> email';
				ok=false;
			}
			else
				document.getElementById('labmail').innerHTML = 'email';
				
			if (pass=="" || pass != passconf){
				document.getElementById('psw').innerHTML = '<span class=\"error\">(!)</span> password';
				ok=false;
			}
			else
				document.getElementById('psw').innerHTML = 'password';
				
			if(ok==true){
				document.formreg.action = "registration.php?cmd=submit";
				document.formreg.submit();
			}		
		}
	</script>
	<body>
<?php
	require "component/db_connection.php";
	if(isset($_GET['cmd']))
		{
			$cmd=$_GET['cmd'];
			switch($cmd)
			{
				case "log":	echo "<div align=\"center\" style=\"padding-top: 50px;\">
		  				<form name=\"formreg\" method=\"POST\" action=\"registration.php?cmd=submit\" class=\"form\">
						<table cellspacing=\"2\" cellpadding=\"7\" style=\"border-right:1px solid #000000; border-bottom:2px solid #000000; padding:7px\">
							<tr>
								<td align=\"center\"><h2 class=\"tt\">Registrazione</h2></td>
							</tr>
							<td>
							<table border=\"1\" bordercolor=\"#99FFFF\" cellspacing=\"0\" align=\"center\" class=\"table\" cellpadding=\"3\" >
								
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td><label id=\"nome\">nome</label></td>
									<td><input type=\"TEXT\" name=\"nome\"/></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td><label  id=\"cog\">cognome</label></td>
									<td><input type=\"TEXT\" name=\"cog\"/></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td><label id=\"nascita\">data di nascita</label></td>
									<td><input name=\"nascita\" type=\"TEXT\" value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" 
									onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" /></td>
								</tr>
								<tr width=\"96\">
									<td align=\"right\" class=\"sm\"><label>sesso</label></td>
									<td class=\"sm\"><input type=\"radio\" name=\"sex\" value=\"M\" checked/> M &nbsp
									<input type=\"radio\" name=\"sex\" value=\"F\" /> F</td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td><label id=\"labmail\">email</label></td>
									<td><input type=\"TEXT\" name=\"mail\"/></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td><label id=\"psw\">password</label></td>
									<td><input type=\"password\" name=\"psw\"/></td>
								</tr>
								<tr width=\"96\" align=\"right\" class=\"sm\">
									<td><label>conferma password</label></td>
									<td><input type=\"password\" name=\"pswconf\"/></td>
								</tr></td></table>
								<tr>
									<td align=\"right\"><input type=\"button\" value=\"Procedi\" onClick=\"checkScript()\" class=\"button\"/></td>
								</tr>
							</table>
						</form>
						</div>";
				break;
				case "submit":{
						$query="call InserisciUtente('$_POST[nome]','$_POST[cog]','$_POST[nascita]','$_POST[sex]','$_POST[mail]',sha1('$_POST[psw]'),'Guest', @x);";
						$ins = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));;
						$Qcontrol = "SELECT @x AS FLAG";
						$Rcontrol = mysql_query($Qcontrol,$conn) or die("Query fallita" . mysql_error($conn));;
						$test = mysql_fetch_assoc($Rcontrol);
						if($test['flag']=1){
							echo "<meta http-equiv=\"refresh\" content=\"5;url=http://localhost:8888/default.php\">";
							//header("Location: http://localhost:8888/default.php");
							echo "Registrazione effettuata con successo, verrai reindirizzato a breve..";
							$_SESSION['utente'] = $_POST['mail'];
						}
						else{
						}
				}
				break;
			}
		}	
?></body></html>
<?php ob_flush();?>
