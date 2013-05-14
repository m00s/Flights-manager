<?php
function getByQuery($query)
	{
		$host="localhost"; 
		$user="root"; 
		$pwd= "root";
		//$user="msartore"; 
		//$pwd= "ND0yj5lV"; 
		$dbname="Airlines";
		//$dbname="msartore-ES";
		$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
		mysql_select_db($dbname);
		$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		$num_righe=mysql_num_rows($result);
		$record = mysql_fetch_assoc($result);
		return $record;
	}


function get_record($dato, $campo)
	{
		$host="localhost"; 
		$user="root"; 
		$pwd= "root";
		//$user="msartore"; 
		//$pwd= "ND0yj5lV"; 
		$dbname="Airlines";
		//$dbname="msartore-ES";
		$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
		mysql_select_db($dbname);
		$query="SELECT * FROM Clienti WHERE $campo=\"$dato\"";
		$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		$num_righe=mysql_num_rows($result);
		$record = mysql_fetch_assoc($result);
		return $record;
	}
	
function get_type($dato, $campo)
	{
		$host="localhost"; 
		$user="root"; 
		$pwd= "root";
		//$user="msartore"; 
		//$pwd= "ND0yj5lV"; 
		$dbname="MS-Airlines";
		//$dbname="msartore-ES";
		$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
		mysql_select_db($dbname);
		$query="SELECT * FROM Utenti WHERE $campo=\"$dato\"";
		$result = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
		$num_righe=mysql_num_rows($result);
		$record = mysql_fetch_assoc($result);
		return $record['tipo'];
	}
	
function insert_Ut($query)
{
	$host="localhost"; 
	$user="root"; 
	$pwd= "root";
	//$user="msartore"; 
	//$pwd= "ND0yj5lV"; 
	$dbname="Airlines";
	//$dbname="msartore-ES";
	$conn=mysql_connect($host, $user, $pwd) or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
	mysql_select_db($dbname);
	echo $query;
	$ins = mysql_query($query,$conn) or die("Query fallita" . mysql_error($conn));
}

function invert_data($data)
{
	if(strlen($data)>10)
	{
		$data_iniziale=substr($data,0,10);
		$ora=substr($data,11);
	}
	else
	{
		$data_iniziale=$data;
	}
	
	$data_iniziale=str_replace("/","-",	$data_iniziale);
	$vet=explode("-",$data_iniziale);
	$data_finale=$vet[2]."-".$vet[1]."-".$vet[0];
	return $data_finale;
}

function getDurata($start, $end)
{
   $part = explode(":", $start);
   $arr = explode(":", $end);

   $diff = mktime($arr[0], $arr[1], 0,1,1,2004) - mktime($part[0], $part[1],0,1,1,2004);

   $tempo = floor($diff / (60*60)).".".(($diff / 60) % 60)." h";

   return $tempo;
}

function echo_row($row)
{
	echo "<tr align=\"center\" onMouseover=\"this.bgColor='#FFFFFF'\"onMouseout=\"this.bgColor='#DDDDDD'\"><td>$row[0]</td>";
	echo "<td>$row[1]</td>";
	echo "<td>$row[2]</td>";
	echo "<td>$row[5]</td>";
	//echo "<td><a href=\"default.php?cmd=ss&id=$row[5]&d=$row[0]\">VEDI</a></td></tr>";
	echo "<td><a href=\"default.php?cmd=ss&id=$row[6]&d=$row[0]\">
			<img src=\"images/go.png\" width=\"20px\" height=\"20px\" alt=\"vedi\" /></a></td></tr>";
}

function populate_select($row)
{
	echo "<option>$row[0]</option>";
}
?>