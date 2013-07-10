<?php session_start();?>
<html>
<head>
	<title> 
		Airlines 
	</title>
	<head>
		<link rel="stylesheet" type="text/css" href="\component\style.css">
	</head>
</head>

<body>


<div id="personale" align="center" style="background-color:#FF4030;">
<?php
	if(isset($_REQUEST["cmd"]))
		if($_REQUEST["cmd"]=="logout")
			{
				$_SESSION=array();
				session_destroy();
				header("Location:/basidati/~msartore/default.php");
			}
	if(isset($_SESSION["Privileges"])){
		echo "Benvenuto ".$_SESSION["email"] .", <a href=\"details.php?cmd=logout\" >Logout</a>";
	}
	else{
		header("Location:/basidati/~msartore/default.php");	
	}
?>
</div>

<div id="biglietti" align="left" style="background-color:orange;">
<?php
	$voloa=NULL;
if(isset($_REQUEST["idv"]))
{
	require "component/db_connection.php";
	$queryca="SELECT idViaggioDiretto FROM viaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idv]";
	$resultca=mysql_fetch_array(mysql_query($queryca,$conn));
	
	if($resultca)
	{
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idv],0);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idv],0);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idv],0);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idv],0);";
			mysql_query($query,$conn);
		}
	}
	else
	{
	$voloa=$_REQUEST["idv"];
	if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idv],1);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idv],1);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idv],1);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idv],1);";
			mysql_query($query,$conn);
		}	
	}
}

if(isset($_REQUEST["idva"]) & isset($_REQUEST["idvr"]))
{
	require "component/db_connection.php";
	
		$queryca="SELECT idViaggioDiretto FROM viaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idva]";
		$querycr="SELECT idViaggioDiretto FROM viaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idvr]";
		$resultca=mysql_query($queryca,$conn);
		$resultcr=mysql_query($querycr,$conn);
		$rowca=mysql_fetch_array($resultca);
		$rowcr=mysql_fetch_array($resultcr);
		
		if($rowca)
			$voloa="diretto";
		else
			$voloa="scali";
			
		if($rowcr)
			$volor="diretto";
		else
			$volor="scali";
		
		
	if($voloa=='diretto' & $volor=='diretto')
	{
	/*scala posti volo andata diretto*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],0);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],0);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],0);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],0);";
			mysql_query($query,$conn);
		}
		/*scala posti volo ritorno diretto*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],0);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],0);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],0);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],0);";
			mysql_query($query,$conn);
		}
					
		
	}
	if($voloa=='diretto' & $volor=='scali')
	{
		/*scala posti volo andata diretto*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],0);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],0);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],0);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],0);";
			mysql_query($query,$conn);
		}
		
		/*scala posti ritorno con scali*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],1);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],1);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],1);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],1);";
			mysql_query($query,$conn);
		}
		
	}
	
	
	if($voloa=='scali' & $volor=='diretto')
	{
		/*scala posti andata con scali*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],1);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],1);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],1);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],1);";
			mysql_query($query,$conn);
		}
		
		/*scala posti volo ritorno diretto*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],0);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],0);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],0);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],0);";
			mysql_query($query,$conn);
		}
	}
	if($voloa=='scali' & $volor=='scali')
	{
		/*scala posti andata con scali*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],1);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],1);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idva],1);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idva],1);";
			mysql_query($query,$conn);
		}
		
		/*scala posti ritorno con scali*/
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],1);";
			mysql_query($query,$conn);
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],1);";	
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0 && isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiPrima],1,$_REQUEST[idvr],1);";
			mysql_query($query,$conn);
		}
		else
		if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0 && isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]==0)
		{
			$query="CALL ScalaPosti($_REQUEST[bigliettiSeconda],0,$_REQUEST[idvr],1);";
			mysql_query($query,$conn);
		}
	}
}

if(isset($_REQUEST["idv"]) || (isset($_REQUEST["idva"])&&isset($_REQUEST["idvr"])))
{

	echo"<form method=\"POST\" action=\"pay.php\" class=\"form\">";
		if(isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0)
			{
			echo"<h4>Nome passeggeri Prima Classe Viaggio D'andata, Un bagaglio da 20KG già incluso, Selezionare Eventuali Da Aggiungere Massimo 2, Bagaglio A Mano Incluso</h4>";
			
				for($i=1;$i<$_REQUEST["bigliettiPrima"]+1;$i++)
				{/*ppa=PasseggeroPrimaAndata*/
					echo"<h4>Passeggero N°".$i."</h4>
						<label for=\"Nome\">Nome</label><input type=\"text\" name=\"ppanome".$i."\">
						<label for=\"Nome\">Cognome</label><input type=\"text\" name=\"ppacognome".$i."\">
						<label for=\"Nome\">Nascita</label><input type=\"text\" name=\"ppanascita".$i."\"value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" />
						<label for=\"Nome\">Sesso</label>
						<label form\"Maschio\">Maschio<input type=\"radio\" name=\"ppasesso".$i."\" value=\"M\"></label>
						<label form\"Femmina\">Femmina<input type=\"radio\" name=\"ppasesso".$i."\" value=\"F\"></label>
						<label form\"Adulto\">Adulto<input type=\"radio\" name=\"ppatipo".$i."\" value=\"adulto\" checked></label>
						<label form\"Bambino\">Bambino<input type=\"radio\" name=\"ppatipo".$i."\" value=\"bambino\"></label>
						<label for=\"Nome\">Email</label><input type=\"text\" name=\"ppaemail".$i."\">
						<label for=\"Bagagli\">Numero Bagagli <select name=\"ppabagagli".$i."\">
												<option>0</option>
												<option>1</option>
												<option>2</option>
											</select></label>";
						if(isset($_REQUEST["idv"]) && $voloa==NULL)
						{
							$queryb="SELECT b.peso,tb.prezzo FROM Bagagli b NATURAL JOIN TariffeBagagli tb 
									WHERE tb.idCompagnia=(SELECT idCompagniaEsec FROM ViaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idv])";
							echo"<label for=\"Bagagli\">Peso,Prezzo (kg,€)<select name=\"ppapesobagagli".$i."\">";
										$resultb=mysql_query($queryb,$conn);
										while($row=mysql_fetch_array($resultb))
										{
											echo"<option>$row[0],$row[1]</option>";
										}
									echo"</select></label>";
						}
				}
				echo"<input type=\"hidden\" name=\"bigliettiPrima\" value=\"$_REQUEST[bigliettiPrima]\">";
			}
			
			if(isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0)
			{
			echo"<h4>Nome passeggeri Seconda Classe Viaggio D'andata, Bagaglio A Mano Incluso, Selezionare Il Numero Di Bagagli In Stiva Massimo 3</h4>";
				for($i=1;$i<$_REQUEST["bigliettiSeconda"]+1;$i++)
				{/*psa=PasseggeroSecondaAndata*/
					echo"<h4>Passeggero N°".$i."</h4>
						<label for=\"Nome\">Nome</label><input type=\"text\" name=\"psanome".$i."\">
						<label for=\"Nome\">Cognome</label><input type=\"text\" name=\"psacognome".$i."\">
						<label for=\"Nome\">Nascita</label><input type=\"text\" name=\"psanascita".$i."\"value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" />
						<label for=\"Nome\">Sesso</label>
						<label form\"Maschio\">Maschio<input type=\"radio\" name=\"psasesso".$i."\" value=\"M\"></label>
						<label form\"Femmina\">Femmina<input type=\"radio\" name=\"psasesso".$i."\" value=\"F\"></label>
						<label form\"Adulto\">Adulto<input type=\"radio\" name=\"psatipo".$i."\" value=\"adulto\" checked></label>
						<label form\"Bambino\">Bambino<input type=\"radio\" name=\"psatipo".$i."\" value=\"bambino\"></label>
						<label for=\"Nome\">Email</label><input type=\"text\" name=\"psaemail".$i."\">
						<label for=\"Bagagli\">Numero Bagagli <select name=\"psabagagli".$i."\">
												<option>0</option>
												<option>1</option>
												<option>2</option>
												<option>3</option>
											</select></label>";
						if(isset($_REQUEST["idv"]) && $voloa==NULL)
						{
							$queryb="SELECT b.peso,tb.prezzo FROM Bagagli b NATURAL JOIN TariffeBagagli tb 
									WHERE tb.idCompagnia=(SELECT idCompagniaEsec FROM ViaggiDiretti WHERE idViaggioDiretto=$_REQUEST[idv])";
							echo"<label for=\"Bagagli\">Peso,Prezzo (kg,€)<select name=\"psapesobagagli".$i."\">";
										$resultb=mysql_query($queryb,$conn);
										while($row=mysql_fetch_array($resultb))
										{
											echo"<option>$row[0],$row[1]</option>";
										}
									echo"</select></label>";
						}
				}
				echo"<input type=\"hidden\" name=\"bigliettiSeconda\" value=\"$_REQUEST[bigliettiSeconda]\">";
			}
			if(isset($_REQUEST["idv"]))
				echo"<input type=\"hidden\" name=\"idv\" value=\"$_REQUEST[idv]\">";
			else
			{
				echo"<input type=\"hidden\" name=\"idva\"  value=\"$_REQUEST[idva]\">";
				echo"<input type=\"hidden\" name=\"idvr\"  value=\"$_REQUEST[idvr]\">";
			}
			if(isset($_REQUEST["offerte"]))
					echo"<input type=\"hidden\" name=\"offerte\" value=\"on\">";
					
			if((isset($_REQUEST["bigliettiSeconda"]) && $_REQUEST["bigliettiSeconda"]!=0) || (isset($_REQUEST["bigliettiPrima"]) && $_REQUEST["bigliettiPrima"]!=0))
			echo"<br><br><input type=\"submit\" value=\"Procedi Al Pagamento\">";
			
			echo"</form>";
}


?>
</div>


</body>
</html>